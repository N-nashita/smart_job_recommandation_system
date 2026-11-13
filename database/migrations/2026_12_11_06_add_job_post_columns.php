<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('job_posts', function (Blueprint $t) {
            $columns = ['location', 'type', 'salary_min', 'salary_max', 'description', 'skills', 'published_at', 'is_suspicious'];
            foreach ($columns as $col) {
                if (! Schema::hasColumn('job_posts', $col)) {
                    match ($col) {
                        'location', 'type', 'description' => $t->string($col)->nullable(),
                        'salary_min', 'salary_max' => $t->integer($col)->nullable(),
                        'skills' => $t->json($col)->nullable(),
                        'published_at' => $t->timestamp($col)->nullable(),
                        'is_suspicious' => $t->boolean($col)->default(false),
                        default => null,
                    };
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('job_posts', function (Blueprint $t) {
            $t->dropColumn(['location', 'type', 'salary_min', 'salary_max', 'description', 'skills', 'published_at', 'is_suspicious']);
        });
    }
};