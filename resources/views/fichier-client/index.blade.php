@extends('layouts.app')
@section('title', 'Fichier Clients')

@section('content')
<div class="p-6">

    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-stone-800">📂 Fichier Clients</h2>
            <p class="text-sm text-stone-500 mt-1">Situation d'achat, paiement et solde par client</p>
        </div>
    </div>

    {{-- Stats globales --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
        <div class="bg-white border border-stone-200 rounded-xl p-4">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Clients</p>
            <p class="text-2xl font-bold text-stone-800 mt-1">{{ $stats['total_clients'] }}</p>
        </div>
        <div class="bg-white border border-stone-200 rounded-xl p-4">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Total Achats</p>
            <p class="text-xl font-bold text-stone-800 mt-1">
                {{ number_format($stats['total_achats'], 0, ',', ' ') }} GNF
            </p>
        </div>
        <div class="bg-white border border-green-200 bg-green-50 rounded-xl p-4">
            <p class="text-xs text-green-600 uppercase tracking-wide">Total Payé</p>
            <p class="text-xl font-bold text-green-600 mt-1">
                {{ number_format($stats['total_paye'], 0, ',', ' ') }} GNF
            </p>
        </div>
        <div class="bg-white border border-red-200 bg-red-50 rounded-xl p-4">
            <p class="text-xs text-red-500 uppercase tracking-wide">Total Reste</p>
            <p class="text-xl font-bold text-red-600 mt-1">
                {{ number_format($stats['total_reste'], 0, ',', ' ') }} GNF
            </p>
        </div>
        <div class="bg-white border border-orange-200 bg-orange-50 rounded-xl p-4">
            <p class="text-xs text-orange-500 uppercase tracking-wide">Clients avec solde</p>
            <p class="text-2xl font-bold text-orange-600 mt-1">{{ $stats['clients_solde'] }}</p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="bg-white border border-stone-200 rounded-xl p-4 mb-6">
        <form method="GET" action="{{ route('fichier-client.index') }}" class="flex flex-wrap gap-3 items-end">

            <div class="flex-1 min-w-48">
                <label class="block text-xs text-stone-500 mb-1">Rechercher</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Nom, prénom, téléphone..."
                       class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-xs text-stone-500 mb-1">Situation</label>
                <select name="situation"
                        class="border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <option value="">Tous les clients</option>
                    <option value="actif"      {{ request('situation') === 'actif'      ? 'selected' : '' }}>Clients actifs</option>
                    <option value="solde"      {{ request('situation') === 'solde'      ? 'selected' : '' }}>Avec solde impayé</option>
                    <option value="solde_zero" {{ request('situation') === 'solde_zero' ? 'selected' : '' }}>Tout payé</option>
                </select>
            </div>

            <button type="submit"
                    class="bg-stone-800 hover:bg-stone-900 text-white px-4 py-2 rounded-lg text-sm transition">
                Filtrer
            </button>

            @if(request()->hasAny(['search', 'situation']))
            <a href="{{ route('fichier-client.index') }}"
               class="border border-stone-200 text-stone-600 hover:bg-stone-50 px-4 py-2 rounded-lg text-sm transition">
                Réinitialiser
            </a>
            @endif

        </form>
    </div>

    {{-- Tableau clients --}}
    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-stone-50 border-b border-stone-200">
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Client</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Ventes</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Total Achats</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Total Payé</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Solde Réel</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Dernière vente</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @forelse($clients as $client)
                <tr class="hover:bg-stone-50 transition {{ $client->total_reste > 0 ? 'bg-red-50/30' : '' }}">

                    {{-- Client --}}
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center font-semibold text-sm
                                {{ $client->total_reste > 0 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' }}">
                                {{ strtoupper(substr($client->nom, 0, 1)) }}
                            </div>
                            <div>
                                <p class="font-medium text-stone-800">{{ $client->nom }} {{ $client->prenom }}</p>
                                <p class="text-xs text-stone-400">{{ $client->telephone ?? $client->email ?? '—' }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Nb ventes --}}
                    <td class="px-4 py-3 text-center">
                        <span class="px-2 py-1 rounded-full text-xs font-medium bg-stone-100 text-stone-700">
                            {{ $client->nb_ventes }}
                        </span>
                        @if($client->nb_en_cours > 0)
                        <span class="ml-1 px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-700">
                            {{ $client->nb_en_cours }} en cours
                        </span>
                        @endif
                    </td>

                    {{-- Total achats --}}
                    <td class="px-4 py-3 text-right font-medium text-stone-800">
                        {{ number_format($client->total_achats, 0, ',', ' ') }} GNF
                    </td>

                    {{-- Total payé --}}
                    <td class="px-4 py-3 text-right font-medium text-green-600">
                        {{ number_format($client->total_paye, 0, ',', ' ') }} GNF
                    </td>

                    {{-- Solde réel --}}
                    <td class="px-4 py-3 text-right">
                        @if($client->total_reste > 0)
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-700">
                                {{ number_format($client->total_reste, 0, ',', ' ') }} GNF
                            </span>
                        @else
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                ✅ Soldé
                            </span>
                        @endif
                    </td>

                    {{-- Dernière vente --}}
                    <td class="px-4 py-3 text-stone-500 text-xs">
                        @if($client->derniere_vente)
                            {{ $client->derniere_vente->created_at->format('d/m/Y') }}<br>
                            <span class="text-stone-400">{{ $client->derniere_vente->numero }}</span>
                        @else
                            <span class="text-stone-300">—</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('fichier-client.show', $client) }}"
                           class="inline-flex items-center gap-1 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-lg text-xs font-medium transition">
                            📂 Fiche
                        </a>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-12 text-center text-stone-400">
                        <div class="text-4xl mb-2">👥</div>
                        <p class="text-sm">Aucun client trouvé</p>
                    </td>
                </tr>
                @endforelse
            </tbody>

            {{-- Totaux --}}
            @if($clients->count() > 0)
            <tfoot class="border-t-2 border-stone-200 bg-stone-50">
                <tr>
                    <td colspan="2" class="px-4 py-3 font-bold text-stone-800">
                        TOTAUX — {{ $clients->count() }} client(s)
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-stone-800">
                        {{ number_format($stats['total_achats'], 0, ',', ' ') }} GNF
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-green-600">
                        {{ number_format($stats['total_paye'], 0, ',', ' ') }} GNF
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-red-600">
                        {{ number_format($stats['total_reste'], 0, ',', ' ') }} GNF
                    </td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif

        </table>
    </div>
</div>
@endsection
