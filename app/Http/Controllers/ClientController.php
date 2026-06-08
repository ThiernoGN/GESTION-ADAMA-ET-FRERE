<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    // ─── Liste ───────────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Client::withCount('ventes')
                       ->withSum('ventes', 'total_ttc')
                       ->latest();

        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom', 'like', "%{$s}%")
                  ->orWhere('prenom', 'like', "%{$s}%")
                  ->orWhere('telephone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        if ($request->fidelite === 'oui') {
            $query->where('points_fidelite', '>', 0);
        }

        $clients = $query->paginate(20)->withQueryString();

        return view('clients.index', compact('clients'));
    }

    // ─── Formulaire création ──────────────────────────────────
    public function create()
    {
        return view('clients.create');
    }

    // ─── Enregistrer ─────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20|unique:clients,telephone',
            'email'     => 'nullable|email|unique:clients,email',
            'adresse'   => 'nullable|string',
        ]);

        Client::create($request->all());

        return redirect()->route('clients.index')
                         ->with('success', 'Client ajouté avec succès !');
    }

    // ─── Détail ───────────────────────────────────────────────
    public function show(Client $client)
    {
        $client->load('ventes.lignes.produit');
        return view('clients.show', compact('client'));
    }

    // ─── Formulaire modification ──────────────────────────────
    public function edit(Client $client)
    {
        return view('clients.edit', compact('client'));
    }

    // ─── Mettre à jour ────────────────────────────────────────
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'nom'       => 'required|string|max:255',
            'prenom'    => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20|unique:clients,telephone,' . $client->id,
            'email'     => 'nullable|email|unique:clients,email,' . $client->id,
            'adresse'   => 'nullable|string',
        ]);

        $client->update($request->all());

        return redirect()->route('clients.show', $client)
                         ->with('success', 'Client mis à jour avec succès !');
    }

    // ─── Supprimer ────────────────────────────────────────────
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')
                         ->with('success', 'Client supprimé avec succès.');
    }
}
