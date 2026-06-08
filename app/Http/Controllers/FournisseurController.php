<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\CommandeFournisseur;
use App\Models\Produit;
use Illuminate\Http\Request;

class FournisseurController extends Controller
{
    // ─── Liste ───────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Fournisseur::withCount('commandes')
                            ->withSum('commandes', 'total')
                            ->latest();

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom', 'like', "%{$s}%")
                  ->orWhere('telephone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        $fournisseurs = $query->paginate(20)->withQueryString();
        return view('fournisseurs.index', compact('fournisseurs'));
    }

    // ─── Formulaire création ──────────────────────────────────
    public function create()
    {
        return view('fournisseurs.create');
    }

    // ─── Enregistrer ─────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email'     => 'nullable|email',
            'adresse'   => 'nullable|string',
        ]);

        Fournisseur::create($request->all());

        return redirect()->route('fournisseurs.index')
                         ->with('success', 'Fournisseur ajouté avec succès !');
    }

    // ─── Détail ───────────────────────────────────────────────
    public function show(Fournisseur $fournisseur)
    {
        $fournisseur->load('commandes.lignes.produit');
        $produits = Produit::actif()->orderBy('nom')->get();
        return view('fournisseurs.show', compact('fournisseur', 'produits'));
    }

    // ─── Formulaire modification ──────────────────────────────
    public function edit(Fournisseur $fournisseur)
    {
        return view('fournisseurs.edit', compact('fournisseur'));
    }

    // ─── Mettre à jour ────────────────────────────────────────
    public function update(Request $request, Fournisseur $fournisseur)
    {
        $request->validate([
            'nom'       => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'email'     => 'nullable|email',
            'adresse'   => 'nullable|string',
        ]);

        $fournisseur->update($request->all());

        return redirect()->route('fournisseurs.show', $fournisseur)
                         ->with('success', 'Fournisseur mis à jour avec succès !');
    }

    // ─── Supprimer ────────────────────────────────────────────
    public function destroy(Fournisseur $fournisseur)
    {
        $fournisseur->delete();

        return redirect()->route('fournisseurs.index')
                         ->with('success', 'Fournisseur supprimé avec succès.');
    }

    // ─── Créer une commande fournisseur ───────────────────────
    public function commandeStore(Request $request, Fournisseur $fournisseur)
    {
        $request->validate([
            'lignes'                  => 'required|array|min:1',
            'lignes.*.produit_id'     => 'required|exists:produits,id',
            'lignes.*.quantite'       => 'required|integer|min:1',
            'lignes.*.prix_unitaire'  => 'required|numeric|min:0',
            'date_livraison_prevue'   => 'nullable|date',
        ]);

        $total = 0;
        foreach ($request->lignes as $ligne) {
            $total += $ligne['quantite'] * $ligne['prix_unitaire'];
        }

        $commande = $fournisseur->commandes()->create([
            'user_id'               => auth()->id(),
            'total'                 => $total,
            'statut'                => 'en_attente',
            'date_livraison_prevue' => $request->date_livraison_prevue,
        ]);

        foreach ($request->lignes as $ligne) {
            $commande->lignes()->create($ligne);
        }

        return redirect()->route('fournisseurs.show', $fournisseur)
                         ->with('success', 'Commande fournisseur créée avec succès !');
    }

    // ─── Recevoir une commande (met à jour le stock) ──────────
    public function commandeRecevoir(Fournisseur $fournisseur, CommandeFournisseur $commande)
    {
        if ($commande->statut === 'recue') {
            return back()->withErrors('Cette commande a déjà été reçue.');
        }

        foreach ($commande->lignes as $ligne) {
            Produit::find($ligne->produit_id)
                   ->increment('stock_actuel', $ligne->quantite);
        }

        $commande->update(['statut' => 'recue']);

        return back()->with('success', 'Commande reçue ! Stock mis à jour.');
    }
}
