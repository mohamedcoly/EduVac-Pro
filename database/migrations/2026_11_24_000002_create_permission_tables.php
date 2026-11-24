<?php

use Illuminate\\Database\\Migrations\\Migration;
use Illuminate\\Database\\Schema\\Blueprint;
use Illuminate\\Support\\Facades\\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $teams = config('permission.teams');
        $tableNames = config('permission.table_names');
        $columnNames = config('permission.column_names');

        $rolePivotKey = $columnNames['role_pivot_key'] ?? 'role_id';
        $permissionPivotKey = $columnNames['permission_pivot_key'] ?? 'permission_id';

        throw_if(empty($tableNames), new \\Exception('Permission table names are not configured. Publish the Spatie permission config before running migrations.'));

        Schema::create($tableNames['permissions'], function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tableNames['roles'], function (Blueprint $table) use ($teams, $columnNames): void {
            $table->bigIncrements('id');
            if ($teams || config('permission.testing')) {
                $teamKey = $columnNames['team_foreign_key'] ?? 'team_id';
                $table->unsignedBigInteger($teamKey)->nullable();
                $table->index($teamKey, 'roles_team_foreign_key_index');
                $table->unique([$teamKey, 'name', 'guard_name']);
            } else {
                $table->string('name');
                $table->string('guard_name');
                $table->timestamps();
                $table->unique(['name', 'guard_name']);
            }
        });

        Schema::create($tableNames['model_has_permissions'], function (Blueprint $table) use ($tableNames, $columnNames, $permissionPivotKey, $teams): void {
            $table->unsignedBigInteger($permissionPivotKey);
            $table->string('model_type');
            $table->unsignedBigInteger($columnNames['model_morph_key'] ?? 'model_id');
            $table->index([$columnNames['model_morph_key'] ?? 'model_id', 'model_type'], 'model_has_permissions_model_id_model_type_index');
            $table->foreign($permissionPivotKey)->references('id')->on($tableNames['permissions'])->cascadeOnDelete();
            if ($teams) {
                $teamKey = $columnNames['team_foreign_key'] ?? 'team_id';
                $table->unsignedBigInteger($teamKey);
                $table->index($teamKey, 'model_has_permissions_team_foreign_key_index');
                $table->primary([$teamKey, $permissionPivotKey, $columnNames['model_morph_key'] ?? 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
            } else {
                $table->primary([$permissionPivotKey, $columnNames['model_morph_key'] ?? 'model_id', 'model_type'], 'model_has_permissions_permission_model_type_primary');
            }
        });

        Schema::create($tableNames['model_has_roles'], function (Blueprint $table) use ($tableNames, $columnNames, $rolePivotKey, $teams): void {
            $table->unsignedBigInteger($rolePivotKey);
            $table->string('model_type');
            $modelKey = $columnNames['model_morph_key'] ?? 'model_id';
            $table->unsignedBigInteger($modelKey);
            $table->index([$modelKey, 'model_type'], 'model_has_roles_model_id_model_type_index');
            $table->foreign($rolePivotKey)->references('id')->on($tableNames['roles'])->cascadeOnDelete();
            if ($teams) {
                $teamKey = $columnNames['team_foreign_key'] ?? 'team_id';
                $table->unsignedBigInteger($teamKey);
                $table->index($teamKey, 'model_has_roles_team_foreign_key_index');
                $table->primary([$teamKey, $rolePivotKey, $modelKey, 'model_type'], 'model_has_roles_role_model_type_primary');
            } else {
                $table->primary([$rolePivotKey, $modelKey, 'model_type'], 'model_has_roles_role_model_type_primary');
            }
        });

        Schema::create($tableNames['role_has_permissions'], function (Blueprint $table) use ($tableNames, $permissionPivotKey, $rolePivotKey): void {
            $table->unsignedBigInteger($permissionPivotKey);
            $table->unsignedBigInteger($rolePivotKey);
            $table->foreign($permissionPivotKey)->references('id')->on($tableNames['permissions'])->cascadeOnDelete();
            $table->foreign($rolePivotKey)->references('id')->on($tableNames['roles'])->cascadeOnDelete();
            $table->primary([$permissionPivotKey, $rolePivotKey]);
        });
    }

    public function down(): void
    {
        $tableNames = config('permission.table_names');
        if (empty($tableNames)) {
            return;
        }
        Schema::dropIfExists($tableNames['role_has_permissions']);
        Schema::dropIfExists($tableNames['model_has_roles']);
        Schema::dropIfExists($tableNames['model_has_permissions']);
        Schema::dropIfExists($tableNames['roles']);
        Schema::dropIfExists($tableNames['permissions']);
    }
};
