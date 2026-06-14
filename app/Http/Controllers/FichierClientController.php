<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Vente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FichierClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        // Recherche par nom/téléphone
        if ($request->search) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('nom', 'like', "%{$s}%")
                  ->orWhere('prenom', 'like', "%{$s}%")
                  ->orWhere('telephone', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%");
            });
        }

        // Filtre par situation de paiement
        if ($request->situation === 'solde') {
            // Clients avec reste_a_payer > 0
            $query->whereHas('ventes', function ($q) {
                $q->where('reste_a_payer', '>', 0)
                  ->whereIn('statut', ['payee', 'en_cours']);
            });
        } elseif ($request->situation === 'solde') {
            $query->whereDoesntHave('ventes', function ($q) {
                $q->where('reste_a_payer', '>', 0);
            });
        }

        $clients = $query->orderBy('nom')->get();

        // Calcul des stats pour chaque client
        $clients = $clients->map(function ($client) {
            $ventes = $client->ventes()
                ->whereIn('statut', ['payee', 'en_cours'])
                ->get();

            $client->total_achats     = $ventes->sum('total_ttc');
            $client->total_paye       = $ventes->sum('montant_paye');
            $client->total_reste      = $ventes->sum('reste_a_payer');
            $client->nb_ventes        = $ventes->count();
            $client->nb_en_cours      = $ventes->where('statut', 'en_cours')->count();
            $client->derniere_vente   = $ventes->sortByDesc('created_at')->first();

            return $client;
        });

        // Filtre côté collection après calcul
        if ($request->situation === 'solde') {
            $clients = $clients->filter(fn($c) => $c->total_reste > 0);
        } elseif ($request->situation === 'solde_zero') {
            $clients = $clients->filter(fn($c) => $c->total_reste == 0);
        } elseif ($request->situation === 'actif') {
            $clients = $clients->filter(fn($c) => $c->nb_ventes > 0);
        }

        // Stats globales
        $stats = [
            'total_clients'   => $clients->count(),
            'total_achats'    => $clients->sum('total_achats'),
            'total_paye'      => $clients->sum('total_paye'),
            'total_reste'     => $clients->sum('total_reste'),
            'clients_solde'   => $clients->filter(fn($c) => $c->total_reste > 0)->count(),
        ];

        return view('fichier-client.index', compact('clients', 'stats'));
    }

    public function show(Request $request, Client $client)
    {
        $query = $client->ventes()
            ->with('lignes.produit', 'vendeur')
            ->whereIn('statut', ['payee', 'en_cours', 'annulee']);

        // Filtre par date
        if ($request->date_debut) {
            $query->whereDate('created_at', '>=', $request->date_debut);
        }
        if ($request->date_fin) {
            $query->whereDate('created_at', '<=', $request->date_fin);
        }

        // Filtre par statut paiement
        if ($request->paiement === 'solde') {
            $query->where('reste_a_payer', '>', 0);
        } elseif ($request->paiement === 'complet') {
            $query->where('reste_a_payer', 0);
        }

        $ventes = $query->latest()->get();

        // Calculs financiers
        $resume = [
            'total_achats'   => $ventes->whereIn('statut', ['payee','en_cours'])->sum('total_ttc'),
            'total_paye'     => $ventes->whereIn('statut', ['payee','en_cours'])->sum('montant_paye'),
            'total_reste'    => $ventes->whereIn('statut', ['payee','en_cours'])->sum('reste_a_payer'),
            'nb_ventes'      => $ventes->whereIn('statut', ['payee','en_cours'])->count(),
            'nb_annulees'    => $ventes->where('statut', 'annulee')->count(),
            'nb_avec_solde'  => $ventes->where('reste_a_payer', '>', 0)->count(),
        ];

        // Solde réel = total_reste (ce que le client doit encore)
        $resume['solde_reel'] = $resume['total_reste'];

        return view('fichier-client.show', compact('client', 'ventes', 'resume'));
    }
    public function solde(Request $request, Vente $vente)
{
    $request->validate([
        'montant_paye_2' => 'required|numeric|min:1',
    ]);

    if ($vente->reste_a_payer <= 0) {
        return back()->withErrors('Cette vente est déjà soldée.');
    }

    $montant_2     = $request->montant_paye_2;
    $nouveau_reste = max(0, $vente->reste_a_payer - $montant_2);
    $statut        = $nouveau_reste <= 0 ? 'payee' : 'en_cours';

    $vente->update([
        'montant_paye_2'  => $montant_2,
        'date_paiement_1' => $vente->date_paiement_1 ?? $vente->created_at,
        'date_paiement_2' => now(),
        'reste_a_payer'   => $nouveau_reste,
        'statut'          => $statut,
    ]);

    if ($statut === 'payee' && $vente->client_id) {
        $points = intdiv((int) $vente->total_ttc, 1000);
        if ($points > 0) {
            $vente->client->increment('points_fidelite', $points);
        }
    }

    return redirect()
        ->route('fichier-client.show', $vente->client)
        ->with('success', $statut === 'payee'
            ? "✅ Vente {$vente->numero} soldée !"
            : "Paiement enregistré. Reste : " . number_format($nouveau_reste, 0, ',', ' ') . " GNF"
        );
}
}
