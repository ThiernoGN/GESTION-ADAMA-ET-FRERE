<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\FournisseurController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MarqueController;
use App\Http\Controllers\CategorieController;

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profil (généré par Breeze — obligatoire)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Produits & Stock
    Route::resource('produits', ProduitController::class);
    Route::resource('categories', CategorieController::class);
    Route::resource('marques', MarqueController::class);

    // Clients
    Route::resource('clients', ClientController::class);

    // Ventes
    Route::resource('ventes', VenteController::class);
    Route::get('ventes/{vente}/facture', [VenteController::class, 'facture'])->name('ventes.facture');
    Route::post('ventes/{vente}/annuler', [VenteController::class, 'annuler'])->name('ventes.annuler');

    // Fournisseurs
    Route::resource('fournisseurs', FournisseurController::class);

    // Rapports (Admin seulement)
    Route::middleware('role:admin')->prefix('rapports')->name('rapports.')->group(function () {
        Route::get('ventes', [RapportController::class, 'ventes'])->name('ventes');
        Route::get('stock', [RapportController::class, 'stock'])->name('stock');
        Route::get('export/excel', [RapportController::class, 'exportExcel'])->name('export.excel');
    });

    // Commandes fournisseurs
        Route::post('fournisseurs/{fournisseur}/commande',
            [FournisseurController::class, 'commandeStore'])
            ->name('fournisseurs.commande.store');

        Route::post('fournisseurs/{fournisseur}/commande/{commande}/recevoir',
            [FournisseurController::class, 'commandeRecevoir'])
            ->name('fournisseurs.commande.recevoir');
            // Utilisateurs (Admin seulement)
            
            Route::middleware('role:admin')->prefix('rapports')->name('rapports.')->group(function () {
    Route::get('ventes',  [RapportController::class, 'ventes'])->name('ventes');
    Route::get('stock',   [RapportController::class, 'stock'])->name('stock');
    Route::get('caisse',  [RapportController::class, 'caisse'])->name('caisse');  // ← ajouter
    Route::get('export/excel', [RapportController::class, 'exportExcel'])->name('export.excel');
});
    Route::middleware('role:admin')->resource('utilisateurs', UserController::class);
});

require __DIR__.'/auth.php';