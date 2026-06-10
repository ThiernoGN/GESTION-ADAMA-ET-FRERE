<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\MarqueController;
use App\Http\Controllers\CategorieController;
use App\Http\Controllers\ParametreController; // ← AJOUTER CETTE LIGNE




Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profil
    Route::get('/profile',    [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile',  [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Produits
    Route::resource('produits',   ProduitController::class);
    Route::resource('categories', CategorieController::class);
    Route::resource('marques',    MarqueController::class);

    // Clients
    Route::resource('clients', ClientController::class);

    // Ventes
    Route::resource('ventes', VenteController::class);
    Route::get('ventes/{vente}/facture',   [VenteController::class, 'facture'])->name('ventes.facture');
    Route::post('ventes/{vente}/annuler',  [VenteController::class, 'annuler'])->name('ventes.annuler');

    // Fournisseurs
    Route::resource('fournisseurs', FournisseurController::class);
    Route::post('fournisseurs/{fournisseur}/commande',
        [FournisseurController::class, 'commandeStore'])->name('fournisseurs.commande.store');
    Route::post('fournisseurs/{fournisseur}/commande/{commande}/recevoir',
        [FournisseurController::class, 'commandeRecevoir'])->name('fournisseurs.commande.recevoir');

    // Rapports (Admin)
    Route::middleware('role:admin')->prefix('rapports')->name('rapports.')->group(function () {
        Route::get('ventes',       [RapportController::class, 'ventes'])->name('ventes');
        Route::get('stock',        [RapportController::class, 'stock'])->name('stock');
        Route::get('caisse',       [RapportController::class, 'caisse'])->name('caisse');
        Route::get('export/excel', [RapportController::class, 'exportExcel'])->name('export.excel');
    });

    // ═══ PARAMÈTRES (Admin) ═══════════════════════════════
    Route::middleware('role:admin')->prefix('parametres')->name('parametres.')->group(function () {

        Route::get('/',        [ParametreController::class, 'index'])->name('index');

     
        // Utilisateurs
        Route::get('utilisateurs',                [ParametreController::class, 'utilisateurs'])->name('utilisateurs');
        Route::post('utilisateurs',               [ParametreController::class, 'utilisateurStore'])->name('utilisateurs.store');
        Route::put('utilisateurs/{user}',         [ParametreController::class, 'utilisateurUpdate'])->name('utilisateurs.update');
        Route::delete('utilisateurs/{user}',      [ParametreController::class, 'utilisateurDestroy'])->name('utilisateurs.destroy');
        Route::post('utilisateurs/{user}/toggle', [ParametreController::class, 'utilisateurToggle'])->name('utilisateurs.toggle');

        // Catégories
        Route::get('categories',                  [ParametreController::class, 'categories'])->name('categories');
        Route::post('categories',                 [ParametreController::class, 'categorieStore'])->name('categories.store');
        Route::put('categories/{categorie}',      [ParametreController::class, 'categorieUpdate'])->name('categories.update');
        Route::delete('categories/{categorie}',   [ParametreController::class, 'categorieDestroy'])->name('categories.destroy');

        // Marques
        Route::get('marques',                     [ParametreController::class, 'marques'])->name('marques');
        Route::post('marques',                    [ParametreController::class, 'marqueStore'])->name('marques.store');
        Route::put('marques/{marque}',            [ParametreController::class, 'marqueUpdate'])->name('marques.update');
        Route::delete('marques/{marque}',         [ParametreController::class, 'marqueDestroy'])->name('marques.destroy');

        // Boutique
        Route::get('boutique',                    [ParametreController::class, 'boutique'])->name('boutique');
        Route::put('boutique',                    [ParametreController::class, 'boutiqueUpdate'])->name('boutique.update');
    });

});

require __DIR__.'/auth.php';