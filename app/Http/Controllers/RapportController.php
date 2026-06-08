<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\Produit;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RapportController extends Controller
{
    // ─── Rapport Ventes ───────────────────────────────────────
    public function ventes(Request $request)
    {
        $dateDebut = $request->date_debut ?? now()->startOfMonth()->format('Y-m-d');
        $dateFin   = $request->date_fin   ?? now()->format('Y-m-d');

        // CA par jour
        $ventesParJour = Vente::selectRaw('DATE(created_at) as date, SUM(total_ttc) as total, COUNT(*) as nombre')
            ->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
            ->where('statut', 'payee')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Top produits
        $topProduits = DB::table('vente_lignes')
            ->join('produits', 'produits.id', '=', 'vente_lignes.produit_id')
            ->join('ventes', 'ventes.id', '=', 'vente_lignes.vente_id')
            ->whereBetween('ventes.created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
            ->where('ventes.statut', 'payee')
            ->selectRaw('produits.nom, produits.reference,
                         SUM(vente_lignes.quantite) as total_vendu,
                         SUM(vente_lignes.sous_total) as total_ca')
            ->groupBy('produits.id', 'produits.nom', 'produits.reference')
            ->orderByDesc('total_vendu')
            ->limit(10)
            ->get();

        // Stats globales
        $stats = [
            'ca_total'      => Vente::whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
                                    ->where('statut', 'payee')->sum('total_ttc'),
            'nb_ventes'     => Vente::whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
                                    ->where('statut', 'payee')->count(),
            'nb_annulees'   => Vente::whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
                                    ->where('statut', 'annulee')->count(),
            'panier_moyen'  => Vente::whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
                                    ->where('statut', 'payee')->avg('total_ttc') ?? 0,
        ];

        // Répartition par mode de paiement
        $paiements = Vente::selectRaw('mode_paiement, COUNT(*) as nombre, SUM(total_ttc) as total')
            ->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
            ->where('statut', 'payee')
            ->groupBy('mode_paiement')
            ->get();

        return view('rapports.ventes', compact(
            'ventesParJour', 'topProduits', 'stats',
            'paiements', 'dateDebut', 'dateFin'
        ));
    }
public function caisse(Request $request)
{
    $mode = $request->mode ?? 'jour';

    // ─── Situation globale (tout le temps) ────────────────
    $situation = [
        'total_entrees'     => Vente::where('statut', 'payee')->sum('total_ttc'),
        'total_sorties'     => \App\Models\CommandeFournisseur::whereIn('statut', ['en_attente', 'recue'])->sum('total'),
        'total_annulees'    => Vente::where('statut', 'annulee')->sum('total_ttc'),
        'nb_ventes_total'   => Vente::where('statut', 'payee')->count(),
        'nb_achats_total'   => \App\Models\CommandeFournisseur::whereIn('statut', ['en_attente', 'recue'])->count(),
        'nb_annulees_total' => Vente::where('statut', 'annulee')->count(),
    ];
    $situation['solde_global'] = $situation['total_entrees'] - $situation['total_sorties'];

    // ─── Filtre selon le mode ─────────────────────────────
    if ($mode === 'periode') {
        $dateDebut = $request->date_debut ?? now()->startOfMonth()->format('Y-m-d');
        $dateFin   = $request->date_fin   ?? now()->format('Y-m-d');
        $date      = now()->format('Y-m-d');

        $ventes = Vente::with('client', 'vendeur')
            ->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
            ->where('statut', 'payee')
            ->get();

        $achats = \App\Models\CommandeFournisseur::with('fournisseur', 'lignes')
            ->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
            ->whereIn('statut', ['en_attente', 'recue'])
            ->get();

        $nbAnnulees = Vente::whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
                           ->where('statut', 'annulee')->count();
    } else {
        $date      = $request->date ?? now()->format('Y-m-d');
        $dateDebut = null;
        $dateFin   = null;

        $ventes = Vente::with('client', 'vendeur')
            ->whereDate('created_at', $date)
            ->where('statut', 'payee')
            ->get();

        $achats = \App\Models\CommandeFournisseur::with('fournisseur', 'lignes')
            ->whereDate('created_at', $date)
            ->whereIn('statut', ['en_attente', 'recue'])
            ->get();

        $nbAnnulees = Vente::whereDate('created_at', $date)
                           ->where('statut', 'annulee')->count();
    }

    $entrees = [
        'especes'      => $ventes->where('mode_paiement', 'especes')->sum('total_ttc'),
        'carte'        => $ventes->where('mode_paiement', 'carte')->sum('total_ttc'),
        'mobile_money' => $ventes->where('mode_paiement', 'mobile_money')->sum('total_ttc'),
        'credit'       => $ventes->where('mode_paiement', 'credit')->sum('total_ttc'),
        'total'        => $ventes->sum('total_ttc'),
        'nb_ventes'    => $ventes->count(),
        'annulees'     => $nbAnnulees,
    ];

    $sorties = [
        'total'        => $achats->sum('total'),
        'nb_commandes' => $achats->count(),
    ];

    $solde = $entrees['total'] - $sorties['total'];

    return view('rapports.caisse', compact(
        'ventes', 'achats', 'entrees', 'sorties',
        'solde', 'date', 'situation',
        'dateDebut', 'dateFin'
    ));
}
    // ─── Rapport Stock ────────────────────────────────────────
    public function stock()
    {
        $produits       = Produit::with('categorie', 'marque')->orderBy('stock_actuel')->get();
        $stockFaible    = Produit::stockFaible()->with('marque')->get();
        $enRupture      = Produit::enRupture()->with('marque')->get();
        $valeurStock    = Produit::all()->sum(fn($p) => $p->stock_actuel * $p->prix_achat);
        $valeurVente    = Produit::all()->sum(fn($p) => $p->stock_actuel * $p->prix_vente);

        return view('rapports.stock', compact(
            'produits', 'stockFaible', 'enRupture',
            'valeurStock', 'valeurVente'
        ));
    }

    // ─── Export Excel ─────────────────────────────────────────
    public function exportExcel(Request $request)
    {
        $dateDebut = $request->date_debut ?? now()->startOfMonth()->format('Y-m-d');
        $dateFin   = $request->date_fin   ?? now()->format('Y-m-d');

        $ventes = Vente::with('client', 'vendeur', 'lignes.produit')
            ->whereBetween('created_at', [$dateDebut . ' 00:00:00', $dateFin . ' 23:59:59'])
            ->where('statut', 'payee')
            ->get();

        // Génération CSV simple sans package
        $filename = "rapport-ventes-{$dateDebut}-{$dateFin}.csv";
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($ventes) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8

            // En-têtes
            fputcsv($file, [
                'N° Vente', 'Date', 'Client', 'Vendeur',
                'Mode Paiement', 'Total HT', 'Remise', 'Total TTC'
            ], ';');

            foreach ($ventes as $vente) {
                fputcsv($file, [
                    $vente->numero,
                    $vente->created_at->format('d/m/Y H:i'),
                    $vente->client ? $vente->client->nom . ' ' . $vente->client->prenom : 'Client passager',
                    $vente->vendeur->name,
                    $vente->mode_paiement,
                    $vente->total_ht,
                    $vente->remise,
                    $vente->total_ttc,
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}