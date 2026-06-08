<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // --- CATEGORIES ---
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // --- MARQUES ---
        Schema::create('marques', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('pays_origine')->nullable();
            $table->string('logo')->nullable();
            $table->timestamps();
        });

        // --- PRODUITS ---
        Schema::create('produits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_id')->constrained('categories')->cascadeOnDelete();
            $table->foreignId('marque_id')->constrained('marques')->cascadeOnDelete();
            $table->string('nom');
            $table->string('reference')->unique();
            $table->text('description')->nullable();
            $table->decimal('prix_achat', 10, 2);
            $table->decimal('prix_vente', 10, 2);
            $table->integer('stock_actuel')->default(0);
            $table->integer('stock_minimum')->default(5);
            $table->string('contenance')->nullable();
            $table->enum('genre', ['homme', 'femme', 'mixte'])->default('mixte');
            $table->string('image')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });

        // --- CLIENTS ---
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('prenom');
            $table->string('telephone')->nullable();
            $table->string('email')->nullable()->unique();
            $table->text('adresse')->nullable();
            $table->integer('points_fidelite')->default(0);
            $table->timestamps();
        });

        // --- FOURNISSEURS ---
        Schema::create('fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->text('adresse')->nullable();
            $table->timestamps();
        });

        // --- VENTES ---
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            $table->string('numero')->unique();
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('total_ht', 10, 2);
            $table->decimal('remise', 10, 2)->default(0);
            $table->decimal('total_ttc', 10, 2);
            $table->enum('mode_paiement', ['especes', 'carte', 'mobile_money', 'credit'])->default('especes');
            $table->enum('statut', ['en_cours', 'payee', 'annulee'])->default('payee');
            $table->timestamps();
        });

        // --- LIGNES DE VENTE ---
        Schema::create('vente_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vente_id')->constrained('ventes')->cascadeOnDelete();
            $table->foreignId('produit_id')->constrained('produits');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->decimal('sous_total', 10, 2);
            $table->timestamps();
        });

        // --- COMMANDES FOURNISSEURS ---
        Schema::create('commandes_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fournisseur_id')->constrained('fournisseurs');
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('statut', ['en_attente', 'recue', 'annulee'])->default('en_attente');
            $table->date('date_livraison_prevue')->nullable();
            $table->timestamps();
        });

        // --- LIGNES COMMANDES FOURNISSEURS ---
        Schema::create('commande_fournisseur_lignes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commande_fournisseur_id')->constrained('commandes_fournisseurs')->cascadeOnDelete();
            $table->foreignId('produit_id')->constrained('produits');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commande_fournisseur_lignes');
        Schema::dropIfExists('commandes_fournisseurs');
        Schema::dropIfExists('vente_lignes');
        Schema::dropIfExists('ventes');
        Schema::dropIfExists('fournisseurs');
        Schema::dropIfExists('clients');
        Schema::dropIfExists('produits');
        Schema::dropIfExists('marques');
        Schema::dropIfExists('categories');
    }
};
