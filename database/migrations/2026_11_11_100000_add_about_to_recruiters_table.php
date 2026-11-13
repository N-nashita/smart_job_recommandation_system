<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('recruiters', function (Blueprint $t) {
            if (! Schema::hasColumn('recruiters', 'about')) $t->text('about')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('recruiters', function (Blueprint $t) { $t->dropColumn('about'); });
    }
};
