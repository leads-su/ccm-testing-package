<?php

namespace ConsulConfigManager\Testing\Concerns;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

/**
 * Trait WithPermissionsMigrations
 * @package ConsulConfigManager\Testing\Concerns
 */
trait WithPermissionsMigrations
{
    /**
     * Create `permissions` table
     * @return void
     */
    private function createPermissionsTable(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 125);
            $table->string('guard_name', 125);
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });
    }

    /**
     * Create `roles` table
     * @return void
     */
    private function createRolesTable(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 125);
            $table->string('guard_name', 125);
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });
    }

    /**
     * Create `model_has_permissions` table
     * @return void
     */
    private function createModelHasPermissionsTable(): void
    {
        Schema::create('model_has_permissions', function (Blueprint $table) {
            $indexKey = 'permission_id';

            $table->unsignedBigInteger($indexKey);

            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(
                [
                    'model_id',
                    'model_type',
                ],
                'model_has_permissions_model_id_model_type_index'
            );

            $table->foreign($indexKey)
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->primary(
                [
                    $indexKey,
                    'model_id',
                    'model_type',
                ],
                'model_has_permissions_permission_model_type_primary'
            );
        });
    }

    /**
     * Create `model_has_roles` table
     * @return void
     */
    private function createModelHasRolesTable(): void
    {
        Schema::create('model_has_roles', function (Blueprint $table) {
            $indexKey = 'role_id';
            $table->unsignedBigInteger($indexKey);

            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->index(
                [
                    'model_id',
                    'model_type',
                ],
                'model_has_roles_model_id_model_type_index'
            );

            $table->foreign($indexKey)
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');
            $table->primary(
                [
                    $indexKey,
                    'model_id',
                    'model_type',
                ],
                'model_has_roles_role_model_type_primary'
            );
        });
    }

    /**
     * Create `role_has_permissions` table
     * @return void
     */
    private function createRoleHasPermissionsTable(): void
    {
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $permissionKey = 'permission_id';
            $roleKey = 'role_id';
            $table->unsignedBigInteger($permissionKey);
            $table->unsignedBigInteger($roleKey);

            $table->foreign($permissionKey)
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade');

            $table->foreign($roleKey)
                ->references('id')
                ->on('roles')
                ->onDelete('cascade');

            $table->primary([$permissionKey, $roleKey], 'role_has_permissions_permission_id_role_id_primary');
        });
    }

    /**
     * Create permissions specific tables
     * @return void
     */
    public function createPermissionsTables(): void
    {
        $this->createPermissionsTable();
        $this->createRolesTable();
        $this->createModelHasPermissionsTable();
        $this->createModelHasRolesTable();
        $this->createRoleHasPermissionsTable();
    }

    /**
     * Drop `permissions` table
     * @return void
     */
    private function dropPermissionsTable(): void
    {
        Schema::dropIfExists('permissions');
    }

    /**
     * Drop `roles` table
     * @return void
     */
    private function dropRolesTable(): void
    {
        Schema::dropIfExists('roles');
    }

    /**
     * Drop `model_has_permissions` table
     * @return void
     */
    private function dropModelHasPermissionsTable(): void
    {
        Schema::dropIfExists('model_has_permissions');
    }

    /**
     * Drop `model_has_roles` table
     * @return void
     */
    private function dropModelHasRolesTable(): void
    {
        Schema::dropIfExists('model_has_roles');
    }

    /**
     * Drop `role_has_permissions` table
     * @return void
     */
    private function dropRoleHasPermissionsTable(): void
    {
        Schema::dropIfExists('role_has_permissions');
    }

    /**
     * Drop permissions specific tables
     * @return void
     */
    public function dropPermissionsTables(): void
    {
        $this->dropPermissionsTable();
        $this->dropRolesTable();
        $this->dropModelHasPermissionsTable();
        $this->dropModelHasRolesTable();
        $this->dropRoleHasPermissionsTable();
    }
}
