<?php

namespace Tests\Feature;

use App\Services\DailyReportService;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class V15DailyReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_builds_for_each_branch_and_total(): void
    {
        $a = $this->branch('Markaz');
        $b = $this->branch('Chilonzor');
        $text = app(DailyReportService::class)->build([$a->id => 'Markaz', $b->id => 'Chilonzor'], CarbonImmutable::now());

        $this->assertStringContainsString('Markaz', $text);
        $this->assertStringContainsString('Chilonzor', $text);
        $this->assertStringContainsString('JAMI', $text);
        $this->assertStringContainsString('Qarzdorlar', $text);
    }

    public function test_send_without_bot_sends_nothing(): void
    {
        $this->assertSame(0, app(DailyReportService::class)->send());
    }
}
