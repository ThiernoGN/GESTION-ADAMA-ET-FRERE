<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Produit;
use App\Models\Client;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'ca_jour'       => Vente::whereDate('created_at', today())
                                    ->where('statut', 'payee')->sum('total_ttc'),
            'ca_mois'       => Vente::whereMonth('created_at', now()->month)
                                    ->where('statut', 'payee')->sum('total_ttc'),
            'total_clients' => Client::count(),
            'stock_faible'  => Produit::stockFaible()->count(),
        ];

        $ventes_semaine = Vente::selectRaw('DATE(created_at) as date, SUM(total_ttc) as total')
            ->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])
            ->where('statut', 'payee')
            ->groupBy('date')->orderBy('date')->get();

        $top_produits = DB::table('vente_lignes')
            ->join('produits', 'produits.id', '=', 'vente_lignes.produit_id')
            ->selectRaw('produits.nom, SUM(vente_lignes.quantite) as total_vendu')
            ->groupBy('produits.id', 'produits.nom')
            ->orderByDesc('total_vendu')->limit(5)->get();

        $alertes_stock    = Produit::stockFaible()->with('marque')->get();
        $dernieres_ventes = Vente::with('client', 'vendeur')->latest()->limit(10)->get();

        return view('dashboard', compact(
            'stats', 'ventes_semaine', 'top_produits',
            'alertes_stock', 'dernieres_ventes'
        ));
    }
}