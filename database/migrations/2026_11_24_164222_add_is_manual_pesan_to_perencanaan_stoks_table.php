<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('perencanaan_stoks', function (Blueprint $table) {
            $table->boolean('is_manual_pesan')->default(false)->after('status_pesan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('perencanaan_stoks', function (Blueprint $table) {
            $table->dropColumn('is_manual_pesan');
        });
    }
};
