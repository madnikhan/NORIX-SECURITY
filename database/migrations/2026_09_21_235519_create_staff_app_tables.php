<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guards', function (Blueprint $table) {
            $table->string('password')->nullable()->after('email');
            $table->rememberToken()->after('password');
            $table->boolean('must_set_password')->default(true)->after('remember_token');
            $table->string('invite_token', 64)->nullable()->unique()->after('must_set_password');
            $table->timestamp('invite_sent_at')->nullable()->after('invite_token');
            $table->timestamp('last_login_at')->nullable()->after('invite_sent_at');
            $table->decimal('default_hourly_rate', 8, 2)->nullable()->after('availability_notes');
        });

        Schema::table('sites', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('address');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->unsignedInteger('geofence_radius_meters')->default(150)->after('longitude');
            $table->timestamp('geocoded_at')->nullable()->after('geofence_radius_meters');
        });

        Schema::create('attendance_punches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guard_id')->constrained()->cascadeOnDelete();
            $table->string('type'); // clock_in | clock_out
            $table->timestamp('punched_at');
            $table->decimal('lat', 10, 7)->nullable();
            $table->decimal('lng', 10, 7)->nullable();
            $table->decimal('accuracy_meters', 8, 2)->nullable();
            $table->decimal('distance_meters', 10, 2)->nullable();
            $table->boolean('within_geofence')->default(false);
            $table->json('device_meta')->nullable();
            $table->string('source')->default('staff_app');
            $table->timestamps();

            $table->index(['shift_id', 'type']);
            $table->index(['guard_id', 'punched_at']);
        });

        Schema::create('staff_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guard_id')->constrained()->cascadeOnDelete();
            $table->string('subject')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('staff_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_conversation_id')->constrained()->cascadeOnDelete();
            $table->nullableMorphs('sender');
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guard_id')->constrained()->cascadeOnDelete();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->text('reason')->nullable();
            $table->string('status')->default('pending'); // pending | approved | rejected
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->index(['guard_id', 'status']);
        });

        Schema::create('staff_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guard_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->text('body')->nullable();
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index(['guard_id', 'read_at']);
        });

        Schema::create('policy_acknowledgements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guard_id')->constrained()->cascadeOnDelete();
            $table->foreignId('policy_id')->constrained()->cascadeOnDelete();
            $table->timestamp('acknowledged_at');
            $table->timestamps();

            $table->unique(['guard_id', 'policy_id']);
        });

        Schema::create('shift_reminder_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_id')->constrained()->cascadeOnDelete();
            $table->foreignId('guard_id')->constrained()->cascadeOnDelete();
            $table->string('window'); // 2h | 30m
            $table->timestamp('sent_at');
            $table->timestamps();

            $table->unique(['shift_id', 'window']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_reminder_logs');
        Schema::dropIfExists('policy_acknowledgements');
        Schema::dropIfExists('staff_notifications');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('staff_messages');
        Schema::dropIfExists('staff_conversations');
        Schema::dropIfExists('attendance_punches');

        Schema::table('sites', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'geofence_radius_meters', 'geocoded_at']);
        });

        Schema::table('guards', function (Blueprint $table) {
            $table->dropColumn([
                'password',
                'remember_token',
                'must_set_password',
                'invite_token',
                'invite_sent_at',
                'last_login_at',
                'default_hourly_rate',
            ]);
        });
    }
};
