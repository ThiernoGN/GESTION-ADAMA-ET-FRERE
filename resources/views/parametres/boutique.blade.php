@extends('layouts.app')
@section('title', 'Ma Boutique')

@section('content')
<div class="p-6 max-w-2xl">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-stone-800">⚙️ Paramètres</h2>
        <p class="text-sm text-stone-500 mt-1">Informations de votre boutique</p>
    </div>

    @include('parametres.partials.nav')

    <div class="bg-white border border-stone-200 rounded-xl p-5 mt-6">
        <h3 class="font-semibold text-stone-800 mb-4">🏪 Informations de la boutique</h3>
        <p class="text-xs text-stone-400 mb-4">Ces informations apparaissent sur les factures PDF.</p>

        <form method="POST" action="{{ route('parametres.boutique.update') }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Nom de la boutique *</label>
                <input type="text" name="nom"
                       value="{{ old('nom', env('BOUTIQUE_NOM', 'Ma Parfumerie')) }}" required
                       class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Téléphone</label>
                    <input type="text" name="telephone"
                           value="{{ old('telephone', env('BOUTIQUE_TELEPHONE', '')) }}"
                           placeholder="ex: +224 620 000 000"
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Email</label>
                    <input type="email" name="email"
                           value="{{ old('email', env('BOUTIQUE_EMAIL', '')) }}"
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Adresse</label>
                <textarea name="adresse" rows="2"
                          class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('adresse', env('BOUTIQUE_ADRESSE', '')) }}</textarea>
            </div>

            <div class="w-40">
                <label class="block text-sm font-medium text-stone-700 mb-1">Devise *</label>
                <select name="devise"
                        class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    @foreach(['GNF','XOF','EUR','USD','MAD','DZD','TND'] as $devise)
                    <option value="{{ $devise }}"
                        {{ old('devise', env('BOUTIQUE_DEVISE', 'GNF')) === $devise ? 'selected' : '' }}>
                        {{ $devise }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-2">
                <button type="submit"
                        class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
                    💾 Enregistrer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
