<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_jobs', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $t->foreignId('job_post_id')->constrained('job_posts')->cascadeOnDelete();
            $t->unique(['user_id', 'job_post_id'], 'saved_jobs_user_job_unique');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_jobs');
    }
};