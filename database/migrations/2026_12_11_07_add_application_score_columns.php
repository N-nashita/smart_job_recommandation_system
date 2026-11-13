<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $t) {
            $columns = ['skills_score', 'experience_score', 'location_score', 'summary'];
            foreach ($columns as $col) {
                if (! Schema::hasColumn('applications', $col)) {
                    match ($col) {
                        'summary' => $t->text($col)->nullable(),
                        default => $t->unsignedTinyInteger($col)->default(0),
                    };
                }
            }
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $t) {
            $t->dropColumn(['skills_score', 'experience_score', 'location_score', 'summary']);
        });
    }
};