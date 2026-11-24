<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->foreignId('proposed_by')->nullable()->after('application_id')->constrained('users')->nullOnDelete();
            $table->unsignedInteger('pay_amount_fcfa')->nullable()->after('weekly_hours');
            $table->enum('payment_frequency', ['hourly', 'weekly', 'monthly', 'fixed'])->nullable()->after('pay_amount_fcfa');
            $table->text('schedule_details')->nullable()->after('payment_frequency');
            $table->string('workplace_address')->nullable()->after('schedule_details');
            $table->text('mission_description')->nullable()->after('workplace_address');
            $table->enum('teacher_confirmation', ['pending', 'accepted', 'declined'])->default('pending')->after('status');
            $table->timestamp('confirmed_at')->nullable()->after('teacher_confirmation');
        });
    }

    public function down(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropForeign(['proposed_by']);
            $table->dropColumn([
                'proposed_by', 'pay_amount_fcfa', 'payment_frequency', 'schedule_details',
                'workplace_address', 'mission_description', 'teacher_confirmation', 'confirmed_at',
            ]);
        });
    }
};