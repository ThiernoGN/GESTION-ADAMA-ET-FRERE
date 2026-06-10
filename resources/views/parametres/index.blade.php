@extends('layouts.app')
@section('title', 'Paramètres')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-stone-800">⚙️ Paramètres</h2>
        <p class="text-sm text-stone-500 mt-1">Gérez toute votre application depuis un seul endroit</p>
    </div>

    @include('parametres.partials.nav')

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mt-6">

        <a href="{{ route('parametres.utilisateurs') }}"
           class="bg-white border border-stone-200 rounded-xl p-5 hover:border-amber-300 hover:shadow-sm transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-2xl">👤</div>
                <div>
                    <p class="font-semibold text-stone-800">Utilisateurs</p>
                    <p class="text-xs text-stone-500 mt-0.5">Comptes, rôles, accès</p>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-2xl font-bold text-amber-600">{{ $stats['utilisateurs'] }}</span>
                <span class="text-xs text-stone-400">utilisateur(s)</span>
            </div>
        </a>

        <a href="{{ route('parametres.categories') }}"
           class="bg-white border border-stone-200 rounded-xl p-5 hover:border-amber-300 hover:shadow-sm transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-2xl">🏷️</div>
                <div>
                    <p class="font-semibold text-stone-800">Catégories</p>
                    <p class="text-xs text-stone-500 mt-0.5">Types de parfums</p>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-2xl font-bold text-blue-600">{{ $stats['categories'] }}</span>
                <span class="text-xs text-stone-400">catégorie(s)</span>
            </div>
        </a>


        <a href="{{ route('fournisseurs.index') }}"
           class="bg-white border border-stone-200 rounded-xl p-5 hover:border-amber-300 hover:shadow-sm transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center text-2xl">🚚</div>
                <div>
                    <p class="font-semibold text-stone-800">Fournisseurs</p>
                    <p class="text-xs text-stone-500 mt-0.5">Gestion des fournisseurs</p>
                </div>
            </div>
            <div class="mt-4 flex items-center justify-between">
                <span class="text-2xl font-bold text-green-600">{{ $stats['fournisseurs'] }}</span>
                <span class="text-xs text-stone-400">fournisseur(s)</span>
            </div>
        </a>

        <a href="{{ route('parametres.boutique') }}"
           class="bg-white border border-stone-200 rounded-xl p-5 hover:border-amber-300 hover:shadow-sm transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-stone-100 rounded-xl flex items-center justify-center text-2xl">🏪</div>
                <div>
                    <p class="font-semibold text-stone-800">Ma Boutique</p>
                    <p class="text-xs text-stone-500 mt-0.5">Nom, adresse, devise</p>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-amber-600">Modifier les informations →</span>
            </div>
        </a>

        <a href="{{ route('profile.edit') }}"
           class="bg-white border border-stone-200 rounded-xl p-5 hover:border-amber-300 hover:shadow-sm transition group">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-2xl">🔑</div>
                <div>
                    <p class="font-semibold text-stone-800">Mon profil</p>
                    <p class="text-xs text-stone-500 mt-0.5">Mot de passe, email</p>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-xs text-amber-600">Gérer mon compte →</span>
            </div>
        </a>

    </div>
</div>
@endsection
