<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            Schema::table('incidents', function (Blueprint $table) {
                $table->unsignedBigInteger('created_by')->nullable()->change();
            });

            return;
        }

        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable()->change();
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'sqlite') {
            // Leave nullable on sqlite rollback for simplicity.
            return;
        }

        DB::table('incidents')->whereNull('created_by')->delete();

        Schema::table('incidents', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->unsignedBigInteger('created_by')->nullable(false)->change();
        });

        Schema::table('incidents', function (Blueprint $table) {
            $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};
