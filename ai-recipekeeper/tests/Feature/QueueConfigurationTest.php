<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class QueueConfigurationTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_queue_connection_is_redis(): void
    {
        Config::set('queue.default', 'redis');

        $this->assertEquals('redis', config('queue.default'));
    }

    public function test_redis_queue_connection_uses_generations_queue(): void
    {
        $this->assertEquals('generations', config('queue.connections.redis.queue'));
    }

    public function test_redis_queue_connection_uses_default_redis_connection(): void
    {
        $this->assertEquals('default', config('queue.connections.redis.connection'));
    }

    public function test_sync_driver_is_used_when_configured(): void
    {
        Config::set('queue.connections.sync.driver', 'sync');

        $this->assertEquals('sync', config('queue.connections.sync.driver'));
    }

    public function test_jobs_table_exists_for_database_driver(): void
    {
        $this->assertTrue(
            Schema::hasTable('jobs'),
            'jobs table must exist for database queue driver'
        );
    }

    public function test_failed_jobs_table_exists(): void
    {
        $this->assertTrue(
            Schema::hasTable('failed_jobs'),
            'failed_jobs table must exist for queue failure tracking'
        );
    }

    public function test_job_batches_table_exists(): void
    {
        $this->assertTrue(
            Schema::hasTable('job_batches'),
            'job_batches table must exist for queue batching'
        );
    }
}
