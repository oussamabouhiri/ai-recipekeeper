<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('generation_ia', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('tokens_used');
            $table->string('job_id')->nullable()->after('status');
            $table->text('error_message')->nullable()->after('job_id');
            $table->timestamp('started_at')->nullable()->after('error_message');
            $table->timestamp('completed_at')->nullable()->after('started_at');
        });
    }

    public function down(): void
    {
        Schema::table('generation_ia', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'job_id',
                'error_message',
                'started_at',
                'completed_at',
            ]);
        });
    }
};
