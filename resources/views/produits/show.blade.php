@extends('layouts.app')
@section('title', $produit->nom)

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('produits.index') }}" class="text-stone-400 hover:text-stone-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-semibold text-stone-800">{{ $produit->nom }}</h2>
                <p class="text-sm text-stone-500">{{ $produit->reference }} — {{ $produit->marque->nom }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('produits.edit', $produit) }}"
               class="inline-flex items-center gap-2 border border-amber-300 text-amber-700 hover:bg-amber-50 px-3 py-2 rounded-lg text-sm transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
            @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('produits.destroy', $produit) }}"
                  onsubmit="return confirm('Supprimer ce produit définitivement ?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 border border-red-200 text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg text-sm transition">
                    Supprimer
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Image + stock --}}
        <div class="space-y-4">

            {{-- Image --}}
            <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
                @if($produit->image)
                <img src="{{ Storage::url($produit->image) }}" alt="{{ $produit->nom }}"
                     class="w-full h-56 object-cover">
                @else
                <div class="w-full h-56 bg-amber-50 flex items-center justify-center text-6xl">
                    🌸
                </div>
                @endif
            </div>

            {{-- Stock --}}
            <div class="bg-white border border-stone-200 rounded-xl p-5">
                <h3 class="font-medium text-stone-800 mb-4">Stock</h3>

                {{-- Jauge de stock --}}
                @php
                    $pct = $produit->stock_minimum > 0
                        ? min(100, round(($produit->stock_actuel / ($produit->stock_minimum * 3)) * 100))
                        : 100;
                    $color = $produit->stock_actuel === 0 ? 'bg-red-500'
                           : ($produit->estStockFaible() ? 'bg-orange-400' : 'bg-green-500');
                @endphp
                <div class="mb-4">
                    <div class="flex justify-between text-xs text-stone-500 mb-1">
                        <span>0</span>
                        <span>Min: {{ $produit->stock_minimum }}</span>
                    </div>
                    <div class="w-full bg-stone-100 rounded-full h-3">
                        <div class="{{ $color }} h-3 rounded-full transition-all"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>

                <div class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-stone-500">Stock actuel</span>
                        <span class="font-bold text-xl
                            {{ $produit->stock_actuel === 0 ? 'text-red-600' :
                               ($produit->estStockFaible() ? 'text-orange-500' : 'text-green-600') }}">
                            {{ $produit->stock_actuel }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-stone-500">Seuil minimum</span>
                        <span class="font-medium text-stone-800">{{ $produit->stock_minimum }}</span>
                    </div>
                    <div class="flex justify-between pt-2 border-t border-stone-100">
                        <span class="text-stone-500">Statut</span>
                        @if($produit->stock_actuel === 0)
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">🚫 Rupture</span>
                        @elseif($produit->estStockFaible())
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">⚠️ Faible</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">✅ OK</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Détails + historique --}}
        <div class="md:col-span-2 space-y-4">

            {{-- Informations --}}
            <div class="bg-white border border-stone-200 rounded-xl p-5">
                <h3 class="font-medium text-stone-800 mb-4">Informations</h3>
                <dl class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-stone-500 mb-1">Catégorie</dt>
                        <dd class="font-medium text-stone-800">{{ $produit->categorie->nom }}</dd>
                    </div>
                    <div>
                        <dt class="text-stone-500 mb-1">Marque</dt>
                        <dd class="font-medium text-stone-800">{{ $produit->marque->nom }}</dd>
                    </div>
                    <div>
                        <dt class="text-stone-500 mb-1">Genre</dt>
                        <dd class="font-medium text-stone-800 capitalize">
                            @php $icons = ['homme'=>'💙','femme'=>'💗','mixte'=>'💜']; @endphp
                            {{ $icons[$produit->genre] ?? '' }} {{ $produit->genre }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-stone-500 mb-1">Contenance</dt>
                        <dd class="font-medium text-stone-800">{{ $produit->contenance ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-stone-500 mb-1">Prix d'achat</dt>
                        <dd class="font-medium text-stone-800">{{ number_format($produit->prix_achat, 0, ',', ' ') }} GNF</dd>
                    </div>
                    <div>
                        <dt class="text-stone-500 mb-1">Prix de vente</dt>
                        <dd class="font-bold text-amber-600 text-base">{{ number_format($produit->prix_vente, 0, ',', ' ') }} GNF</dd>
                    </div>
                    <div>
                        <dt class="text-stone-500 mb-1">Marge</dt>
                        @php
                            $marge = $produit->prix_vente - $produit->prix_achat;
                            $pctMarge = $produit->prix_achat > 0
                                ? round(($marge / $produit->prix_achat) * 100)
                                : 0;
                        @endphp
                        <dd class="font-medium text-green-600">
                            {{ number_format($marge, 0, ',', ' ') }} GNF
                            <span class="text-xs">(+{{ $pctMarge }}%)</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-stone-500 mb-1">Référence</dt>
                        <dd class="font-mono text-stone-700 bg-stone-100 px-2 py-0.5 rounded text-xs inline-block">
                            {{ $produit->reference }}
                        </dd>
                    </div>
                </dl>

                @if($produit->description)
                <div class="mt-4 pt-4 border-t border-stone-100">
                    <dt class="text-stone-500 text-sm mb-1">Description</dt>
                    <dd class="text-stone-700 text-sm">{{ $produit->description }}</dd>
                </div>
                @endif
            </div>

            {{-- Historique des ventes --}}
            <div class="bg-white border border-stone-200 rounded-xl p-5">
                <h3 class="font-medium text-stone-800 mb-4">
                    Historique des ventes
                    <span class="text-xs font-normal text-stone-400 ml-2">
                        ({{ $produit->venteLignes->count() }} transactions)
                    </span>
                </h3>

                @if($produit->venteLignes->isEmpty())
                <p class="text-sm text-stone-400 italic text-center py-4">Aucune vente enregistrée</p>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-stone-100">
                                <th class="text-left py-2 text-xs font-medium text-stone-500 uppercase">N° Vente</th>
                                <th class="text-center py-2 text-xs font-medium text-stone-500 uppercase">Qté</th>
                                <th class="text-right py-2 text-xs font-medium text-stone-500 uppercase">Prix</th>
                                <th class="text-right py-2 text-xs font-medium text-stone-500 uppercase">Sous-total</th>
                                <th class="text-right py-2 text-xs font-medium text-stone-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-stone-50">
                            @foreach($produit->venteLignes->take(10) as $ligne)
                            <tr class="hover:bg-stone-50">
                                <td class="py-2 text-amber-600 font-medium">
                                    <a href="{{ route('ventes.show', $ligne->vente) }}" class="hover:underline">
                                        {{ $ligne->vente->numero }}
                                    </a>
                                </td>
                                <td class="py-2 text-center text-stone-700">{{ $ligne->quantite }}</td>
                                <td class="py-2 text-right text-stone-600">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} GNF</td>
                                <td class="py-2 text-right font-medium text-stone-800">{{ number_format($ligne->sous_total, 0, ',', ' ') }} GNF</td>
                                <td class="py-2 text-right text-stone-400 text-xs">{{ $ligne->vente->created_at->format('d/m/Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t border-stone-200">
                            <tr>
                                <td colspan="3" class="py-2 text-right text-sm font-medium text-stone-600">Total vendu</td>
                                <td class="py-2 text-right font-bold text-amber-600">
                                    {{ number_format($produit->venteLignes->sum('sous_total'), 0, ',', ' ') }} GNF
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection
