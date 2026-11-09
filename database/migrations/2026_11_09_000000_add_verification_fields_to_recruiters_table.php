<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recruiters', function (Blueprint $table) {
            $table->json('verification_checks')->nullable();               // result of the automatic checks
            $table->text('verification_note')->nullable();                 // reason shown to the recruiter when rejected
            $table->string('verification_document')->nullable();           // private path of the uploaded certificate
            $table->timestamp('verification_submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            if (! Schema::hasColumn('recruiters', 'verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('recruiters', function (Blueprint $table) {
            $table->dropColumn(['verification_checks', 'verification_note', 'verification_document',
                                'verification_submitted_at', 'reviewed_at']);
        });
    }
};
