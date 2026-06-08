@extends('layouts.app')
@section('title', 'Rapport Stock')

@section('content')
<div class="p-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-stone-800">Rapport Stock</h2>
            <p class="text-sm text-stone-500 mt-1">État du stock en temps réel</p>
        </div>
        <a href="{{ route('produits.create') }}"
           class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            + Ajouter un produit
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Total produits</p>
            <p class="text-2xl font-bold text-stone-800 mt-1">{{ $produits->count() }}</p>
        </div>
        <div class="bg-white border border-red-200 bg-red-50 rounded-xl p-5">
            <p class="text-xs text-red-500 uppercase tracking-wide">Stock faible</p>
            <p class="text-2xl font-bold text-red-600 mt-1">{{ $stockFaible->count() }}</p>
        </div>
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Valeur stock (achat)</p>
            <p class="text-xl font-bold text-stone-800 mt-1">
                {{ number_format($valeurStock, 0, ',', ' ') }} GNF
            </p>
        </div>
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Valeur stock (vente)</p>
            <p class="text-xl font-bold text-amber-600 mt-1">
                {{ number_format($valeurVente, 0, ',', ' ') }} GNF
            </p>
        </div>
    </div>
    {{-- Onglets --}}
<div class="flex gap-2 mb-6">
    <a href="{{ route('rapports.ventes') }}"
       class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
              bg-white border border-stone-200 text-stone-600 hover:border-amber-300 hover:text-amber-600 transition">
        🛒 Rapport Ventes
    </a>
    <a href="{{ route('rapports.stock') }}"
       class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
              bg-amber-600 text-white">
        📦 Rapport Stock
    </a>
    <a href="{{ route('rapports.caisse') }}"
       class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium
              bg-white border border-stone-200 text-stone-600 hover:border-amber-300 hover:text-amber-600 transition">
        💰 Rapport Caisse
    </a>
</div>

    {{-- Tableau stock complet --}}
    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
        <div class="p-4 border-b border-stone-100">
            <h3 class="font-medium text-stone-800">État du stock complet</h3>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-stone-50 border-b border-stone-200">
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Produit</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Catégorie</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Stock</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Min</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Valeur achat</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Valeur vente</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach($produits as $p)
                <tr class="hover:bg-stone-50 transition {{ $p->estStockFaible() ? 'bg-red-50' : '' }}">
                    <td class="px-4 py-3">
                        <a href="{{ route('produits.show', $p) }}"
                           class="font-medium text-stone-800 hover:text-amber-600 hover:underline">
                            {{ $p->nom }}
                        </a>
                        <p class="text-xs text-stone-400">{{ $p->marque->nom }} — {{ $p->reference }}</p>
                    </td>
                    <td class="px-4 py-3 text-stone-600">{{ $p->categorie->nom }}</td>
                    <td class="px-4 py-3 text-center font-bold
                        {{ $p->stock_actuel === 0 ? 'text-red-600' :
                           ($p->estStockFaible() ? 'text-orange-500' : 'text-green-600') }}">
                        {{ $p->stock_actuel }}
                    </td>
                    <td class="px-4 py-3 text-center text-stone-500">{{ $p->stock_minimum }}</td>
                    <td class="px-4 py-3 text-right text-stone-600">
                        {{ number_format($p->stock_actuel * $p->prix_achat, 0, ',', ' ') }} GNF
                    </td>
                    <td class="px-4 py-3 text-right text-amber-600 font-medium">
                        {{ number_format($p->stock_actuel * $p->prix_vente, 0, ',', ' ') }} GNF
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($p->stock_actuel === 0)
                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">🚫 Rupture</span>
                        @elseif($p->estStockFaible())
                            <span class="px-2 py-1 rounded-full text-xs bg-orange-100 text-orange-700">⚠️ Faible</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">✅ OK</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection