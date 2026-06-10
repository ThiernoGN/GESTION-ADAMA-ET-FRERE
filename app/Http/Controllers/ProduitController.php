<?php

namespace App\Http\Controllers;

use App\Models\Produit;
use App\Models\Categorie;
use App\Models\Fournisseur;
use App\Models\CommandeFournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProduitController extends Controller
{
    public function index(Request $request)
    {
        $query = Produit::with('categorie', 'fournisseur')->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nom', 'like', "%{$request->search}%")
                  ->orWhere('reference', 'like', "%{$request->search}%");
            });
        }
        if ($request->categorie_id) {
            $query->where('categorie_id', $request->categorie_id);
        }
        if ($request->genre) {
            $query->where('genre', $request->genre);
        }
        if ($request->stock === 'faible') {
            $query->whereColumn('stock_actuel', '<=', 'stock_minimum');
        }
        if ($request->stock === 'rupture') {
            $query->where('stock_actuel', 0);
        }

        $produits    = $query->paginate(20)->withQueryString();
        $categories  = Categorie::orderBy('nom')->get();

        return view('produits.index', compact('produits', 'categories'));
    }

    public function create()
    {
        $categories   = Categorie::orderBy('nom')->get();
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        return view('produits.create', compact('categories', 'fournisseurs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'            => 'required|string|max:255',
            'reference'      => 'required|string|unique:produits,reference',
            'categorie_id'   => 'required|exists:categories,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'prix_achat'     => 'required|numeric|min:0',
            'prix_vente'     => 'required|numeric|min:0',
            'stock_actuel'   => 'required|integer|min:0',
            'stock_minimum'  => 'required|integer|min:0',
            'genre'          => 'required|in:homme,femme,mixte',
            'contenance'     => 'nullable|string|max:50',
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|max:2048',
        ]);

        // ─── Préparer les données ─────────────────────────
        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('produits', 'public');
        }

        // ─── Créer le produit ─────────────────────────────
        $produit = Produit::create($data);

        // ─── Créer commande fournisseur automatiquement ───
        // si un fournisseur est sélectionné ET stock initial > 0
        if ($request->fournisseur_id && $request->stock_actuel > 0) {
            $total = $request->prix_achat * $request->stock_actuel;

            $commande = CommandeFournisseur::create([
                'fournisseur_id' => $request->fournisseur_id,
                'user_id'        => auth()->id(),
                'total'          => $total,
                'statut'         => 'recue',
            ]);

            $commande->lignes()->create([
                'produit_id'    => $produit->id,
                'quantite'      => $request->stock_actuel,
                'prix_unitaire' => $request->prix_achat,
            ]);
        }

        return redirect()->route('produits.index')
                         ->with('success', 'Produit ajouté avec succès !');
    }

    public function show(Produit $produit)
    {
        $produit->load('categorie', 'fournisseur', 'venteLignes.vente');
        return view('produits.show', compact('produit'));
    }

    public function edit(Produit $produit)
    {
        $categories   = Categorie::orderBy('nom')->get();
        $fournisseurs = Fournisseur::orderBy('nom')->get();
        return view('produits.edit', compact('produit', 'categories', 'fournisseurs'));
    }

    public function update(Request $request, Produit $produit)
    {
        $request->validate([
            'nom'            => 'required|string|max:255',
            'reference'      => 'required|string|unique:produits,reference,' . $produit->id,
            'categorie_id'   => 'required|exists:categories,id',
            'fournisseur_id' => 'nullable|exists:fournisseurs,id',
            'prix_achat'     => 'required|numeric|min:0',
            'prix_vente'     => 'required|numeric|min:0',
            'stock_actuel'   => 'required|integer|min:0',
            'stock_minimum'  => 'required|integer|min:0',
            'genre'          => 'required|in:homme,femme,mixte',
            'contenance'     => 'nullable|string|max:50',
            'description'    => 'nullable|string',
            'image'          => 'nullable|image|max:2048',
        ]);

        // ─── Préparer les données ─────────────────────────
        $data = $request->except('image');

        if ($request->hasFile('image')) {
            if ($produit->image) {
                Storage::disk('public')->delete($produit->image);
            }
            $data['image'] = $request->file('image')->store('produits', 'public');
        }

        $produit->update($data);

        return redirect()->route('produits.index')
                         ->with('success', 'Produit mis à jour avec succès !');
    }

    public function destroy(Produit $produit)
    {
        if ($produit->image) {
            Storage::disk('public')->delete($produit->image);
        }
        $produit->delete();

        return redirect()->route('produits.index')
                         ->with('success', 'Produit supprimé avec succès.');
    }
}