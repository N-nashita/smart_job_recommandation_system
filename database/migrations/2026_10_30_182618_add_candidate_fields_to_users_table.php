<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->unique();
            $table->string('category')->nullable();
            $table->string('experience', 30)->nullable();
            $table->string('degree', 50)->nullable();
            $table->string('institution')->nullable();
            $table->string('major')->nullable();
            $table->unsignedSmallInteger('grad_year')->nullable();
            $table->decimal('cgpa', 3, 2)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['phone']);
            $table->dropColumn([
                'phone',
                'category',
                'experience',
                'degree',
                'institution',
                'major',
                'grad_year',
                'cgpa',
            ]);
        });
    }
};