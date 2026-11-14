<?php

namespace Tests\Feature;

use App\Services\HealthService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** v23: Redis'ga tayyorlik (Redis o'chiq bo'lsa hech narsa o'zgarmaydi). */
class V23RedisReadyTest extends TestCase
{
    use RefreshDatabase;

    public function test_failover_cache_store_and_redis_queue_are_configured(): void
    {
        $this->assertSame(['redis', 'file'], config('cache.stores.failover.stores'));
        $this->assertSame('redis', config('queue.connections.redis.driver'));
        $this->assertGreaterThanOrEqual(180, config('queue.connections.redis.retry_after'));
        $this->assertContains(config('database.redis.client'), ['predis', 'phpredis']);
    }

    public function test_health_has_no_redis_row_when_redis_is_not_used(): void
    {
        $keys = array_column(app(HealthService::class)->checks(), 'key');

        $this->assertNotContains('redis', $keys);
    }

    public function test_health_reports_redis_failure_without_crashing_when_redis_is_used_but_down(): void
    {
        config(['queue.default' => 'redis', 'database.redis.default.host' => '127.0.0.1', 'database.redis.default.port' => 1, 'database.redis.client' => 'phpredis']);

        $row = collect(app(HealthService::class)->checks())->firstWhere('key', 'redis');

        $this->assertNotNull($row);
        $this->assertSame('fail', $row['status']);
    }
}
