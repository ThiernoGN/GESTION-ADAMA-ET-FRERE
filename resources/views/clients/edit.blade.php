@extends('layouts.app')
@section('title', 'Modifier ' . $client->nom_complet)

@section('content')
<div class="p-6 max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('clients.show', $client) }}" class="text-stone-400 hover:text-stone-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <div>
            <h2 class="text-2xl font-semibold text-stone-800">Modifier le client</h2>
            <p class="text-sm text-stone-500">{{ $client->nom_complet }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('clients.update', $client) }}">
        @csrf
        @method('PUT')

        <div class="bg-white border border-stone-200 rounded-xl p-5 space-y-4">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Nom *</label>
                    <input type="text" name="nom" value="{{ old('nom', $client->nom) }}" required
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('nom') border-red-400 @enderror">
                    @error('nom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Prénom *</label>
                    <input type="text" name="prenom" value="{{ old('prenom', $client->prenom) }}" required
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('prenom') border-red-400 @enderror">
                    @error('prenom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $client->telephone) }}"
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('telephone') border-red-400 @enderror">
                    @error('telephone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $client->email) }}"
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('email') border-red-400 @enderror">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Adresse</label>
                <textarea name="adresse" rows="2"
                          class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('adresse', $client->adresse) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Points fidélité</label>
                <input type="number" name="points_fidelite"
                       value="{{ old('points_fidelite', $client->points_fidelite) }}" min="0"
                       class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <p class="text-xs text-stone-400 mt-1">1 point = 1 000 GNF d'achat</p>
            </div>

        </div>

        <div class="flex gap-3 mt-5">
            <button type="submit"
                    class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
                💾 Enregistrer les modifications
            </button>
            <a href="{{ route('clients.show', $client) }}"
               class="border border-stone-200 hover:bg-stone-50 text-stone-600 px-6 py-2.5 rounded-lg text-sm transition">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
