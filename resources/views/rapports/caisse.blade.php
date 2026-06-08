@extends('layouts.app')
@section('title', 'Rapport Caisse')

@section('content')
<div class="p-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-stone-800">Rapports</h2>
            <p class="text-sm text-stone-500 mt-1">Analyse et statistiques</p>
        </div>
    </div>

    {{-- Onglets --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ route('rapports.ventes') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white border border-stone-200 text-stone-600 hover:border-amber-300 transition">
            🛒 Rapport Ventes
        </a>
        <a href="{{ route('rapports.stock') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-white border border-stone-200 text-stone-600 hover:border-amber-300 transition">
            📦 Rapport Stock
        </a>
        <a href="{{ route('rapports.caisse') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
                  bg-amber-600 text-white">
            💰 Rapport Caisse
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         NOUVEAU — SITUATION GLOBALE DE LA CAISSE (tout temps)
    ═══════════════════════════════════════════════════════ --}}
    <div class="bg-stone-900 text-white rounded-xl p-5 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-base">🏦 Situation actuelle de la caisse — Depuis le début</h3>
            <span class="text-xs text-stone-400">Mise à jour en temps réel</span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-stone-800 rounded-xl p-4">
                <p class="text-xs text-stone-400 uppercase tracking-wide mb-1">Total Entrées</p>
                <p class="text-xl font-bold text-green-400">
                    +{{ number_format($situation['total_entrees'], 0, ',', ' ') }} GNF
                </p>
                <p class="text-xs text-stone-500 mt-1">{{ $situation['nb_ventes_total'] }} ventes</p>
            </div>
            <div class="bg-stone-800 rounded-xl p-4">
                <p class="text-xs text-stone-400 uppercase tracking-wide mb-1">Total Sorties</p>
                <p class="text-xl font-bold text-red-400">
                    -{{ number_format($situation['total_sorties'], 0, ',', ' ') }} GNF
                </p>
                <p class="text-xs text-stone-500 mt-1">{{ $situation['nb_achats_total'] }} commandes</p>
            </div>
            <div class="bg-stone-800 rounded-xl p-4">
                <p class="text-xs text-stone-400 uppercase tracking-wide mb-1">Ventes annulées</p>
                <p class="text-xl font-bold text-orange-400">
                    {{ number_format($situation['total_annulees'], 0, ',', ' ') }} GNF
                </p>
                <p class="text-xs text-stone-500 mt-1">{{ $situation['nb_annulees_total'] }} annulations</p>
            </div>
            <div class="bg-stone-800 rounded-xl p-4 border border-amber-500">
                <p class="text-xs text-amber-400 uppercase tracking-wide mb-1">💰 Solde Caisse</p>
                <p class="text-xl font-bold {{ $situation['solde_global'] >= 0 ? 'text-amber-400' : 'text-red-400' }}">
                    {{ $situation['solde_global'] >= 0 ? '+' : '' }}{{ number_format($situation['solde_global'], 0, ',', ' ') }} GNF
                </p>
                <p class="text-xs mt-1 {{ $situation['solde_global'] >= 0 ? 'text-amber-500' : 'text-red-500' }}">
                    {{ $situation['solde_global'] >= 0 ? '✅ Bénéficiaire' : '⚠️ Déficitaire' }}
                </p>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         FILTRE — PAR DATE ou PAR PÉRIODE
    ═══════════════════════════════════════════════════════ --}}
    <div class="bg-white border border-stone-200 rounded-xl p-4 mb-6">
        <div class="flex gap-3 mb-3">
            <button onclick="toggleMode('jour')" id="btn-jour"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition border
                           bg-amber-600 text-white border-amber-600">
                📅 Par jour
            </button>
            <button onclick="toggleMode('periode')" id="btn-periode"
                    class="px-3 py-1.5 rounded-lg text-sm font-medium transition border
                           bg-white text-stone-600 border-stone-200 hover:border-amber-300">
                📆 Par période
            </button>
        </div>

        {{-- Filtre par jour --}}
        <form method="GET" action="{{ route('rapports.caisse') }}"
              id="form-jour"
              class="flex flex-wrap gap-3 items-end">
            <input type="hidden" name="mode" value="jour">
            <div>
                <label class="block text-xs text-stone-500 mb-1">Date</label>
                <input type="date" name="date" value="{{ $date }}"
                       class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <button type="submit"
                    class="bg-stone-800 hover:bg-stone-900 text-white px-4 py-2 rounded-lg text-sm transition">
                Filtrer
            </button>
            <a href="{{ route('rapports.caisse') }}"
               class="border border-stone-200 text-stone-600 hover:bg-stone-50 px-4 py-2 rounded-lg text-sm transition">
                Aujourd'hui
            </a>
        </form>

        {{-- Filtre par période --}}
        <form method="GET" action="{{ route('rapports.caisse') }}"
              id="form-periode"
              class="flex flex-wrap gap-3 items-end hidden">
            <input type="hidden" name="mode" value="periode">
            <div>
                <label class="block text-xs text-stone-500 mb-1">Date début</label>
                <input type="date" name="date_debut" value="{{ $dateDebut ?? now()->startOfMonth()->format('Y-m-d') }}"
                       class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs text-stone-500 mb-1">Date fin</label>
                <input type="date" name="date_fin" value="{{ $dateFin ?? now()->format('Y-m-d') }}"
                       class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <button type="submit"
                    class="bg-stone-800 hover:bg-stone-900 text-white px-4 py-2 rounded-lg text-sm transition">
                Filtrer la période
            </button>
            <a href="{{ route('rapports.caisse') }}?mode=periode&date_debut={{ now()->startOfMonth()->format('Y-m-d') }}&date_fin={{ now()->format('Y-m-d') }}"
               class="border border-stone-200 text-stone-600 hover:bg-stone-50 px-4 py-2 rounded-lg text-sm transition">
                Ce mois
            </a>
        </form>
    </div>

    {{-- Titre de la période affichée --}}
    <div class="mb-4 flex items-center gap-2">
        <span class="text-sm font-medium text-stone-600">
            📋 Résultats pour :
        </span>
        @if(isset($dateDebut) && isset($dateFin) && request('mode') === 'periode')
        <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-sm font-medium">
            Du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}
            au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
        </span>
        @else
        <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-sm font-medium">
            {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
        </span>
        @endif
    </div>

    {{-- Résumé général (existant conservé) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        <div class="bg-green-50 border border-green-200 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-2xl">📈</span>
                <p class="text-sm font-semibold text-green-700 uppercase tracking-wide">Entrées (Ventes)</p>
            </div>
            <p class="text-3xl font-bold text-green-600">
                {{ number_format($entrees['total'], 0, ',', ' ') }} GNF
            </p>
            <p class="text-xs text-green-600 mt-2">{{ $entrees['nb_ventes'] }} vente(s) payée(s)</p>
            @if($entrees['annulees'] > 0)
            <p class="text-xs text-red-500 mt-1">{{ $entrees['annulees'] }} annulée(s)</p>
            @endif
        </div>

        <div class="bg-red-50 border border-red-200 rounded-xl p-5">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-2xl">📉</span>
                <p class="text-sm font-semibold text-red-700 uppercase tracking-wide">Sorties (Achats)</p>
            </div>
            <p class="text-3xl font-bold text-red-600">
                {{ number_format($sorties['total'], 0, ',', ' ') }} GNF
            </p>
            <p class="text-xs text-red-600 mt-2">{{ $sorties['nb_commandes'] }} commande(s) reçue(s)</p>
        </div>

        <div class="border rounded-xl p-5
            {{ $solde >= 0 ? 'bg-amber-50 border-amber-200' : 'bg-red-50 border-red-300' }}">
            <div class="flex items-center gap-2 mb-3">
                <span class="text-2xl">💰</span>
                <p class="text-sm font-semibold uppercase tracking-wide
                   {{ $solde >= 0 ? 'text-amber-700' : 'text-red-700' }}">
                    Solde Période
                </p>
            </div>
            <p class="text-3xl font-bold {{ $solde >= 0 ? 'text-amber-600' : 'text-red-600' }}">
                {{ $solde >= 0 ? '+' : '' }}{{ number_format($solde, 0, ',', ' ') }} GNF
            </p>
            <p class="text-xs mt-2 {{ $solde >= 0 ? 'text-amber-600' : 'text-red-600' }}">
                {{ $solde >= 0 ? '✅ Caisse bénéficiaire' : '⚠️ Caisse déficitaire' }}
            </p>
        </div>
    </div>

    {{-- Détail par mode paiement (existant conservé) --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-stone-200 rounded-xl p-4">
            <p class="text-xs text-stone-500 uppercase tracking-wide">💵 Espèces</p>
            <p class="text-xl font-bold text-stone-800 mt-1">
                {{ number_format($entrees['especes'], 0, ',', ' ') }} GNF
            </p>
        </div>
        <div class="bg-white border border-stone-200 rounded-xl p-4">
            <p class="text-xs text-stone-500 uppercase tracking-wide">💳 Carte</p>
            <p class="text-xl font-bold text-stone-800 mt-1">
                {{ number_format($entrees['carte'], 0, ',', ' ') }} GNF
            </p>
        </div>
        <div class="bg-white border border-stone-200 rounded-xl p-4">
            <p class="text-xs text-stone-500 uppercase tracking-wide">📱 Mobile Money</p>
            <p class="text-xl font-bold text-stone-800 mt-1">
                {{ number_format($entrees['mobile_money'], 0, ',', ' ') }} GNF
            </p>
        </div>
        <div class="bg-white border border-stone-200 rounded-xl p-4">
            <p class="text-xs text-stone-500 uppercase tracking-wide">📋 Crédit</p>
            <p class="text-xl font-bold text-stone-800 mt-1">
                {{ number_format($entrees['credit'], 0, ',', ' ') }} GNF
            </p>
        </div>
    </div>

    {{-- Tableaux entrées / sorties (existant conservé) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
            <div class="p-4 border-b border-stone-100 bg-green-50 flex justify-between items-center">
                <h3 class="font-medium text-green-800">📈 Entrées — Ventes</h3>
                <span class="text-xs font-semibold text-green-700 bg-green-100 px-2 py-1 rounded-full">
                    +{{ number_format($entrees['total'], 0, ',', ' ') }} GNF
                </span>
            </div>
            @if($ventes->isEmpty())
            <div class="p-6 text-center text-stone-400">
                <p class="text-sm">Aucune vente sur cette période</p>
            </div>
            @else
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-stone-50 border-b border-stone-100">
                        <th class="text-left px-3 py-2 text-xs font-medium text-stone-500 uppercase">N°</th>
                        <th class="text-left px-3 py-2 text-xs font-medium text-stone-500 uppercase">
                            {{ request('mode') === 'periode' ? 'Date' : 'Heure' }}
                        </th>
                        <th class="text-left px-3 py-2 text-xs font-medium text-stone-500 uppercase">Client</th>
                        <th class="text-left px-3 py-2 text-xs font-medium text-stone-500 uppercase">Paiem.</th>
                        <th class="text-right px-3 py-2 text-xs font-medium text-stone-500 uppercase">Montant</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach($ventes as $vente)
                    <tr class="hover:bg-green-50 transition">
                        <td class="px-3 py-2">
                            <a href="{{ route('ventes.show', $vente) }}"
                               class="font-medium text-amber-600 hover:underline text-xs">
                                {{ $vente->numero }}
                            </a>
                        </td>
                        <td class="px-3 py-2 text-stone-400 text-xs">
                            {{ request('mode') === 'periode'
                                ? $vente->created_at->format('d/m H:i')
                                : $vente->created_at->format('H:i') }}
                        </td>
                        <td class="px-3 py-2 text-stone-700 text-xs">
                            {{ $vente->client ? $vente->client->nom : 'Passager' }}
                        </td>
                        <td class="px-3 py-2 text-xs">
                            @php $icons = ['especes'=>'💵','carte'=>'💳','mobile_money'=>'📱','credit'=>'📋']; @endphp
                            {{ $icons[$vente->mode_paiement] ?? '' }}
                        </td>
                        <td class="px-3 py-2 text-right font-semibold text-green-600 text-xs">
                            +{{ number_format($vente->total_ttc, 0, ',', ' ') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t border-stone-200 bg-green-50">
                    <tr>
                        <td colspan="4" class="px-3 py-2 font-bold text-stone-800 text-right text-sm">Total</td>
                        <td class="px-3 py-2 text-right font-bold text-green-600">
                            +{{ number_format($entrees['total'], 0, ',', ' ') }} GNF
                        </td>
                    </tr>
                </tfoot>
            </table>
            @endif
        </div>

        <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
            <div class="p-4 border-b border-stone-100 bg-red-50 flex justify-between items-center">
                <h3 class="font-medium text-red-800">📉 Sorties — Achats fournisseurs</h3>
                <span class="text-xs font-semibold text-red-700 bg-red-100 px-2 py-1 rounded-full">
                    -{{ number_format($sorties['total'], 0, ',', ' ') }} GNF
                </span>
            </div>
            @if($achats->isEmpty())
            <div class="p-6 text-center text-stone-400">
                <p class="text-sm">Aucun achat fournisseur sur cette période</p>
            </div>
            @else
            {{-- Dans le tableau des achats, remplacer thead et tbody --}}
<table class="w-full text-sm">
    <thead>
        <tr class="bg-stone-50 border-b border-stone-100">
            <th class="text-left px-3 py-2 text-xs font-medium text-stone-500 uppercase">ID</th>
            <th class="text-left px-3 py-2 text-xs font-medium text-stone-500 uppercase">
                {{ request('mode') === 'periode' ? 'Date' : 'Heure' }}
            </th>
            <th class="text-left px-3 py-2 text-xs font-medium text-stone-500 uppercase">Fournisseur</th>
            <th class="text-center px-3 py-2 text-xs font-medium text-stone-500 uppercase">Statut</th>
            <th class="text-right px-3 py-2 text-xs font-medium text-stone-500 uppercase">Montant</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-stone-100">
        @foreach($achats as $achat)
        <tr class="hover:bg-red-50 transition">
            <td class="px-3 py-2 font-mono text-stone-400 text-xs">#{{ $achat->id }}</td>
            <td class="px-3 py-2 text-stone-400 text-xs">
                {{ request('mode') === 'periode'
                    ? $achat->created_at->format('d/m H:i')
                    : $achat->created_at->format('H:i') }}
            </td>
            <td class="px-3 py-2 text-stone-700 text-xs">{{ $achat->fournisseur->nom }}</td>
            <td class="px-3 py-2 text-center">
                @if($achat->statut === 'recue')
                    <span class="px-2 py-0.5 rounded-full text-xs bg-green-100 text-green-700">✅ Reçue</span>
                @elseif($achat->statut === 'annulee')
                    <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-700">❌ Annulée</span>
                @else
                    <span class="px-2 py-0.5 rounded-full text-xs bg-yellow-100 text-yellow-700">⏳ En attente</span>
                @endif
            </td>
            <td class="px-3 py-2 text-right font-semibold text-red-600 text-xs">
                -{{ number_format($achat->total, 0, ',', ' ') }}
            </td>
        </tr>
        @endforeach
    </tbody>
    <tfoot class="border-t border-stone-200 bg-red-50">
        <tr>
            <td colspan="4" class="px-3 py-2 font-bold text-stone-800 text-right text-sm">Total</td>
            <td class="px-3 py-2 text-right font-bold text-red-600">
                -{{ number_format($sorties['total'], 0, ',', ' ') }} GNF
            </td>
        </tr>
    </tfoot>
</table>
            @endif
        </div>
    </div>

    {{-- Récapitulatif final (existant conservé) --}}
    <div class="mt-6 bg-stone-800 text-white rounded-xl p-6">
        <h3 class="font-semibold text-lg mb-4 text-center">
            📊 Récapitulatif —
            @if(request('mode') === 'periode')
                Du {{ \Carbon\Carbon::parse($dateDebut)->format('d/m/Y') }}
                au {{ \Carbon\Carbon::parse($dateFin)->format('d/m/Y') }}
            @else
                {{ \Carbon\Carbon::parse($date)->format('d/m/Y') }}
            @endif
        </h3>
        <div class="grid grid-cols-3 gap-6 text-center">
            <div>
                <p class="text-xs text-stone-400 uppercase tracking-wide mb-1">Total Entrées</p>
                <p class="text-2xl font-bold text-green-400">+{{ number_format($entrees['total'], 0, ',', ' ') }} GNF</p>
            </div>
            <div>
                <p class="text-xs text-stone-400 uppercase tracking-wide mb-1">Total Sorties</p>
                <p class="text-2xl font-bold text-red-400">-{{ number_format($sorties['total'], 0, ',', ' ') }} GNF</p>
            </div>
            <div class="border-l border-stone-600 pl-6">
                <p class="text-xs text-stone-400 uppercase tracking-wide mb-1">Solde Net</p>
                <p class="text-2xl font-bold {{ $solde >= 0 ? 'text-amber-400' : 'text-red-400' }}">
                    {{ $solde >= 0 ? '+' : '' }}{{ number_format($solde, 0, ',', ' ') }} GNF
                </p>
            </div>
        </div>
    </div>

</div>

<script>
// Détecter le mode actif au chargement
const modeActif = "{{ request('mode', 'jour') }}";
if (modeActif === 'periode') {
    toggleMode('periode');
}

function toggleMode(mode) {
    const formJour    = document.getElementById('form-jour');
    const formPeriode = document.getElementById('form-periode');
    const btnJour     = document.getElementById('btn-jour');
    const btnPeriode  = document.getElementById('btn-periode');

    if (mode === 'jour') {
        formJour.classList.remove('hidden');
        formPeriode.classList.add('hidden');
        btnJour.className    = 'px-3 py-1.5 rounded-lg text-sm font-medium transition border bg-amber-600 text-white border-amber-600';
        btnPeriode.className = 'px-3 py-1.5 rounded-lg text-sm font-medium transition border bg-white text-stone-600 border-stone-200 hover:border-amber-300';
    } else {
        formPeriode.classList.remove('hidden');
        formJour.classList.add('hidden');
        btnPeriode.className = 'px-3 py-1.5 rounded-lg text-sm font-medium transition border bg-amber-600 text-white border-amber-600';
        btnJour.className    = 'px-3 py-1.5 rounded-lg text-sm font-medium transition border bg-white text-stone-600 border-stone-200 hover:border-amber-300';
    }
}
</script>
@endsection