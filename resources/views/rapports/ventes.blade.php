@extends('layouts.app')
@section('title', 'Rapport des ventes')

@section('content')
<div class="p-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-stone-800">Rapports</h2>
            <p class="text-sm text-stone-500 mt-1">Analyse et statistiques</p>
        </div>
        <a href="{{ route('rapports.export.excel') }}?date_debut={{ $dateDebut }}&date_fin={{ $dateFin }}"
           class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            📥 Exporter CSV
        </a>
    </div>

    {{-- Onglets navigation rapports --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ route('rapports.ventes') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition
                  bg-amber-600 text-white">
            🛒 Rapport Ventes
        </a>
        <a href="{{ route('rapports.stock') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition
                  bg-white border border-stone-200 text-stone-600 hover:border-amber-300 hover:text-amber-600">
            📦 Rapport Stock
        </a>
        <a href="{{ route('rapports.caisse') }}"
           class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium transition
                  bg-white border border-stone-200 text-stone-600 hover:border-amber-300 hover:text-amber-600">
            💰 Rapport Caisse
        </a>
    </div>

    {{-- Filtres --}}
    <div class="bg-white border border-stone-200 rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('rapports.ventes') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs text-stone-500 mb-1">Date début</label>
                <input type="date" name="date_debut" value="{{ $dateDebut }}"
                       class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <div>
                <label class="block text-xs text-stone-500 mb-1">Date fin</label>
                <input type="date" name="date_fin" value="{{ $dateFin }}"
                       class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>
            <button type="submit"
                    class="bg-stone-800 hover:bg-stone-900 text-white px-4 py-2 rounded-lg text-sm transition">
                Filtrer
            </button>
        </form>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <p class="text-xs text-stone-500 uppercase tracking-wide">CA Total</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">
                {{ number_format($stats['ca_total'], 0, ',', ' ') }} GNF
            </p>
        </div>
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Nb Ventes</p>
            <p class="text-2xl font-bold text-stone-800 mt-1">{{ $stats['nb_ventes'] }}</p>
        </div>
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Panier Moyen</p>
            <p class="text-2xl font-bold text-stone-800 mt-1">
                {{ number_format($stats['panier_moyen'], 0, ',', ' ') }} GNF
            </p>
        </div>
        <div class="bg-white border border-red-200 bg-red-50 rounded-xl p-5">
            <p class="text-xs text-red-500 uppercase tracking-wide">Annulées</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stats['nb_annulees'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Top produits --}}
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <h3 class="font-medium text-stone-800 mb-4">🏆 Top 10 produits</h3>
            @forelse($topProduits as $i => $p)
            <div class="flex items-center justify-between py-2 border-b border-stone-100 last:border-0">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-amber-100 text-amber-700 rounded-full text-xs flex items-center justify-center font-bold">
                        {{ $i + 1 }}
                    </span>
                    <div>
                        <p class="text-sm font-medium text-stone-800">{{ $p->nom }}</p>
                        <p class="text-xs text-stone-400">{{ $p->reference }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-stone-800">{{ $p->total_vendu }} unités</p>
                    <p class="text-xs text-amber-600">{{ number_format($p->total_ca, 0, ',', ' ') }} GNF</p>
                </div>
            </div>
            @empty
            <p class="text-sm text-stone-400 text-center py-4">Aucune vente sur cette période</p>
            @endforelse
        </div>

        {{-- Paiements --}}
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <h3 class="font-medium text-stone-800 mb-4">💳 Répartition paiements</h3>
            @php
                $icons = ['especes'=>'💵','carte'=>'💳','mobile_money'=>'📱','credit'=>'📋'];
                $totalPaiements = $paiements->sum('total');
            @endphp
            @forelse($paiements as $p)
            <div class="mb-4">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-stone-700">
                        {{ $icons[$p->mode_paiement] ?? '' }}
                        {{ ucfirst(str_replace('_', ' ', $p->mode_paiement)) }}
                        <span class="text-stone-400">({{ $p->nombre }} ventes)</span>
                    </span>
                    <span class="font-medium text-stone-800">
                        {{ number_format($p->total, 0, ',', ' ') }} GNF
                    </span>
                </div>
                <div class="w-full bg-stone-100 rounded-full h-2">
                    @php $pct = $totalPaiements > 0 ? ($p->total / $totalPaiements) * 100 : 0; @endphp
                    <div class="bg-amber-500 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-sm text-stone-400 text-center py-4">Aucune donnée</p>
            @endforelse
        </div>
    </div>

    {{-- Ventes par jour --}}
    <div class="bg-white border border-stone-200 rounded-xl p-5">
        <h3 class="font-medium text-stone-800 mb-4">📅 Ventes par jour</h3>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-stone-50 border-b border-stone-200">
                    <th class="text-left px-4 py-2 text-xs font-medium text-stone-500 uppercase">Date</th>
                    <th class="text-center px-4 py-2 text-xs font-medium text-stone-500 uppercase">Nb ventes</th>
                    <th class="text-right px-4 py-2 text-xs font-medium text-stone-500 uppercase">CA</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($ventesParJour as $v)
                <tr class="hover:bg-stone-50">
                    <td class="px-4 py-2 text-stone-700">
                        {{ \Carbon\Carbon::parse($v->date)->format('d/m/Y') }}
                    </td>
                    <td class="px-4 py-2 text-center text-stone-600">{{ $v->nombre }}</td>
                    <td class="px-4 py-2 text-right font-semibold text-amber-600">
                        {{ number_format($v->total, 0, ',', ' ') }} GNF
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="px-4 py-8 text-center text-stone-400 text-sm">
                        Aucune vente sur cette période
                    </td>
                </tr>
                @endforelse
            </tbody>
            @if($ventesParJour->count() > 0)
            <tfoot class="border-t border-stone-200 bg-stone-50">
                <tr>
                    <td class="px-4 py-3 font-bold text-stone-800">TOTAL</td>
                    <td class="px-4 py-3 text-center font-bold text-stone-800">{{ $stats['nb_ventes'] }}</td>
                    <td class="px-4 py-3 text-right font-bold text-amber-600">
                        {{ number_format($stats['ca_total'], 0, ',', ' ') }} GNF
                    </td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
@endsection