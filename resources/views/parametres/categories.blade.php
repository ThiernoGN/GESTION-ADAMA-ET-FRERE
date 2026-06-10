@extends('layouts.app')
@section('title', 'Catégories')

@section('content')
<div class="p-6">
    <div class="mb-6">
        <h2 class="text-2xl font-semibold text-stone-800">⚙️ Paramètres</h2>
        <p class="text-sm text-stone-500 mt-1">Gestion des catégories de produits</p>
    </div>

    @include('parametres.partials.nav')

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">

        {{-- Formulaire ajout --}}
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <h3 class="font-semibold text-stone-800 mb-4">➕ Nouvelle catégorie</h3>
            <form method="POST" action="{{ route('parametres.categories.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Nom *</label>
                    <input type="text" name="nom" value="{{ old('nom') }}" required
                           placeholder="ex: Eau de Parfum"
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500 @error('nom') border-red-400 @enderror">
                    @error('nom') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-stone-600 mb-1">Description</label>
                    <textarea name="description" rows="2" placeholder="Description optionnelle..."
                              class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">{{ old('description') }}</textarea>
                </div>
                <button type="submit"
                        class="w-full bg-amber-600 hover:bg-amber-700 text-white py-2 rounded-lg text-sm font-medium transition">
                    ✅ Créer la catégorie
                </button>
            </form>
        </div>

        {{-- Liste --}}
        <div class="lg:col-span-2">
            <h3 class="font-semibold text-stone-800 mb-3">🏷️ Catégories ({{ $categories->count() }})</h3>
            <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-stone-50 border-b border-stone-200">
                            <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Nom</th>
                            <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Description</th>
                            <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Produits</th>
                            <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        @forelse($categories as $cat)
                        <tr class="hover:bg-stone-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-stone-800">{{ $cat->nom }}</p>
                                <p class="text-xs text-stone-400">{{ $cat->slug }}</p>
                            </td>
                            <td class="px-4 py-3 text-stone-500 text-xs">
                                {{ $cat->description ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                    {{ $cat->produits_count }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-center gap-1">
                                    <button onclick="toggleEditCat({{ $cat->id }})"
                                            class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-600 transition" title="Modifier">
                                        ✏️
                                    </button>
                                    @if($cat->produits_count === 0)
                                    <form method="POST" action="{{ route('parametres.categories.destroy', $cat) }}"
                                          onsubmit="return confirm('Supprimer « {{ $cat->nom }} » ?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="p-1.5 rounded-lg hover:bg-red-50 text-red-500 transition" title="Supprimer">
                                            🗑️
                                        </button>
                                    </form>
                                    @else
                                    <span class="p-1.5 text-stone-300 cursor-not-allowed" title="Impossible : produits associés">🗑️</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        {{-- Formulaire édition inline --}}
                        <tr id="edit-cat-{{ $cat->id }}" class="hidden bg-amber-50">
                            <td colspan="4" class="px-4 py-3">
                                <form method="POST" action="{{ route('parametres.categories.update', $cat) }}"
                                      class="flex gap-3 items-end flex-wrap">
                                    @csrf @method('PUT')
                                    <div class="flex-1 min-w-32">
                                        <label class="block text-xs text-stone-500 mb-1">Nom</label>
                                        <input type="text" name="nom" value="{{ $cat->nom }}" required
                                               class="w-full border border-stone-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    </div>
                                    <div class="flex-1 min-w-32">
                                        <label class="block text-xs text-stone-500 mb-1">Description</label>
                                        <input type="text" name="description" value="{{ $cat->description }}"
                                               class="w-full border border-stone-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                                    </div>
                                    <div class="flex gap-2">
                                        <button type="submit"
                                                class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-1.5 rounded-lg text-xs font-medium transition">
                                            💾 Sauvegarder
                                        </button>
                                        <button type="button" onclick="toggleEditCat({{ $cat->id }})"
                                                class="border border-stone-200 px-3 py-1.5 rounded-lg text-xs text-stone-600 hover:bg-stone-50 transition">
                                            Annuler
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-stone-400 text-sm">
                                Aucune catégorie — créez-en une !
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function toggleEditCat(id) {
    document.getElementById('edit-cat-' + id).classList.toggle('hidden');
}
</script>
@endsection
