@extends('layouts.app')
@section('title', $client->nom_complet)

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('clients.index') }}" class="text-stone-400 hover:text-stone-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 font-bold text-xl">
                    {{ strtoupper(substr($client->nom, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-2xl font-semibold text-stone-800">{{ $client->nom_complet }}</h2>
                    <p class="text-sm text-stone-500">Client depuis {{ $client->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('clients.edit', $client) }}"
               class="inline-flex items-center gap-2 border border-amber-300 text-amber-700 hover:bg-amber-50 px-3 py-2 rounded-lg text-sm transition">
                Modifier
            </a>
            @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('clients.destroy', $client) }}"
                  onsubmit="return confirm('Supprimer ce client ?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="border border-red-200 text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg text-sm transition">
                    Supprimer
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        {{-- Stats --}}
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Total achats</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">
                {{ number_format($client->total_achats, 0, ',', ' ') }} GNF
            </p>
        </div>
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Nombre de ventes</p>
            <p class="text-2xl font-bold text-stone-800 mt-1">{{ $client->nombre_ventes }}</p>
        </div>
        <div class="bg-white border border-amber-200 bg-amber-50 rounded-xl p-5">
            <p class="text-xs text-amber-600 uppercase tracking-wide">Points fidélité</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">⭐ {{ $client->points_fidelite }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

        {{-- Infos --}}
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <h3 class="font-medium text-stone-800 mb-4">Coordonnées</h3>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-stone-500">Nom complet</dt>
                    <dd class="font-medium text-stone-800">{{ $client->nom_complet }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-stone-500">Téléphone</dt>
                    <dd class="font-medium text-stone-800">{{ $client->telephone ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-stone-500">Email</dt>
                    <dd class="font-medium text-stone-800">{{ $client->email ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-stone-500">Adresse</dt>
                    <dd class="font-medium text-stone-800">{{ $client->adresse ?? '—' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-stone-500">Dernière visite</dt>
                    <dd class="font-medium text-stone-800">
                        {{ $client->derniere_visite?->format('d/m/Y') ?? '—' }}
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Nouvelle vente rapide --}}
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <h3 class="font-medium text-stone-800 mb-4">Actions rapides</h3>
            <div class="space-y-3">
                <a href="{{ route('ventes.create') }}?client_id={{ $client->id }}"
                   class="flex items-center gap-3 p-3 border border-stone-200 rounded-lg hover:bg-amber-50 hover:border-amber-300 transition">
                    <span class="text-2xl">🛒</span>
                    <div>
                        <p class="text-sm font-medium text-stone-800">Nouvelle vente</p>
                        <p class="text-xs text-stone-400">Créer une vente pour ce client</p>
                    </div>
                </a>
                <div class="flex items-center gap-3 p-3 border border-stone-200 rounded-lg bg-amber-50">
                    <span class="text-2xl">⭐</span>
                    <div>
                        <p class="text-sm font-medium text-stone-800">{{ $client->points_fidelite }} points</p>
                        <p class="text-xs text-stone-400">≈ {{ number_format($client->points_fidelite * 1000, 0, ',', ' ') }} GNF d'achats cumulés</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Historique ventes --}}
    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
        <div class="p-4 border-b border-stone-100">
            <h3 class="font-medium text-stone-800">
                Historique des ventes
                <span class="text-xs font-normal text-stone-400 ml-2">({{ $client->ventes->count() }} ventes)</span>
            </h3>
        </div>

        @if($client->ventes->isEmpty())
        <div class="p-8 text-center text-stone-400">
            <p class="text-sm">Aucune vente enregistrée pour ce client</p>
        </div>
        @else
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-stone-50 border-b border-stone-100">
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">N° Vente</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Produits</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Paiement</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Total</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Statut</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach($client->ventes->sortByDesc('created_at') as $vente)
                <tr class="hover:bg-stone-50 transition">
                    <td class="px-4 py-3">
                        <a href="{{ route('ventes.show', $vente) }}"
                           class="font-medium text-amber-600 hover:underline">
                            {{ $vente->numero }}
                        </a>
                    </td>
                    <td class="px-4 py-3 text-stone-600 text-xs">
                        {{ $vente->lignes->count() }} article(s)
                    </td>
                    <td class="px-4 py-3 text-stone-600">
                        {{ ucfirst(str_replace('_', ' ', $vente->mode_paiement)) }}
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-stone-800">
                        {{ number_format($vente->total_ttc, 0, ',', ' ') }} GNF
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($vente->statut === 'payee')
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">✅ Payée</span>
                        @elseif($vente->statut === 'annulee')
                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">❌ Annulée</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">⏳ En cours</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right text-stone-500 text-xs">
                        {{ $vente->created_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>
@endsection
