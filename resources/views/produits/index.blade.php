@extends('layouts.app')
@section('title', 'Produits & Stock')

@section('content')
<div class="p-6">

    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-stone-800">Produits & Stock</h2>
            <p class="text-sm text-stone-500 mt-1">Gestion du catalogue et des stocks</p>
        </div>
        <a href="{{ route('produits.create') }}"
           class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau produit
        </a>
    </div>

    {{-- Cartes stock rapide --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white border border-stone-200 rounded-xl p-4">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Total produits</p>
            <p class="text-2xl font-semibold text-stone-800 mt-1">{{ $produits->total() }}</p>
        </div>
        <div class="bg-white border border-stone-200 rounded-xl p-4">
            <p class="text-xs text-stone-500 uppercase tracking-wide">En stock</p>
            <p class="text-2xl font-semibold text-green-600 mt-1">
                {{ \App\Models\Produit::where('stock_actuel', '>', 0)->count() }}
            </p>
        </div>
        <div class="bg-white border border-red-200 bg-red-50 rounded-xl p-4">
            <p class="text-xs text-red-500 uppercase tracking-wide">Stock faible</p>
            <p class="text-2xl font-semibold text-red-600 mt-1">
                {{ \App\Models\Produit::stockFaible()->count() }}
            </p>
        </div>
        <div class="bg-white border border-stone-200 rounded-xl p-4">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Rupture</p>
            <p class="text-2xl font-semibold text-stone-800 mt-1">
                {{ \App\Models\Produit::where('stock_actuel', 0)->count() }}
            </p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white border border-stone-200 rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('produits.index') }}" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nom, référence..."
                   class="border border-stone-200 rounded-lg px-3 py-2 text-sm flex-1 min-w-48 focus:outline-none focus:ring-2 focus:ring-amber-500">

            <select name="categorie_id"
                    class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">Toutes catégories</option>
                @foreach($categories as $cat)
                <option value="{{ $cat->id }}" {{ request('categorie_id') == $cat->id ? 'selected' : '' }}>
                    {{ $cat->nom }}
                </option>
                @endforeach
            </select>

            <select name="marque_id"
                    class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">Toutes marques</option>
                @foreach($marques as $marque)
                <option value="{{ $marque->id }}" {{ request('marque_id') == $marque->id ? 'selected' : '' }}>
                    {{ $marque->nom }}
                </option>
                @endforeach
            </select>

            <select name="genre"
                    class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">Tous genres</option>
                <option value="homme" {{ request('genre') === 'homme' ? 'selected' : '' }}>Homme</option>
                <option value="femme" {{ request('genre') === 'femme' ? 'selected' : '' }}>Femme</option>
                <option value="mixte" {{ request('genre') === 'mixte' ? 'selected' : '' }}>Mixte</option>
            </select>

            <select name="stock"
                    class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">Tout le stock</option>
                <option value="faible"  {{ request('stock') === 'faible'  ? 'selected' : '' }}>Stock faible</option>
                <option value="rupture" {{ request('stock') === 'rupture' ? 'selected' : '' }}>Rupture</option>
            </select>

            <button type="submit"
                    class="bg-stone-800 hover:bg-stone-900 text-white px-4 py-2 rounded-lg text-sm transition">
                Filtrer
            </button>

            @if(request()->hasAny(['search','categorie_id','marque_id','genre','stock']))
            <a href="{{ route('produits.index') }}"
               class="border border-stone-200 hover:bg-stone-50 text-stone-600 px-4 py-2 rounded-lg text-sm transition">
                Réinitialiser
            </a>
            @endif
        </form>
    </div>

    {{-- Tableau --}}
    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-stone-50 border-b border-stone-200">
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Produit</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Catégorie</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Genre</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Prix achat</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Prix vente</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Stock</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Statut</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($produits as $produit)
                <tr class="hover:bg-stone-50 transition {{ $produit->estStockFaible() ? 'bg-red-50' : '' }}">

                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            @if($produit->image)
                            <img src="{{ Storage::url($produit->image) }}" alt="{{ $produit->nom }}"
                                 class="w-10 h-10 rounded-lg object-cover border border-stone-200">
                            @else
                            <div class="w-10 h-10 rounded-lg bg-amber-100 flex items-center justify-center text-lg">
                                🌸
                            </div>
                            @endif
                            <div>
                                <p class="font-medium text-stone-800">{{ $produit->nom }}</p>
                                <p class="text-xs text-stone-400">{{ $produit->marque->nom }} — {{ $produit->reference }}</p>
                                @if($produit->contenance)
                                <p class="text-xs text-stone-400">{{ $produit->contenance }}</p>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td class="px-4 py-3 text-stone-600">{{ $produit->categorie->nom }}</td>

                    <td class="px-4 py-3">
                        @php
                            $genres = ['homme'=>['💙','blue'],'femme'=>['💗','pink'],'mixte'=>['💜','purple']];
                            [$icon,$color] = $genres[$produit->genre] ?? ['⚪','stone'];
                        @endphp
                        <span class="text-xs">{{ $icon }} {{ ucfirst($produit->genre) }}</span>
                    </td>

                    <td class="px-4 py-3 text-right text-stone-600">
                        {{ number_format($produit->prix_achat, 0, ',', ' ') }} GNF
                    </td>

                    <td class="px-4 py-3 text-right font-semibold text-amber-600">
                        {{ number_format($produit->prix_vente, 0, ',', ' ') }} GNF
                    </td>

                    {{-- Stock --}}
                    <td class="px-4 py-3 text-center">
                        <div class="flex flex-col items-center">
                            <span class="text-lg font-bold {{ $produit->stock_actuel === 0 ? 'text-red-600' : ($produit->estStockFaible() ? 'text-orange-500' : 'text-green-600') }}">
                                {{ $produit->stock_actuel }}
                            </span>
                            <span class="text-xs text-stone-400">min: {{ $produit->stock_minimum }}</span>
                        </div>
                    </td>

                    {{-- Statut stock --}}
                    <td class="px-4 py-3 text-center">
                        @if($produit->stock_actuel === 0)
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                🚫 Rupture
                            </span>
                        @elseif($produit->estStockFaible())
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                ⚠️ Faible
                            </span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                ✅ OK
                            </span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('produits.show', $produit) }}"
                               class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-500 transition" title="Voir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('produits.edit', $produit) }}"
                               class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-600 transition" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @if(auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('produits.destroy', $produit) }}"
                                  onsubmit="return confirm('Supprimer ce produit ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 rounded-lg hover:bg-red-50 text-red-500 transition" title="Supprimer">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-12 text-center text-stone-400">
                        <div class="text-4xl mb-2">🌸</div>
                        <p class="text-sm">Aucun produit trouvé</p>
                        <a href="{{ route('produits.create') }}" class="text-amber-600 text-sm mt-1 inline-block">
                            Ajouter un produit →
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($produits->hasPages())
        <div class="px-4 py-3 border-t border-stone-100">
            {{ $produits->links() }}
        </div>
        @endif
    </div>
</div>
@endsection