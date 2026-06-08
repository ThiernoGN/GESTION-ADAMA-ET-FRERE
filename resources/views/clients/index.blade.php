@extends('layouts.app')
@section('title', 'Clients')

@section('content')
<div class="p-6">

    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-stone-800">Clients</h2>
            <p class="text-sm text-stone-500 mt-1">Gestion de la clientèle et programme fidélité</p>
        </div>
        <a href="{{ route('clients.create') }}"
           class="inline-flex items-center gap-2 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Nouveau client
        </a>
    </div>

    {{-- Filtres --}}
    <div class="bg-white border border-stone-200 rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('clients.index') }}" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Nom, prénom, téléphone, email..."
                   class="border border-stone-200 rounded-lg px-3 py-2 text-sm flex-1 min-w-48 focus:outline-none focus:ring-2 focus:ring-amber-500">

            <select name="fidelite"
                    class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                <option value="">Tous les clients</option>
                <option value="oui" {{ request('fidelite') === 'oui' ? 'selected' : '' }}>Avec points fidélité</option>
            </select>

            <button type="submit"
                    class="bg-stone-800 hover:bg-stone-900 text-white px-4 py-2 rounded-lg text-sm transition">
                Filtrer
            </button>

            @if(request()->hasAny(['search', 'fidelite']))
            <a href="{{ route('clients.index') }}"
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
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Client</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Téléphone</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Email</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Ventes</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Total achats</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Points</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($clients as $client)
                <tr class="hover:bg-stone-50 transition">

                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center text-amber-700 font-semibold text-sm">
                                {{ strtoupper(substr($client->nom, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-stone-800">{{ $client->nom_complet }}</p>
                                @if($client->adresse)
                                <p class="text-xs text-stone-400">{{ $client->adresse }}</p>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td class="px-4 py-3 text-stone-600">{{ $client->telephone ?? '—' }}</td>
                    <td class="px-4 py-3 text-stone-600">{{ $client->email ?? '—' }}</td>

                    <td class="px-4 py-3 text-center font-medium text-stone-800">
                        {{ $client->ventes_count ?? 0 }}
                    </td>

                    <td class="px-4 py-3 text-right font-semibold text-amber-600">
                        {{ number_format($client->ventes_sum_total_ttc ?? 0, 0, ',', ' ') }} GNF
                    </td>

                    <td class="px-4 py-3 text-center">
                        @if($client->points_fidelite > 0)
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700">
                            ⭐ {{ $client->points_fidelite }}
                        </span>
                        @else
                        <span class="text-stone-400 text-xs">0</span>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('clients.show', $client) }}"
                               class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-500 transition" title="Voir">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('clients.edit', $client) }}"
                               class="p-1.5 rounded-lg hover:bg-amber-50 text-amber-600 transition" title="Modifier">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @if(auth()->user()->isAdmin())
                            <form method="POST" action="{{ route('clients.destroy', $client) }}"
                                  onsubmit="return confirm('Supprimer ce client ?')">
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
                    <td colspan="7" class="px-4 py-12 text-center text-stone-400">
                        <div class="text-4xl mb-2">👥</div>
                        <p class="text-sm">Aucun client trouvé</p>
                        <a href="{{ route('clients.create') }}" class="text-amber-600 text-sm mt-1 inline-block">
                            Ajouter un client →
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($clients->hasPages())
        <div class="px-4 py-3 border-t border-stone-100">
            {{ $clients->links() }}
        </div>
        @endif
    </div>

    <div class="mt-3 text-sm text-stone-400 text-right">
        {{ $clients->total() }} client(s) trouvé(s)
    </div>
</div>
@endsection
