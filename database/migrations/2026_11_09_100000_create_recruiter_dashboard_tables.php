<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// NOTE: `jobs` is Laravel's queue table, so postings are called job_posts.
// Each table is skipped if your teammate already created it.
return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('job_posts')) {
            Schema::create('job_posts', function (Blueprint $t) {
                $t->id();
                $t->foreignId('recruiter_id')->constrained('recruiters')->cascadeOnDelete();
                $t->string('title');
                $t->enum('status', ['draft', 'open', 'closed'])->default('draft');
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('applications')) {
            Schema::create('applications', function (Blueprint $t) {
                $t->id();
                $t->foreignId('job_post_id')->constrained('job_posts')->cascadeOnDelete();
                $t->foreignId('candidate_id')->constrained('users')->cascadeOnDelete();
                $t->unsignedTinyInteger('match_score')->default(0);
                $t->enum('status', ['new', 'reviewed', 'shortlisted', 'interview', 'rejected', 'hired'])->default('new');
                $t->timestamps();
            });
        }
        if (! Schema::hasTable('interviews')) {
            Schema::create('interviews', function (Blueprint $t) {
                $t->id();
                $t->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
                $t->foreignId('recruiter_id')->constrained('recruiters')->cascadeOnDelete();
                $t->dateTime('scheduled_at');
                $t->string('round')->nullable();
                $t->string('mode')->nullable();
                $t->timestamps();
            });
        }
        Schema::create('focus_tasks', function (Blueprint $t) {
            $t->id();
            $t->foreignId('recruiter_id')->constrained('recruiters')->cascadeOnDelete();
            $t->string('title', 140);
            $t->string('note', 200)->nullable();
            $t->boolean('done')->default(false);
            $t->date('for_date');
            $t->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('focus_tasks');
    }
};
