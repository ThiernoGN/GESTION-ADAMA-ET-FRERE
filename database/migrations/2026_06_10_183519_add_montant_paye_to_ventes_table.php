<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->decimal('montant_paye', 10, 2)->default(0)->after('total_ttc');
            $table->decimal('reste_a_payer', 10, 2)->default(0)->after('montant_paye');
        });
    }

    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn(['montant_paye', 'reste_a_payer']);
        });
    }
};