<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // One application per candidate per job (enforced at DB level).
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $t) {
            $t->unique(['candidate_id', 'job_post_id'], 'applications_candidate_job_unique');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $t) {
            $t->dropUnique('applications_candidate_job_unique');
        });
    }
};