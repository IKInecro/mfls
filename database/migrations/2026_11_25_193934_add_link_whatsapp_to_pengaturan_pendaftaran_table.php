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
        Schema::table('pengaturan_pendaftaran', function (Blueprint $table) {
            $table->string('link_wa_grup', 500)->nullable()->after('timeline');
            $table->string('link_wa_saluran', 500)->nullable()->after('link_wa_grup');
            $table->string('link_wa_helpdesk', 500)->nullable()->after('link_wa_saluran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaturan_pendaftaran', function (Blueprint $table) {
            $table->dropColumn(['link_wa_grup', 'link_wa_saluran', 'link_wa_helpdesk']);
        });
    }
};
