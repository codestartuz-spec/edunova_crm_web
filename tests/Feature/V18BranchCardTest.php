<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\BranchPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v18: filial kartochkasi (sozlamalar, SMS, obuna to'lovlari tarixi). */
class V18BranchCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_and_show_pages_open_and_edit_redirects_to_card(): void
    {
        $sadmin = $this->user(Role::SAdmin);
        $branch = $this->branch('Qarshi');

        $this->actingAs($sadmin)->get(route('branches.index'))->assertOk()->assertSee('Qarshi')->assertSee(route('branches.show', $branch), false);
        $this->actingAs($sadmin)->get(route('branches.show', $branch))->assertOk()->assertSee("To'lov qabul qilish", false);
        $this->actingAs($sadmin)->get(route('branches.edit', $branch))->assertRedirect(route('branches.show', $branch));
    }

    public function test_sadmin_records_payment_and_history_is_append_only(): void
    {
        $sadmin = $this->user(Role::SAdmin);
        $branch = $this->branch();

        $this->actingAs($sadmin)->post(route('branches.payments.store', $branch), ['amount' => 500000, 'method' => 'card', 'note' => 'Noyabr'])->assertRedirect();

        $p = BranchPayment::firstOrFail();
        $this->assertSame(500000, $p->amount);
        $this->assertSame('card', $p->method);
        $this->assertSame($sadmin->id, $p->created_by);

        $this->actingAs($sadmin)->get(route('branches.show', ['branch' => $branch, 'tab' => 'payments']))->assertOk()->assertSee('Noyabr')->assertSee('Karta');

        $this->actingAs($sadmin)->post(route('branches.payments.store', $branch), ['amount' => 0, 'method' => 'cash'])->assertSessionHasErrors('amount');
        $this->actingAs($sadmin)->post(route('branches.payments.store', $branch), ['amount' => 1000, 'method' => 'crypto'])->assertSessionHasErrors('method');
        $this->assertSame(1, BranchPayment::count());
    }

    public function test_only_sadmin_can_take_payments_director_does_not_see_payments(): void
    {
        $branch = $this->branch();
        $director = $this->user(Role::Director, null, ['branches.manage']);

        $this->actingAs($director)->post(route('branches.payments.store', $branch), ['amount' => 1000, 'method' => 'cash'])->assertForbidden();
        $this->actingAs($director)->get(route('branches.show', $branch))->assertOk()->assertDontSee("To'lov qabul qilish", false);
        $this->assertSame(0, BranchPayment::count());
    }

    public function test_branch_sms_settings_save_limit_and_eskiz(): void
    {
        $sadmin = $this->user(Role::SAdmin);
        $branch = $this->branch();

        $this->actingAs($sadmin)->put(route('branches.sms', $branch), ['sms_modem_daily_limit' => 40, 'eskiz_email' => 'f@e.uz', 'eskiz_password' => 'pw', 'eskiz_from' => '4546'])->assertRedirect();
        $this->assertSame(40, (int) $branch->fresh()->sms_modem_daily_limit);
        $this->assertTrue($branch->fresh()->hasOwnSmsAccount());

        $this->actingAs($sadmin)->put(route('branches.sms', $branch), ['sms_modem_daily_limit' => -1])->assertSessionHasErrors('sms_modem_daily_limit');
    }

    public function test_settings_update_does_not_touch_eskiz_account(): void
    {
        $sadmin = $this->user(Role::SAdmin);
        $branch = $this->branch('Eski nom');
        $branch->update(['eskiz_email' => 'f@e.uz', 'eskiz_password' => 'pw', 'eskiz_from' => '4546']);

        $this->actingAs($sadmin)->put(route('branches.update', $branch), ['name' => 'Yangi nom', 'phone' => '+998 90 111 2233'])->assertRedirect();

        $this->assertSame('Yangi nom', $branch->fresh()->name);
        $this->assertTrue($branch->fresh()->hasOwnSmsAccount());
    }
}
