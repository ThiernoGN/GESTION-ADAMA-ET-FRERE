<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use App\Models\VenteLigne;
use App\Models\Produit;
use App\Models\Client;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;


class VenteController extends Controller
{
    // ─── Liste des ventes ─────────────────────────────────────
    public function index(Request $request)
    {
        $query = Vente::with('client', 'vendeur')->latest();

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('numero', 'like', "%{$search}%")
                  ->orWhereHas('client', function ($q2) use ($search) {
                      $q2->where('nom', 'like', "%{$search}%")
                         ->orWhere('prenom', 'like', "%{$search}%")
                         ->orWhere('telephone', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->statut) {
            $query->where('statut', $request->statut);
        }

        if ($request->mode_paiement) {
            $query->where('mode_paiement', $request->mode_paiement);
        }

        if ($request->date_debut) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }

        if ($request->date_fin) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        $ventes = $query->paginate(20)->withQueryString();

        return view('ventes.index', compact('ventes'));
    }

    // ─── Formulaire nouvelle vente (Point de vente) ───────────
    public function create()
    {
        $produits = Produit::actif()->with('categorie')->orderBy('nom')->get();
        $clients  = Client::orderBy('nom')->get();
        return view('ventes.create', compact('produits', 'clients'));
    }

public function store(Request $request)
{
    $request->validate([
        'lignes'                  => 'required|array|min:1',
        'lignes.*.produit_id'     => 'required|exists:produits,id',
        'lignes.*.quantite'       => 'required|integer|min:1',
        'mode_paiement'           => 'required|in:especes,carte,mobile_money,credit',
        'remise'                  => 'nullable|numeric|min:0',
        'client_id'               => 'nullable|exists:clients,id',
        'montant_paye'            => 'nullable|numeric|min:0',
    ]);

    $total_ht = 0;
    $lignes   = [];

    foreach ($request->lignes as $ligne) {
        $produit = Produit::findOrFail($ligne['produit_id']);

        if ($produit->stock_actuel < $ligne['quantite']) {
            return back()->withInput()
                         ->withErrors(["Stock insuffisant pour « {$produit->nom} »."]);
        }

        $sous_total = $produit->prix_vente * $ligne['quantite'];
        $total_ht  += $sous_total;

        $lignes[] = [
            'produit_id'    => $produit->id,
            'quantite'      => $ligne['quantite'],
            'prix_unitaire' => $produit->prix_vente,
            'sous_total'    => $sous_total,
        ];
    }

    $remise       = $request->remise ?? 0;
    $total_ttc    = $total_ht - $remise;
    $montant_paye = $request->montant_paye ?? 0;

    // ─── Reste à payer = Total TTC - Montant payé ────────
    // Si montant_paye >= total_ttc → reste = 0
    // Si montant_paye < total_ttc  → reste = différence
    $reste_a_payer = max(0, $total_ttc - $montant_paye);

    // ─── Statut selon paiement ────────────────────────────
    // Paiement complet → payee
    // Paiement partiel ou aucun → en_cours
    $statut = $reste_a_payer > 0 ? 'en_cours' : 'payee';

    $vente = Vente::create([
        'numero'        => Vente::genererNumero(),
        'client_id'     => $request->client_id,
        'user_id'       => auth()->id(),
        'total_ht'      => $total_ht,
        'remise'        => $remise,
        'total_ttc'     => $total_ttc,
        'montant_paye'  => $montant_paye,
        'reste_a_payer' => $reste_a_payer,
        'mode_paiement' => $request->mode_paiement,
        'statut'        => $statut,
    ]);

    foreach ($lignes as $ligne) {
        $vente->lignes()->create($ligne);
        Produit::find($ligne['produit_id'])
               ->decrement('stock_actuel', $ligne['quantite']);
    }

    // Points fidélité uniquement si paiement complet
    if ($vente->client_id && $statut === 'payee') {
        $points = intdiv((int) $total_ttc, 1000);
        if ($points > 0) {
            $vente->client->increment('points_fidelite', $points);
        }
    }

    return redirect()
        ->route('ventes.show', $vente)
        ->with('success', $statut === 'payee'
            ? "Vente {$vente->numero} enregistrée et payée !"
            : "Vente {$vente->numero} enregistrée — Reste à payer : " . number_format($reste_a_payer, 0, ',', ' ') . " GNF"
        );
}

    // ─── Détail d'une vente ───────────────────────────────────
    public function show(Vente $vente)
    {
        $vente->load('client', 'vendeur');
        return view('ventes.show', compact('vente'));
    }

    // ─── Formulaire modification ──────────────────────────────
    public function edit(Vente $vente)
    {
        if ($vente->statut === 'annulee') {
            return redirect()->route('ventes.index')
                             ->withErrors('Impossible de modifier une vente annulée.');
        }

        $vente->load('lignes.produit');
        $produits = Produit::actif()->orderBy('nom')->get();
        $clients  = Client::orderBy('nom')->get();

        return view('ventes.edit', compact('vente', 'produits', 'clients'));
    }

    // ─── Mettre à jour ────────────────────────────────────────
    public function update(Request $request, Vente $vente)
    {
        if ($vente->statut === 'annulee') {
            return redirect()->route('ventes.index')
                             ->withErrors('Impossible de modifier une vente annulée.');
        }

        $request->validate([
            'lignes'              => 'required|array|min:1',
            'lignes.*.produit_id' => 'required|exists:produits,id',
            'lignes.*.quantite'   => 'required|integer|min:1',
            'mode_paiement'       => 'required|in:especes,carte,mobile_money,credit',
            'remise'              => 'nullable|numeric|min:0',
            'client_id'           => 'nullable|exists:clients,id',
        ]);

        // Restaurer le stock des anciennes lignes
        foreach ($vente->lignes as $ancienneLigne) {
            Produit::find($ancienneLigne->produit_id)
                   ->increment('stock_actuel', $ancienneLigne->quantite);
        }
        $vente->lignes()->delete();

        $total_ht = 0;
        $lignes   = [];

        foreach ($request->lignes as $ligne) {
            $produit = Produit::findOrFail($ligne['produit_id']);

            if ($produit->stock_actuel < $ligne['quantite']) {
                return back()
                    ->withInput()
                    ->withErrors(["Stock insuffisant pour « {$produit->nom} »."]);
            }

            $sous_total = $produit->prix_vente * $ligne['quantite'];
            $total_ht  += $sous_total;

            $lignes[] = [
                'produit_id'    => $produit->id,
                'quantite'      => $ligne['quantite'],
                'prix_unitaire' => $produit->prix_vente,
                'sous_total'    => $sous_total,
            ];
        }

        $remise    = $request->remise ?? 0;
        $total_ttc = $total_ht - $remise;

        $vente->update([
            'client_id'     => $request->client_id,
            'total_ht'      => $total_ht,
            'remise'        => $remise,
            'total_ttc'     => $total_ttc,
            'mode_paiement' => $request->mode_paiement,
        ]);

        foreach ($lignes as $ligne) {
            $vente->lignes()->create($ligne);
            Produit::find($ligne['produit_id'])
                   ->decrement('stock_actuel', $ligne['quantite']);
        }

        return redirect()
            ->route('ventes.show', $vente)
            ->with('success', 'Vente mise à jour avec succès !');
    }

    // ─── Supprimer ────────────────────────────────────────────
    public function destroy(Vente $vente)
    {
        // Restaurer le stock avant suppression
        foreach ($vente->lignes as $ligne) {
            Produit::find($ligne->produit_id)
                   ->increment('stock_actuel', $ligne->quantite);
        }

        $vente->lignes()->delete();
        $vente->delete();

        return redirect()
            ->route('ventes.index')
            ->with('success', 'Vente supprimée avec succès.');
    }

    // ─── Générer la facture PDF ───────────────────────────────
    public function facture(Vente $vente)
    {
        $vente->load('client', 'vendeur');
        $pdf = Pdf::loadView('ventes.facture', compact('vente'))
                  ->setPaper('a4', 'portrait');
        return $pdf->stream("facture-{$vente->numero}.pdf");
    }

    // ─── Annuler une vente ────────────────────────────────────
    public function annuler(Vente $vente)
    {
        if ($vente->statut === 'annulee') {
            return back()->withErrors('Cette vente est déjà annulée.');
        }

        // Restaurer le stock
        foreach ($vente->lignes as $ligne) {
            Produit::find($ligne->produit_id)
                   ->increment('stock_actuel', $ligne->quantite);
        }

        $vente->update(['statut' => 'annulee']);

        return back()->with('success', "Vente {$vente->numero} annulée. Stock restauré.");
    }
}
