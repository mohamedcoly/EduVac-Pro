<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->enum('teacher_response', ['pending', 'accepted', 'declined', 'reschedule_requested'])
                ->default('pending')->after('status');
            $table->text('teacher_response_note')->nullable()->after('teacher_response');
            $table->timestamp('responded_at')->nullable()->after('teacher_response_note');
        });
    }

    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $table) {
            $table->dropColumn(['teacher_response', 'teacher_response_note', 'responded_at']);
        });
    }
};