<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->decimal('montant_paye_2', 10, 2)->default(0)->after('reste_a_payer');
            $table->timestamp('date_paiement_1')->nullable()->after('montant_paye_2');
            $table->timestamp('date_paiement_2')->nullable()->after('date_paiement_1');
        });
    }

    public function down(): void
    {
        Schema::table('ventes', function (Blueprint $table) {
            $table->dropColumn(['montant_paye_2', 'date_paiement_1', 'date_paiement_2']);
        });
    }
};