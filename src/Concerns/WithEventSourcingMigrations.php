<?php

namespace ConsulConfigManager\Testing\Concerns;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

/**
 * Trait WithEventSourcingMigrations
 * @package ConsulConfigManager\Testing\Concerns
 */
trait WithEventSourcingMigrations
{
    /**
     * Create `snapshots` table
     * @return void
     */
    private function createSnapshotsTable(): void
    {
        Schema::create('snapshots', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('aggregate_uuid');
            $table->unsignedInteger('aggregate_version');
            $table->jsonb('state');

            $table->timestamps();

            $table->index('aggregate_uuid');
        });
    }

    /**
     * Create `stored_events` table
     * @return void
     */
    private function createStoredEventsTable(): void
    {
        Schema::create('stored_events', static function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->uuid('aggregate_uuid')->nullable();
            $table->unsignedBigInteger('aggregate_version')->nullable();
            $table->integer('event_version')->default(1);
            $table->string('event_class');
            $table->jsonb('event_properties');
            $table->jsonb('meta_data');
            $table->timestamp('created_at');
            $table->index('event_class');
            $table->index('aggregate_uuid');

            $table->unique(['aggregate_uuid', 'aggregate_version']);
        });
    }

    /**
     * Create event sourcing specific tables
     * @return void
     */
    public function createEventSourcingTables(): void
    {
        $this->createSnapshotsTable();
        $this->createStoredEventsTable();
    }

    /**
     * Drop `snapshots` table
     * @return void
     */
    private function dropSnapshotsTable(): void
    {
        Schema::dropIfExists('snapshots');
    }

    /**
     * Drop `stored_events` table
     * @return void
     */
    private function dropStoredEventsTable(): void
    {
        Schema::dropIfExists('stored_events');
    }

    /**
     * Drop event sourcing specific tables
     * @return void
     */
    public function dropEventSourcingTables(): void
    {
        $this->dropSnapshotsTable();
        $this->dropStoredEventsTable();
    }
}
