@extends('layouts.app')
@section('title', 'Nouveau fournisseur')

@section('content')
<div class="p-6 max-w-2xl mx-auto">

    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('fournisseurs.index') }}" class="text-stone-400 hover:text-stone-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-2xl font-semibold text-stone-800">Nouveau fournisseur</h2>
    </div>

    <form method="POST" action="{{ route('fournisseurs.store') }}">
        @csrf

        <div class="bg-white border border-stone-200 rounded-xl p-5 space-y-4">

            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Nom du fournisseur *</label>
                <input type="text" name="nom" value="{{ old('nom') }}" required
                       class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('nom') border-red-400 @enderror">
                @error('nom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Téléphone</label>
                    <input type="text" name="telephone" value="{{ old('telephone') }}"
                           placeholder="ex: +224 620 000 000"
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-stone-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('email') border-red-400 @enderror">
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-stone-700 mb-1">Adresse</label>
                <textarea name="adresse" rows="2"
                          placeholder="Ville, pays..."
                          class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('adresse') }}</textarea>
            </div>

        </div>

        <div class="flex gap-3 mt-5">
            <button type="submit"
                    class="bg-amber-600 hover:bg-amber-700 text-white px-6 py-2.5 rounded-lg text-sm font-medium transition">
                ✅ Enregistrer le fournisseur
            </button>
            <a href="{{ route('fournisseurs.index') }}"
               class="border border-stone-200 hover:bg-stone-50 text-stone-600 px-6 py-2.5 rounded-lg text-sm transition">
                Annuler
            </a>
        </div>
    </form>
</div>
@endsection
