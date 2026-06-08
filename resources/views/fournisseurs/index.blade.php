@extends('layouts.app')
@section('title', 'Fournisseurs')

@section('content')
<div class="p-6">

    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-stone-800">Fournisseurs</h2>
            <p class="text-sm text-stone-500 mt-1">Gestion des fournisseurs et commandes</p>
        </div>
        <a href="{{ route('fournisseurs.create') }}"
           class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau fournisseur
        </a>
    </div>

    {{-- Filtres --}}
    <div class="bg-white border border-stone-200 rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('fournisseurs.index') }}" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nom, téléphone, email..."
                   class="border border-stone-200 rounded-lg px-3 py-2 text-sm flex-1 min-w-48 focus:outline-none focus:ring-2 focus:ring-amber-500">
            <button type="submit"
                    class="bg-stone-800 hover:bg-stone-900 text-white px-4 py-2 rounded-lg text-sm transition">
                Filtrer
            </button>
            @if(request('search'))
            <a href="{{ route('fournisseurs.index') }}"
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
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Fournisseur</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Téléphone</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Email</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Commandes</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Total commandé</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($fournisseurs as $fournisseur)
                <tr class="hover:bg-stone-50 transition">

                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-stone-100 flex items-center justify-center text-stone-600 font-semibold text-sm">
                                {{ strtoupper(substr($fournisseur->nom, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-stone-800">{{ $fournisseur->nom }}</p>
                                @if($fournisseur->adresse)
                                <p class="text-xs text-stone-400">{{ $fournisseur->adresse }}</p>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td class="px-4 py-3 text-stone-600">{{ $fournisseur->telephone ?? '—' }}</td>
                    <td class="px-4 py-3 text-stone-600">{{ $fournisseur->email ?? '—' }}</td>

                    <td class="px-4 py-3 text-center font-medium text-stone-800">
                        {{ $fournisseur->commandes_count ?? 0 }}
                    </td>

                    <td class="px-4 py-3 text-right font-semibold text-amber-600">
                        {{ number_format($fournisseur->commandes_sum_total ?? 0, 0, ',', ' ') }} GNF
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('fournisseurs.show', $fournisseur) }}"
                               class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-500 transition" title="Voir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('fournisseurs.edit', $fournisseur) }}"
                               class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-600 transition" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @if(auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('fournisseurs.destroy', $fournisseur) }}"
                                  onsubmit="return confirm('Supprimer ce fournisseur ?')">
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
                    <td colspan="6" class="px-4 py-12 text-center text-stone-400">
                        <div class="text-4xl mb-2">🚚</div>
                        <p class="text-sm">Aucun fournisseur trouvé</p>
                        <a href="{{ route('fournisseurs.create') }}" class="text-amber-600 text-sm mt-1 inline-block">
                            Ajouter un fournisseur →
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($fournisseurs->hasPages())
        <div class="px-4 py-3 border-t border-stone-100">
            {{ $fournisseurs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
