<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('interviews', function (Blueprint $t) {
            if (! Schema::hasColumn('interviews', 'status')) {
                $t->string('status')->default('waiting');
            }
        });
    }

    public function down(): void
    {
        Schema::table('interviews', function (Blueprint $t) {
            if (Schema::hasColumn('interviews', 'status')) {
                $t->dropColumn('status');
            }
        });
    }
};