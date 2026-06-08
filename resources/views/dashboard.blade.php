@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="p-6">

    <h2 class="text-2xl font-semibold text-stone-800 mb-6">Tableau de bord</h2>

    {{-- Cartes stats cliquables --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">

        <a href="{{ route('ventes.index') }}?date_debut={{ now()->format('Y-m-d') }}"
           class="bg-white rounded-xl border border-stone-200 p-5 hover:border-amber-300 hover:shadow-sm transition block">
            <p class="text-xs text-stone-500 uppercase tracking-wide">VENTE DU JOUR</p>
            <p class="text-2xl font-semibold text-stone-900 mt-1">
                {{ number_format($stats['ca_jour'], 0, ',', ' ') }} GNF
            </p>
            <p class="text-xs text-amber-600 mt-2">Voir les ventes →</p>
        </a>

        <a href="{{ route('ventes.index') }}?date_debut={{ now()->startOfMonth()->format('Y-m-d') }}"
           class="bg-white rounded-xl border border-stone-200 p-5 hover:border-amber-300 hover:shadow-sm transition block">
            <p class="text-xs text-stone-500 uppercase tracking-wide">VENTE DU MOI</p>
            <p class="text-2xl font-semibold text-stone-900 mt-1">
                {{ number_format($stats['ca_mois'], 0, ',', ' ') }} GNF
            </p>
            <p class="text-xs text-amber-600 mt-2">Voir les ventes →</p>
        </a>

        <a href="{{ route('clients.index') }}"
           class="bg-white rounded-xl border border-stone-200 p-5 hover:border-amber-300 hover:shadow-sm transition block">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Clients</p>
            <p class="text-2xl font-semibold text-stone-900 mt-1">{{ $stats['total_clients'] }}</p>
            <p class="text-xs text-amber-600 mt-2">Voir les clients →</p>
        </a>

        <a href="{{ route('produits.index') }}?stock=faible"
           class="bg-white rounded-xl border p-5 hover:shadow-sm transition block
               {{ $stats['stock_faible'] > 0 ? 'border-red-200 bg-red-50' : 'border-stone-200' }}">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Alertes Stock</p>
            <p class="text-2xl font-semibold mt-1
               {{ $stats['stock_faible'] > 0 ? 'text-red-600' : 'text-stone-900' }}">
                {{ $stats['stock_faible'] }}
            </p>
            <p class="text-xs mt-2 {{ $stats['stock_faible'] > 0 ? 'text-red-500' : 'text-amber-600' }}">
                Voir les produits →
            </p>
        </a>
    </div>


    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Top produits --}}
        <div class="bg-white rounded-xl border border-stone-200 p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-medium text-stone-800">Top 5 produits</h3>
                <a href="{{ route('produits.index') }}"
                   class="text-xs text-amber-600 hover:text-amber-700">Voir tout →</a>
            </div>
            @forelse($top_produits as $i => $p)
            <div class="flex items-center justify-between py-2 border-b border-stone-100 last:border-0">
                <div class="flex items-center gap-2">
                    <span class="w-6 h-6 bg-amber-100 text-amber-700 rounded-full text-xs flex items-center justify-center font-medium">
                        {{ $i + 1 }}
                    </span>
                    <span class="text-sm text-stone-700">{{ $p->nom }}</span>
                </div>
                <span class="text-sm font-medium text-stone-900">{{ $p->total_vendu }} ventes</span>
            </div>
            @empty
            <p class="text-sm text-stone-400 text-center py-4">Aucune vente enregistrée</p>
            @endforelse
        </div>

        {{-- Alertes stock --}}
        <div class="bg-white rounded-xl border border-stone-200 p-5">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-medium text-stone-800">⚠️ Alertes stock faible</h3>
                <a href="{{ route('produits.index') }}?stock=faible"
                   class="text-xs text-amber-600 hover:text-amber-700">Voir tout →</a>
            </div>
            @forelse($alertes_stock as $p)
            <a href="{{ route('produits.show', $p) }}"
               class="flex items-center justify-between py-2 border-b border-stone-100 last:border-0 hover:bg-stone-50 rounded px-1 transition">
                <div>
                    <p class="text-sm text-stone-700 font-medium">{{ $p->nom }}</p>
                    <p class="text-xs text-stone-400">{{ $p->marque->nom }}</p>
                </div>
                <span class="text-sm font-semibold text-red-600">
                    {{ $p->stock_actuel }} / {{ $p->stock_minimum }} min
                </span>
            </a>
            @empty
            <p class="text-sm text-green-600">✅ Tous les stocks sont suffisants.</p>
            @endforelse
        </div>
    </div>


</div>
@endsection