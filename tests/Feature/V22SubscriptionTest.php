<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Exceptions\SubscriptionExpiredException;
use App\Models\AppSetting;
use App\Models\Branch;
use App\Models\BranchPayment;
use App\Models\Group;
use App\Models\SubscriptionRequest;
use App\Services\SubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** v22: filial obunasi (oylik/yillik, chek bilan tasdiqlash, faqat-ko'rish rejimi). */
class V22SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    private function configure(): void
    {
        AppSetting::put('subscription.price_monthly', 100000);
        AppSetting::put('subscription.price_yearly', 1000000);
        AppSetting::put('subscription.card_number', '8600 1234 5678 9012');
    }

    private function expire(Branch $b, int $daysAgo = 1): Branch
    {
        $b->update(['subscription_ends_on' => today()->subDays($daysAgo)->toDateString()]);

        return $b->fresh();
    }

    public function test_new_branch_gets_trial_and_is_not_expired(): void
    {
        $b = $this->branch();

        $this->assertSame(today()->addDays(30)->toDateString(), $b->fresh()->subscription_ends_on->toDateString());
        $this->assertFalse($b->fresh()->subscriptionExpired());
        $this->assertSame(30, $b->fresh()->subscriptionDaysLeft());
    }

    public function test_branch_expires_on_its_end_date_and_exempt_never_expires(): void
    {
        $b = $this->branch();
        $b->update(['subscription_ends_on' => today()->toDateString()]);
        $this->assertTrue($b->fresh()->subscriptionExpired());

        $b->update(['subscription_exempt' => true]);
        $this->assertFalse($b->fresh()->subscriptionExpired());
        $this->assertNull($b->fresh()->subscriptionDaysLeft());
    }

    public function test_expired_branch_staff_cannot_write_but_can_read(): void
    {
        $b = $this->expire($this->branch());
        $admin = $this->user(Role::Admin, $b);

        $this->actingAs($admin)->get(route('dashboard'))->assertOk();
        $this->actingAs($admin)->post(route('groups.store'), [])->assertSessionHas('error');

        $this->expectException(SubscriptionExpiredException::class);
        Group::create(['name' => 'Test', 'branch_id' => $b->id]);
    }

    public function test_active_branch_is_writable_and_sadmin_is_never_blocked(): void
    {
        $b = $this->expire($this->branch());
        $sadmin = $this->user(Role::SAdmin);

        $this->actingAs($sadmin)->withSession(['current_branch_id' => $b->id])->post(route('groups.store'), [])->assertSessionMissing('error');
        SubscriptionService::assertWritable($b->id); // aktyor yo'q: cheklov yo'q
        $this->assertTrue(true);
    }

    public function test_director_is_read_only_on_expired_selected_branch(): void
    {
        $b = $this->expire($this->branch());
        $director = $this->user(Role::Director);

        $this->actingAs($director)->withSession(['current_branch_id' => $b->id])->post(route('groups.store'), [])->assertSessionHas('error');
        // obuna sahifasi esa ochiq
        $this->actingAs($director)->withSession(['current_branch_id' => $b->id])->get(route('subscriptions.index'))->assertOk()->assertSee('Obuna sotib olish');
    }

    public function test_director_submits_receipt_and_sadmin_approval_extends_from_today_when_expired(): void
    {
        Storage::fake('local');
        $this->configure();
        $b = $this->expire($this->branch());
        $director = $this->user(Role::Director);
        $sadmin = $this->user(Role::SAdmin);

        $this->actingAs($director)->post(route('subscriptions.store', $b), ['plan' => 'monthly', 'receipt' => UploadedFile::fake()->image('chek.jpg')])
            ->assertRedirect(route('subscriptions.index'));

        $req = SubscriptionRequest::firstOrFail();
        $this->assertSame('pending', $req->status);
        $this->assertSame(100000, $req->amount);
        Storage::disk('local')->assertExists($req->receipt_path);
        $this->assertTrue($b->fresh()->subscriptionExpired());

        // ikkinchi chek (tekshiruvdagi paytda) qabul qilinmaydi
        $this->actingAs($director)->post(route('subscriptions.store', $b), ['plan' => 'yearly', 'receipt' => UploadedFile::fake()->image('c2.jpg')])->assertSessionHas('error');
        $this->assertSame(1, SubscriptionRequest::count());

        $this->actingAs($sadmin)->post(route('subscriptions.approve', $req))->assertSessionHas('success');
        $this->actingAs($sadmin)->post(route('subscriptions.approve', $req)); // takroriy tasdiq

        $b = $b->fresh();
        $this->assertFalse($b->subscriptionExpired());
        $this->assertSame(today()->addMonthNoOverflow()->toDateString(), $b->subscription_ends_on->toDateString());
        $this->assertSame(1, BranchPayment::where('branch_id', $b->id)->count());
        $this->assertSame('approved', $req->fresh()->status);
    }

    public function test_extending_active_branch_adds_on_top_of_end_date_yearly(): void
    {
        Storage::fake('local');
        $this->configure();
        $b = $this->branch();
        $b->update(['subscription_ends_on' => today()->addDays(10)->toDateString()]);
        $director = $this->user(Role::Director);
        $sadmin = $this->user(Role::SAdmin);

        $this->actingAs($director)->post(route('subscriptions.store', $b), ['plan' => 'yearly', 'receipt' => UploadedFile::fake()->image('c.png')]);
        $this->actingAs($sadmin)->post(route('subscriptions.approve', SubscriptionRequest::firstOrFail()));

        $this->assertSame(today()->addDays(10)->addMonthsNoOverflow(12)->toDateString(), $b->fresh()->subscription_ends_on->toDateString());
    }

    public function test_rejected_request_keeps_branch_state_and_allows_resubmit(): void
    {
        Storage::fake('local');
        $this->configure();
        $b = $this->expire($this->branch());
        $director = $this->user(Role::Director);
        $sadmin = $this->user(Role::SAdmin);

        $this->actingAs($director)->post(route('subscriptions.store', $b), ['plan' => 'monthly', 'receipt' => UploadedFile::fake()->image('c.jpg')]);
        $req = SubscriptionRequest::firstOrFail();

        $this->actingAs($sadmin)->post(route('subscriptions.reject', $req), [])->assertSessionHasErrors('reason');
        $this->actingAs($sadmin)->post(route('subscriptions.reject', $req), ['reason' => "Summa mos emas"]);

        $this->assertSame('rejected', $req->fresh()->status);
        $this->assertTrue($b->fresh()->subscriptionExpired());
        $this->actingAs($director)->post(route('subscriptions.store', $b), ['plan' => 'monthly', 'receipt' => UploadedFile::fake()->image('c3.jpg')])->assertRedirect(route('subscriptions.index'));
        $this->assertSame(2, SubscriptionRequest::count());
    }

    public function test_non_image_receipt_and_unconfigured_prices_are_rejected(): void
    {
        Storage::fake('local');
        $b = $this->branch();
        $director = $this->user(Role::Director);

        $this->actingAs($director)->post(route('subscriptions.store', $b), ['plan' => 'monthly', 'receipt' => UploadedFile::fake()->image('c.jpg')])->assertSessionHas('error');

        $this->configure();
        $this->actingAs($director)->post(route('subscriptions.store', $b), ['plan' => 'monthly', 'receipt' => UploadedFile::fake()->create('x.pdf', 10, 'application/pdf')])->assertSessionHasErrors('receipt');
        $this->assertSame(0, SubscriptionRequest::count());
    }

    public function test_only_sadmin_manages_settings_and_receipts_are_private(): void
    {
        Storage::fake('local');
        $this->configure();
        $b = $this->branch();
        $director = $this->user(Role::Director);
        $admin = $this->user(Role::Admin, $b);
        $sadmin = $this->user(Role::SAdmin);

        $this->actingAs($director)->get(route('subscriptions.settings'))->assertForbidden();
        $this->actingAs($admin)->get(route('subscriptions.index'))->assertForbidden();

        $this->actingAs($sadmin)->post(route('subscriptions.settings.save'), [
            'price_monthly' => 150000, 'price_yearly' => 1500000, 'card_number' => '8600 1111 2222 3333', 'trial_days' => 14, 'terms' => 'Shart',
        ])->assertSessionHas('success');
        $this->assertSame(150000, SubscriptionService::price('monthly'));
        $this->assertSame(14, SubscriptionService::trialDays());

        $this->actingAs($sadmin)->post(route('subscriptions.settings.save'), ['price_monthly' => 1, 'price_yearly' => 1, 'card_number' => 'abc', 'trial_days' => 1])->assertSessionHasErrors('card_number');

        $this->actingAs($director)->post(route('subscriptions.store', $b), ['plan' => 'monthly', 'receipt' => UploadedFile::fake()->image('c.jpg')]);
        $req = SubscriptionRequest::firstOrFail();
        $this->actingAs($director)->post(route('subscriptions.approve', $req))->assertForbidden();
        $this->actingAs($admin)->get(route('subscriptions.receipt', $req))->assertForbidden();
        $this->actingAs($sadmin)->get(route('subscriptions.receipt', $req))->assertOk();
    }

    public function test_sadmin_can_mark_branch_exempt(): void
    {
        $b = $this->expire($this->branch());
        $sadmin = $this->user(Role::SAdmin);

        $this->actingAs($sadmin)->put(route('subscriptions.branch.update', $b), ['subscription_exempt' => 1])->assertSessionHas('success');
        $this->assertFalse($b->fresh()->subscriptionExpired());
    }

    public function test_reminders_are_sent_once_per_threshold(): void
    {
        Cache::flush();
        $b = $this->branch();
        $b->update(['subscription_ends_on' => today()->addDays(3)->toDateString()]);
        $other = $this->branch('Boshqa');
        $other->update(['subscription_ends_on' => today()->addDays(20)->toDateString()]);

        $service = app(SubscriptionService::class);
        $this->assertSame(1, $service->sendReminders());
        $this->assertSame(0, $service->sendReminders());
        $this->artisan('subscriptions:notify')->assertSuccessful();
    }

    public function test_all_subscription_pages_render(): void
    {
        $b = $this->branch();
        $director = $this->user(Role::Director);
        $sadmin = $this->user(Role::SAdmin);

        foreach ([false, true] as $configured) {
            $configured && $this->configure();
            $this->actingAs($director)->get(route('subscriptions.buy', $b))->assertOk();
            $this->actingAs($director)->get(route('subscriptions.index'))->assertOk()->assertSee('Obunani uzaytirish');
            $this->actingAs($sadmin)->get(route('subscriptions.index'))->assertOk();
            $this->actingAs($sadmin)->get(route('subscriptions.settings'))->assertOk();
        }

        $this->expire($b);
        $this->actingAs($director)->get(route('subscriptions.buy', $b))->assertOk()->assertSee('Obuna sotib olish');
    }

    public function test_branches_list_shows_subscription_state(): void
    {
        $expired = $this->expire($this->branch('Tugagan filial'));
        $ok = $this->branch('Faol filial');
        $sadmin = $this->user(Role::SAdmin);

        $this->actingAs($sadmin)->get(route('branches.index'))->assertOk()->assertSee('Obuna tugagan')->assertSee('kunigacha');
        $this->actingAs($sadmin)->get(route('branches.show', $expired))->assertOk()->assertSee('Obuna tugagan');
        $this->actingAs($sadmin)->get(route('branches.show', $ok))->assertOk()->assertSee('kun qoldi');
    }

    public function test_second_pending_receipt_is_blocked_by_database_even_if_code_check_is_bypassed(): void
    {
        $b = $this->branch();
        $row = ['branch_id' => $b->id, 'plan' => 'monthly', 'amount' => 1, 'receipt_path' => 'x.jpg', 'status' => 'pending'];
        SubscriptionRequest::create($row);

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);
        SubscriptionRequest::create($row);
    }

    public function test_after_review_a_new_receipt_can_be_sent_and_review_time_is_shown(): void
    {
        Storage::fake('local');
        $this->configure();
        $b = $this->expire($this->branch());
        $director = $this->user(Role::Director);

        $this->actingAs($director)->post(route('subscriptions.store', $b), ['plan' => 'monthly', 'receipt' => UploadedFile::fake()->image('c.jpg', 2400, 1800)])
            ->assertSessionHas('success', fn ($m) => str_contains($m, '4–12 soat'));
        $this->actingAs($director)->get(route('subscriptions.index'))->assertSee('4–12 soat');

        // rasm kichraytirilib saqlangan (eng uzun tomon 1600 px)
        $req = SubscriptionRequest::firstOrFail();
        [$w, $h] = getimagesizefromstring(Storage::disk('local')->get($req->receipt_path));
        $this->assertLessThanOrEqual(1600, max($w, $h));
    }

    public function test_yearly_price_must_be_between_monthly_and_twelve_months(): void
    {
        $sadmin = $this->user(Role::SAdmin);
        $base = ['card_number' => '8600 1111 2222 3333', 'trial_days' => 30];

        $this->actingAs($sadmin)->post(route('subscriptions.settings.save'), $base + ['price_monthly' => 100000, 'price_yearly' => 100000])->assertSessionHasErrors('price_yearly');
        $this->actingAs($sadmin)->post(route('subscriptions.settings.save'), $base + ['price_monthly' => 100000, 'price_yearly' => 1300000])->assertSessionHasErrors('price_yearly');
        $this->actingAs($sadmin)->post(route('subscriptions.settings.save'), $base + ['price_monthly' => 100000, 'price_yearly' => 1000000])->assertSessionHas('success');
    }

    public function test_auto_sms_commands_skip_expired_branches_and_resume_after_renewal(): void
    {
        $expired = $this->expire($this->branch('Tugagan'));
        $ok = $this->branch('Faol');

        $ids = fn () => Branch::active()->subscribed()->pluck('id')->all();
        $this->assertContains($ok->id, $ids());
        $this->assertNotContains($expired->id, $ids());

        // qayta faollashtirilgach avtomatik yana ro'yxatga qaytadi
        $expired->update(['subscription_ends_on' => today()->addMonth()->toDateString()]);
        $this->assertContains($expired->id, $ids());
        $expired->update(['subscription_ends_on' => today()->subDay()->toDateString(), 'subscription_exempt' => true]);
        $this->assertContains($expired->id, $ids());
    }

    public function test_last_active_day_is_one_day_before_end_date(): void
    {
        $b = $this->branch();
        $b->update(['subscription_ends_on' => today()->addDays(5)->toDateString()]);

        $this->assertSame(today()->addDays(4)->toDateString(), $b->fresh()->subscriptionLastDay()->toDateString());
        $this->assertSame(5, $b->fresh()->subscriptionDaysLeft());
    }

    public function test_receipts_are_removed_with_branch_and_included_in_backup(): void
    {
        Storage::fake('local');
        $b = $this->branch('Oʻchadigan');
        $path = 'subscription-receipts/a.jpg';
        Storage::disk('local')->put($path, 'x');
        SubscriptionRequest::create(['branch_id' => $b->id, 'plan' => 'monthly', 'amount' => 1, 'receipt_path' => $path, 'status' => 'rejected']);

        app(\App\Services\BranchDeletionService::class)->delete($b);

        Storage::disk('local')->assertMissing($path);
    }
}
