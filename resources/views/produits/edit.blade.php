@extends('layouts.app')
@section('title', 'Modifier ' . $produit->nom)

@section('content')
<div class="p-6 max-w-3xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('produits.show', $produit) }}" class="text-stone-400 hover:text-stone-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-semibold text-stone-800">Modifier le produit</h2>
            <p class="text-sm text-stone-500">{{ $produit->reference }} — {{ $produit->nom }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('produits.update', $produit) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-5">

            {{-- Informations générales --}}
            <div class="bg-white border border-stone-200 rounded-xl p-5">
                <h3 class="font-medium text-stone-800 mb-4">Informations générales</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Nom du produit *</label>
                        <input type="text" name="nom" value="{{ old('nom', $produit->nom) }}" required
                               class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('nom') border-red-400 @enderror">
                        @error('nom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Référence *</label>
                        <input type="text" name="reference" value="{{ old('reference', $produit->reference) }}" required
                               class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('reference') border-red-400 @enderror">
                        @error('reference') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Catégorie *</label>
                        <select name="categorie_id" required
                                class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Sélectionner...</option>
                            @foreach($categories as $cat)
                            <option value="{{ $cat->id }}"
                                {{ old('categorie_id', $produit->categorie_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nom }}
                            </option>
                            @endforeach
                        </select>
                    </div>


                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Genre *</label>
                        <select name="genre" required
                                class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="mixte" {{ old('genre', $produit->genre) === 'mixte' ? 'selected' : '' }}>💜 Mixte</option>
                            <option value="homme" {{ old('genre', $produit->genre) === 'homme' ? 'selected' : '' }}>💙 Homme</option>
                            <option value="femme" {{ old('genre', $produit->genre) === 'femme' ? 'selected' : '' }}>💗 Femme</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Contenance</label>
                        <input type="text" name="contenance"
                               value="{{ old('contenance', $produit->contenance) }}"
                               placeholder="ex: 50ml, 100ml"
                               class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-stone-700 mb-1">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('description', $produit->description) }}</textarea>
                </div>
            </div>

            {{-- Prix --}}
            <div class="bg-white border border-stone-200 rounded-xl p-5">
                <h3 class="font-medium text-stone-800 mb-4">Prix</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Prix d'achat (GNF) *</label>
                        <input type="number" name="prix_achat"
                               value="{{ old('prix_achat', $produit->prix_achat) }}"
                               required min="0"
                               class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('prix_achat') border-red-400 @enderror">
                        @error('prix_achat') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Prix de vente (GNF) *</label>
                        <input type="number" name="prix_vente"
                               value="{{ old('prix_vente', $produit->prix_vente) }}"
                               required min="0"
                               class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('prix_vente') border-red-400 @enderror">
                        @error('prix_vente') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Aperçu marge --}}
                <div class="mt-3 p-3 bg-green-50 border border-green-100 rounded-lg text-sm text-green-700" id="apercu-marge">
                    Marge : calcul en cours...
                </div>
            </div>

            {{-- Stock --}}
            <div class="bg-white border border-stone-200 rounded-xl p-5">
                <h3 class="font-medium text-stone-800 mb-4">Stock</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Stock actuel *</label>
                        <input type="number" name="stock_actuel"
                               value="{{ old('stock_actuel', $produit->stock_actuel) }}"
                               required min="0"
                               class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-stone-700 mb-1">Stock minimum *</label>
                        <input type="number" name="stock_minimum"
                               value="{{ old('stock_minimum', $produit->stock_minimum) }}"
                               required min="0"
                               class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <p class="text-xs text-stone-400 mt-1">Alerte si stock ≤ ce seuil</p>
                    </div>
                </div>
            </div>

            {{-- Image --}}
            <div class="bg-white border border-stone-200 rounded-xl p-5">
                <h3 class="font-medium text-stone-800 mb-4">Image</h3>

                @if($produit->image)
                <div class="flex items-center gap-4 mb-3">
                    <img src="{{ Storage::url($produit->image) }}"
                         alt="{{ $produit->nom }}"
                         class="w-20 h-20 object-cover rounded-lg border border-stone-200">
                    <p class="text-sm text-stone-500">Image actuelle — choisissez un nouveau fichier pour la remplacer</p>
                </div>
                @endif

                <input type="file" name="image" accept="image/*"
                       class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none">
                <p class="text-xs text-stone-400 mt-1">JPG, PNG — max 2MB</p>
            </div>

            {{-- Boutons --}}
            <div class="flex gap-3">
                <button type="submit"
                        class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
                    💾 Enregistrer les modifications
                </button>
                <a href="{{ route('produits.show', $produit) }}"
                   class="border border-stone-200 hover:bg-stone-50 text-stone-600 px-6 py-2.5 rounded-lg text-sm transition">
                    Annuler
                </a>
            </div>

        </div>
    </form>
</div>

<script>
    const achatInput  = document.querySelector('[name="prix_achat"]');
    const venteInput  = document.querySelector('[name="prix_vente"]');
    const apercuMarge = document.getElementById('apercu-marge');

    function calculerMarge() {
        const achat = parseFloat(achatInput.value) || 0;
        const vente = parseFloat(venteInput.value) || 0;
        const marge = vente - achat;
        const pct   = achat > 0 ? ((marge / achat) * 100).toFixed(1) : 0;

        if (marge >= 0) {
            apercuMarge.className = 'mt-3 p-3 bg-green-50 border border-green-100 rounded-lg text-sm text-green-700';
            apercuMarge.textContent = `✅ Marge : ${marge.toLocaleString('fr-FR')} GNF (+${pct}%)`;
        } else {
            apercuMarge.className = 'mt-3 p-3 bg-red-50 border border-red-100 rounded-lg text-sm text-red-700';
            apercuMarge.textContent = `⚠️ Prix de vente inférieur au prix d'achat ! Marge négative : ${marge.toLocaleString('fr-FR')} GNF`;
        }
    }

    achatInput.addEventListener('input', calculerMarge);
    venteInput.addEventListener('input', calculerMarge);
    calculerMarge(); // Au chargement
</script>
@endsection
