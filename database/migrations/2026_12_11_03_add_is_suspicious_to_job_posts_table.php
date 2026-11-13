<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Placeholder column for the fake-job detector (wired up later).
    public function up(): void
    {
        if (! Schema::hasColumn('job_posts', 'is_suspicious')) {
            Schema::table('job_posts', function (Blueprint $t) {
                $t->boolean('is_suspicious')->default(false)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('job_posts', 'is_suspicious')) {
            Schema::table('job_posts', function (Blueprint $t) {
                $t->dropColumn('is_suspicious');
            });
        }
    }
};