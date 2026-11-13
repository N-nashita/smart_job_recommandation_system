<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->text('summary')->nullable();
            $t->string('location')->nullable();
            $t->string('linkedin')->nullable();
            $t->string('github')->nullable();
            $t->string('portfolio')->nullable();
            $t->string('languages')->nullable();
            $t->text('certifications')->nullable();
            $t->json('skills')->nullable();
            $t->json('experiences')->nullable();
            $t->json('projects')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $t) {
            $t->dropColumn(['summary','location','linkedin','github','portfolio','languages','certifications','skills','experiences','projects']);
        });
    }
};
