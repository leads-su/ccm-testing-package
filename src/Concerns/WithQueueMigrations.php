<?php

namespace ConsulConfigManager\Testing\Concerns;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

/**
 * Trait WithQueueMigrations
 * @package ConsulConfigManager\Testing\Concerns
 */
trait WithQueueMigrations
{
    /**
     * Create `jobs` table
     * @return void
     */
    private function createJobsTable(): void
    {
        Schema::create('jobs', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
    }

    /**
     * Create `failed_jobs` table
     * @return void
     */
    private function createFailedJobsTable(): void
    {
        Schema::create('failed_jobs', static function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
    }

    /**
     * Create queue specific tables
     * @return void
     */
    public function createQueueTables(): void
    {
        $this->createJobsTable();
        $this->createFailedJobsTable();
    }

    /**
     * Drop `jobs` table
     * @return void
     */
    private function dropJobsTable(): void
    {
        Schema::dropIfExists('jobs');
    }

    /**
     * Drop `failed_jobs` table
     * @return void
     */
    private function dropFailedJobsTable(): void
    {
        Schema::dropIfExists('failed_jobs');
    }

    /**
     * Drop queue specific tables
     * @return void
     */
    public function dropQueueTables(): void
    {
        $this->dropJobsTable();
        $this->dropFailedJobsTable();
    }
}
