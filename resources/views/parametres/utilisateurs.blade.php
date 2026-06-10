@extends('layouts.app')
@section('title', 'Utilisateurs')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-stone-800">⚙️ Paramètres</h2>
        <p class="text-sm text-stone-500 mt-1">Gestion des utilisateurs et des rôles</p>
    </div>

    @include('parametres.partials.nav')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

        {{-- Formulaire ajout --}}
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <h3 class="font-semibold text-stone-800 mb-4">➕ Nouvel utilisateur</h3>
            <form method="POST" action="{{ route('parametres.utilisateurs.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Nom complet *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Mot de passe *</label>
                    <input type="password" name="password" required minlength="6"
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <p class="text-xs text-stone-400 mt-1">Minimum 6 caractères</p>
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Rôle *</label>
                    <select name="role" required
                            class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="vendeur"  {{ old('role') === 'vendeur'  ? 'selected' : '' }}>🛒 Vendeur</option>
                        <option value="caissier" {{ old('role') === 'caissier' ? 'selected' : '' }}>💰 Caissier</option>
                        <option value="admin"    {{ old('role') === 'admin'    ? 'selected' : '' }}>👑 Administrateur</option>
                    </select>
                </div>
                <button type="submit"
                        class="w-full bg-amber-600 hover:bg-amber-700 text-white py-2 rounded-lg text-sm font-medium transition">
                    ✅ Créer l'utilisateur
                </button>
            </form>
        </div>

        {{-- Liste utilisateurs --}}
        <div class="lg:col-span-2 space-y-3">
            <h3 class="font-semibold text-stone-800">👥 Utilisateurs ({{ $utilisateurs->count() }})</h3>
            @foreach($utilisateurs as $u)
            <div class="bg-white border border-stone-200 rounded-xl p-4 {{ !$u->actif ? 'opacity-60' : '' }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm
                            {{ $u->role === 'admin' ? 'bg-amber-100 text-amber-700' :
                               ($u->role === 'vendeur' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                            {{ strtoupper(substr($u->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-stone-800 text-sm">{{ $u->name }}</p>
                            <p class="text-xs text-stone-400">{{ $u->email }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                    {{ $u->role === 'admin' ? 'bg-amber-100 text-amber-700' :
                                       ($u->role === 'vendeur' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700') }}">
                                    {{ $u->role === 'admin' ? '👑 Admin' : ($u->role === 'vendeur' ? '🛒 Vendeur' : '💰 Caissier') }}
                                </span>
                                @if(!$u->actif)
                                <span class="px-2 py-0.5 rounded-full text-xs bg-red-100 text-red-600">Désactivé</span>
                                @endif
                                @if($u->id === auth()->id())
                                <span class="px-2 py-0.5 rounded-full text-xs bg-stone-100 text-stone-500">Moi</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center gap-1 shrink-0">
                        {{-- Toggle actif --}}
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('parametres.utilisateurs.toggle', $u) }}">
                            @csrf
                            <button type="submit"
                                    class="p-1.5 rounded-lg text-xs transition
                                           {{ $u->actif ? 'hover:bg-red-50 text-red-500' : 'hover:bg-green-50 text-green-500' }}"
                                    title="{{ $u->actif ? 'Désactiver' : 'Activer' }}">
                                {{ $u->actif ? '🔒' : '🔓' }}
                            </button>
                        </form>
                        @endif

                        {{-- Modifier --}}
                        <button onclick="toggleEdit({{ $u->id }})"
                                class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-600 transition" title="Modifier">
                            ✏️
                        </button>

                        {{-- Supprimer --}}
                        @if($u->id !== auth()->id())
                        <form method="POST" action="{{ route('parametres.utilisateurs.destroy', $u) }}"
                              onsubmit="return confirm('Supprimer {{ $u->name }} ?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                    class="p-1.5 rounded-lg hover:bg-red-50 text-red-500 transition" title="Supprimer">
                                🗑️
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                {{-- Formulaire modification (inline caché) --}}
                <div id="edit-{{ $u->id }}" class="hidden mt-4 pt-4 border-t border-stone-100">
                    <form method="POST" action="{{ route('parametres.utilisateurs.update', $u) }}"
                          class="grid grid-cols-2 gap-3">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-xs text-stone-500 mb-1">Nom</label>
                            <input type="text" name="name" value="{{ $u->name }}" required
                                   class="w-full border border-stone-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs text-stone-500 mb-1">Email</label>
                            <input type="email" name="email" value="{{ $u->email }}" required
                                   class="w-full border border-stone-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div>
                            <label class="block text-xs text-stone-500 mb-1">Rôle</label>
                            <select name="role"
                                    class="w-full border border-stone-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                                <option value="vendeur"  {{ $u->role === 'vendeur'  ? 'selected' : '' }}>🛒 Vendeur</option>
                                <option value="caissier" {{ $u->role === 'caissier' ? 'selected' : '' }}>💰 Caissier</option>
                                <option value="admin"    {{ $u->role === 'admin'    ? 'selected' : '' }}>👑 Admin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-stone-500 mb-1">Nouveau mot de passe</label>
                            <input type="password" name="password" placeholder="Laisser vide = inchangé" minlength="6"
                                   class="w-full border border-stone-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="actif" id="actif-{{ $u->id }}"
                                   {{ $u->actif ? 'checked' : '' }} class="accent-amber-600">
                            <label for="actif-{{ $u->id }}" class="text-xs text-stone-600">Compte actif</label>
                        </div>
                        <div class="flex gap-2 col-span-2">
                            <button type="submit"
                                    class="flex-1 bg-amber-600 hover:bg-amber-700 text-white py-1.5 rounded-lg text-xs font-medium transition">
                                💾 Enregistrer
                            </button>
                            <button type="button" onclick="toggleEdit({{ $u->id }})"
                                    class="px-3 border border-stone-200 rounded-lg text-xs text-stone-600 hover:bg-stone-50 transition">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>

    </div>
</div>

<script>
function toggleEdit(id) {
    const el = document.getElementById('edit-' + id);
    el.classList.toggle('hidden');
}
</script>
@endsection
