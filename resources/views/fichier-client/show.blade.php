@extends('layouts.app')
@section('title', 'Fiche — ' . $client->nom_complet)

@section('content')
<div class="p-6">

    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('fichier-client.index') }}" class="text-stone-400 hover:text-stone-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-xl
                    {{ $resume['solde_reel'] > 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                    {{ strtoupper(substr($client->nom, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-2xl font-semibold text-stone-800">{{ $client->nom_complet }}</h2>
                    <p class="text-sm text-stone-500">{{ $client->telephone ?? $client->email ?? '—' }}</p>
                </div>
            </div>
        </div>
        <a href="{{ route('ventes.create') }}"
           class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            🛒 Nouvelle vente
        </a>
    </div>

    {{-- Résumé financier --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Total Achats</p>
            <p class="text-2xl font-bold text-stone-800 mt-1">
                {{ number_format($resume['total_achats'], 0, ',', ' ') }} GNF
            </p>
            <p class="text-xs text-stone-400 mt-1">{{ $resume['nb_ventes'] }} vente(s)</p>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-5">
            <p class="text-xs text-green-600 uppercase tracking-wide">Total Payé</p>
            <p class="text-2xl font-bold text-green-600 mt-1">
                {{ number_format($resume['total_paye'], 0, ',', ' ') }} GNF
            </p>
        </div>
        <div class="border rounded-xl p-5 col-span-2
            {{ $resume['solde_reel'] > 0 ? 'bg-red-50 border-red-300' : 'bg-green-50 border-green-300' }}">
            <p class="text-xs uppercase tracking-wide font-semibold
                {{ $resume['solde_reel'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                💰 Solde Réel
            </p>
            <p class="text-3xl font-bold mt-2
                {{ $resume['solde_reel'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                {{ number_format($resume['solde_reel'], 0, ',', ' ') }} GNF
            </p>
            <p class="text-xs mt-1 {{ $resume['solde_reel'] > 0 ? 'text-red-500' : 'text-green-500' }}">
                {{ $resume['solde_reel'] > 0 ? '⚠️ Solde impayé' : '✅ Client à jour' }}
            </p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white border border-stone-200 rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('fichier-client.show', $client) }}"
              class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-stone-500 mb-1">Date début</label>
                <input type="date" name="date_debut" value="{{ request('date_debut') }}"
                       class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs text-stone-500 mb-1">Date fin</label>
                <input type="date" name="date_fin" value="{{ request('date_fin') }}"
                       class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs text-stone-500 mb-1">Paiement</label>
                <select name="paiement"
                        class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Tous</option>
                    <option value="solde"   {{ request('paiement') === 'solde'   ? 'selected' : '' }}>Avec solde</option>
                    <option value="complet" {{ request('paiement') === 'complet' ? 'selected' : '' }}>Payé complet</option>
                </select>
            </div>
            <button type="submit"
                    class="bg-stone-800 hover:bg-stone-900 text-white px-4 py-2 rounded-lg text-sm transition">
                Filtrer
            </button>
            @if(request()->hasAny(['date_debut','date_fin','paiement']))
            <a href="{{ route('fichier-client.show', $client) }}"
               class="border border-stone-200 text-stone-600 hover:bg-stone-50 px-4 py-2 rounded-lg text-sm transition">
                Réinitialiser
            </a>
            @endif
        </form>
    </div>

    {{-- Ventes --}}
    <div class="space-y-4">

        @forelse($ventes as $vente)

        @php
            $reste          = floatval($vente->reste_a_payer ?? 0);
            $montant_paye_2 = floatval($vente->montant_paye_2 ?? 0);
            $peut_solde     = $reste > 0 && $vente->statut !== 'annulee' && $montant_paye_2 == 0;
        @endphp

        <div class="bg-white border rounded-xl overflow-hidden
            {{ $reste > 0 ? 'border-red-200' : 'border-stone-200' }}">

            {{-- Header de la vente --}}
            <div class="flex items-center justify-between p-4 border-b border-stone-100
                {{ $reste > 0 ? 'bg-red-50' : 'bg-stone-50' }}">
                <div class="flex items-center gap-3">
                    <a href="{{ route('ventes.show', $vente) }}"
                       class="font-bold text-amber-600 hover:underline text-base">
                        {{ $vente->numero }}
                    </a>
                    @if($vente->statut === 'payee')
                        <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">✅ Payée</span>
                    @elseif($vente->statut === 'en_cours')
                        <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">⏳ En cours</span>
                    @else
                        <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">❌ Annulée</span>
                    @endif
                    <span class="text-xs text-stone-400">{{ $vente->created_at->format('d/m/Y à H:i') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('ventes.facture', $vente) }}" target="_blank"
                       class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-600 transition" title="Facture PDF">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </a>
                </div>
            </div>

            {{-- Corps : produits + paiements --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4">

                {{-- Produits achetés --}}
                <div>
                    <p class="text-xs font-medium text-stone-500 uppercase tracking-wide mb-2">Produits</p>
                    @foreach($vente->lignes as $ligne)
                    <div class="flex justify-between text-sm py-1 border-b border-stone-50">
                        <span class="text-stone-700">{{ $ligne->produit->nom }} × {{ $ligne->quantite }}</span>
                        <span class="text-stone-600 font-medium">{{ number_format($ligne->sous_total, 0, ',', ' ') }} GNF</span>
                    </div>
                    @endforeach
                    <div class="flex justify-between text-sm pt-2 font-bold">
                        <span class="text-stone-800">Total TTC</span>
                        <span class="text-amber-600">{{ number_format($vente->total_ttc, 0, ',', ' ') }} GNF</span>
                    </div>
                </div>

                {{-- Situation paiement --}}
                <div>
                    <p class="text-xs font-medium text-stone-500 uppercase tracking-wide mb-2">Paiements</p>

                    {{-- 1er paiement --}}
                    <div class="flex justify-between items-center py-2 border-b border-stone-100">
                        <div>
                            <span class="text-sm text-stone-600">💵 1er paiement</span>
                            <span class="text-xs text-stone-400 block">
                                {{ $vente->date_paiement_1
                                    ? $vente->date_paiement_1->format('d/m/Y H:i')
                                    : $vente->created_at->format('d/m/Y H:i') }}
                            </span>
                        </div>
                        <span class="font-semibold text-green-600">
                            {{ number_format($vente->montant_paye ?? 0, 0, ',', ' ') }} GNF
                        </span>
                    </div>

                    {{-- 2ème paiement si existe --}}
                    @if($montant_paye_2 > 0)
                    <div class="flex justify-between items-center py-2 border-b border-stone-100">
                        <div>
                            <span class="text-sm text-stone-600">💵 2ème paiement</span>
                            <span class="text-xs text-stone-400 block">
                                {{ $vente->date_paiement_2?->format('d/m/Y H:i') ?? '—' }}
                            </span>
                        </div>
                        <span class="font-semibold text-green-600">
                            {{ number_format($montant_paye_2, 0, ',', ' ') }} GNF
                        </span>
                    </div>
                    @endif

                    {{-- Reste à payer --}}
                    <div class="flex justify-between items-center pt-2">
                        <span class="text-sm font-bold {{ $reste > 0 ? 'text-red-700' : 'text-green-700' }}">
                            Reste à payer
                        </span>
                        <span class="font-bold text-lg {{ $reste > 0 ? 'text-red-600' : 'text-green-600' }}">
                            {{ $reste > 0 ? number_format($reste, 0, ',', ' ') . ' GNF' : '✅ Soldé' }}
                        </span>
                    </div>

                    {{-- ★ BOUTON + FORMULAIRE SOLDE ──────────────────── --}}
                    @if($peut_solde)
                    <div class="mt-3 pt-3 border-t border-red-200">

                        {{-- Bouton toggle --}}
                        <button type="button"
                                onclick="toggleSolde('solde-{{ $vente->id }}')"
                                class="w-full flex items-center justify-center gap-2 px-4 py-2.5
                                       bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-semibold transition">
                            💳 Encaisser le solde —
                            {{ number_format($reste, 0, ',', ' ') }} GNF
                        </button>

                        {{-- Formulaire caché --}}
                        <div id="solde-{{ $vente->id }}" class="hidden mt-3">
                            <form method="POST" action="{{ route('ventes.solde', $vente) }}">
                                @csrf
                                <div class="bg-amber-50 border border-amber-300 rounded-xl p-4 space-y-3">

                                    <p class="text-sm font-medium text-amber-800">
                                        Montant à encaisser
                                        <span class="text-xs text-amber-600">(max {{ number_format($reste, 0, ',', ' ') }} GNF)</span>
                                    </p>

                                    <input type="number"
                                           name="montant_paye_2"
                                           min="1"
                                           placeholder="Saisir le montant en GNF"
                                           required
                                           class="w-full border border-amber-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">

                                    <div class="flex gap-2">
                                        <button type="submit"
                                                class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm font-semibold transition">
                                            ✅ Valider le paiement
                                        </button>
                                        <button type="button"
                                                onclick="toggleSolde('solde-{{ $vente->id }}')"
                                                class="px-4 border border-stone-200 rounded-lg text-sm text-stone-600 hover:bg-stone-50 transition">
                                            Annuler
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                    {{-- ────────────────────────────────────────────────── --}}

                </div>
            </div>
        </div>

        @empty
        <div class="bg-white border border-stone-200 rounded-xl p-8 text-center text-stone-400">
            <p class="text-3xl mb-2">📂</p>
            <p class="text-sm">Aucune vente trouvée pour ce client</p>
        </div>
        @endforelse

    </div>

    {{-- Totaux bas de page --}}
    @if($ventes->count() > 0)
    <div class="mt-6 bg-stone-800 text-white rounded-xl p-5">
        <h3 class="font-semibold text-base mb-4 text-center">
            📊 Récapitulatif — {{ $client->nom_complet }}
        </h3>
        <div class="grid grid-cols-3 gap-4 text-center">
            <div>
                <p class="text-xs text-stone-400 uppercase tracking-wide mb-1">Total Achats</p>
                <p class="text-xl font-bold text-white">{{ number_format($resume['total_achats'], 0, ',', ' ') }} GNF</p>
            </div>
            <div>
                <p class="text-xs text-stone-400 uppercase tracking-wide mb-1">Total Payé</p>
                <p class="text-xl font-bold text-green-400">{{ number_format($resume['total_paye'], 0, ',', ' ') }} GNF</p>
            </div>
            <div class="border-l border-stone-600 pl-4">
                <p class="text-xs text-stone-400 uppercase tracking-wide mb-1">Solde Réel</p>
                <p class="text-xl font-bold {{ $resume['solde_reel'] > 0 ? 'text-red-400' : 'text-green-400' }}">
                    {{ number_format($resume['solde_reel'], 0, ',', ' ') }} GNF
                </p>
            </div>
        </div>
    </div>
    @endif

</div>

<script>
function toggleSolde(id) {
    const el = document.getElementById(id);
    el.classList.toggle('hidden');
    if (!el.classList.contains('hidden')) {
        el.querySelector('input[type="number"]').focus();
    }
}
</script>
@endsection