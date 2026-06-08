@extends('layouts.app')
@section('title', $fournisseur->nom)

@section('content')
<div class="p-6 max-w-5xl mx-auto">

    {{-- En-tête --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('fournisseurs.index') }}" class="text-stone-400 hover:text-stone-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-full bg-stone-100 flex items-center justify-center text-stone-600 font-bold text-xl">
                    {{ strtoupper(substr($fournisseur->nom, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-2xl font-semibold text-stone-800">{{ $fournisseur->nom }}</h2>
                    <p class="text-sm text-stone-500">Fournisseur depuis {{ $fournisseur->created_at->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('fournisseurs.edit', $fournisseur) }}"
               class="inline-flex items-center gap-2 border border-amber-300 text-amber-700 hover:bg-amber-50 px-3 py-2 rounded-lg text-sm transition">
                Modifier
            </a>
            @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('fournisseurs.destroy', $fournisseur) }}"
                  onsubmit="return confirm('Supprimer ce fournisseur ?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="border border-red-200 text-red-600 hover:bg-red-50 px-3 py-2 rounded-lg text-sm transition">
                    Supprimer
                </button>
            </form>
            @endif
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Total commandé</p>
            <p class="text-2xl font-bold text-amber-600 mt-1">
                {{ number_format($fournisseur->total_commandes, 0, ',', ' ') }} GNF
            </p>
        </div>
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Nombre commandes</p>
            <p class="text-2xl font-bold text-stone-800 mt-1">{{ $fournisseur->nombre_commandes }}</p>
        </div>
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <p class="text-xs text-stone-500 uppercase tracking-wide">Dernière commande</p>
            <p class="text-lg font-bold text-stone-800 mt-1">
                {{ $fournisseur->derniere_commande?->format('d/m/Y') ?? '—' }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">

        {{-- Infos fournisseur --}}
        <div class="bg-white border border-stone-200 rounded-xl p-5">
            <h3 class="font-medium text-stone-800 mb-4">Coordonnées</h3>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-stone-500 mb-0.5">Téléphone</dt>
                    <dd class="font-medium text-stone-800">{{ $fournisseur->telephone ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-stone-500 mb-0.5">Email</dt>
                    <dd class="font-medium text-stone-800">{{ $fournisseur->email ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-stone-500 mb-0.5">Adresse</dt>
                    <dd class="font-medium text-stone-800">{{ $fournisseur->adresse ?? '—' }}</dd>
                </div>
            </dl>
        </div>

        {{-- Nouvelle commande --}}
        <div class="md:col-span-2 bg-white border border-stone-200 rounded-xl p-5">
            <h3 class="font-medium text-stone-800 mb-4">📦 Nouvelle commande fournisseur</h3>

            <form method="POST"
                  action="{{ route('fournisseurs.commande.store', $fournisseur) }}"
                  id="form-commande">
                @csrf

                <div id="lignes-commande" class="space-y-2 mb-3">
                    <div class="flex gap-2 items-center ligne-commande">
                        <select name="lignes[0][produit_id]" required
                                class="flex-1 border border-stone-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                            <option value="">Choisir un produit</option>
                            @foreach($produits as $p)
                            <option value="{{ $p->id }}">{{ $p->nom }} ({{ $p->reference }})</option>
                            @endforeach
                        </select>
                        <input type="number" name="lignes[0][quantite]" placeholder="Qté" min="1" required
                               class="w-20 border border-stone-200 rounded-lg px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <input type="number" name="lignes[0][prix_unitaire]" placeholder="Prix GNF" min="0" required
                               class="w-32 border border-stone-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                </div>

                <button type="button" onclick="ajouterLigne()"
                        class="text-amber-600 hover:text-amber-700 text-sm flex items-center gap-1 mb-4">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Ajouter un produit
                </button>

                <div class="flex gap-3 items-end">
                    <div class="flex-1">
                        <label class="block text-xs text-stone-500 mb-1">Date livraison prévue</label>
                        <input type="date" name="date_livraison_prevue"
                               class="w-full border border-stone-200 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <button type="submit"
                            class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
                        ✅ Commander
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Historique commandes --}}
    <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
        <div class="p-4 border-b border-stone-100">
            <h3 class="font-medium text-stone-800">
                Historique des commandes
                <span class="text-xs font-normal text-stone-400 ml-2">({{ $fournisseur->commandes->count() }} commandes)</span>
            </h3>
        </div>

        @if($fournisseur->commandes->isEmpty())
        <div class="p-8 text-center text-stone-400">
            <p class="text-sm">Aucune commande passée à ce fournisseur</p>
        </div>
        @else
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-stone-50 border-b border-stone-100">
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">ID</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Produits</th>
                    <th class="text-right px-4 py-3 text-xs font-medium text-stone-500 uppercase">Total</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Statut</th>
                    <th class="text-left px-4 py-3 text-xs font-medium text-stone-500 uppercase">Livraison prévue</th>
                    <th class="text-center px-4 py-3 text-xs font-medium text-stone-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-stone-100">
                @foreach($fournisseur->commandes->sortByDesc('created_at') as $commande)
                <tr class="hover:bg-stone-50 transition">
                    <td class="px-4 py-3 font-mono text-stone-500 text-xs">#{{ $commande->id }}</td>
                    <td class="px-4 py-3 text-stone-600 text-xs">
                        @foreach($commande->lignes as $ligne)
                        <div>{{ $ligne->produit->nom }} × {{ $ligne->quantite }}</div>
                        @endforeach
                    </td>
                    <td class="px-4 py-3 text-right font-semibold text-amber-600">
                        {{ number_format($commande->total, 0, ',', ' ') }} GNF
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($commande->statut === 'recue')
                            <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">✅ Reçue</span>
                        @elseif($commande->statut === 'annulee')
                            <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">❌ Annulée</span>
                        @else
                            <span class="px-2 py-1 rounded-full text-xs bg-yellow-100 text-yellow-700">⏳ En attente</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-stone-500 text-xs">
                        {{ $commande->date_livraison_prevue
                            ? \Carbon\Carbon::parse($commande->date_livraison_prevue)->format('d/m/Y')
                            : '—' }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        @if($commande->statut === 'en_attente')
                        <form method="POST"
                              action="{{ route('fournisseurs.commande.recevoir', [$fournisseur, $commande]) }}"
                              onsubmit="return confirm('Confirmer la réception ? Le stock sera mis à jour.')">
                            @csrf
                            <button type="submit"
                                    class="text-xs bg-green-100 hover:bg-green-200 text-green-700 px-3 py-1 rounded-full transition">
                                Marquer reçue
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

</div>

<script>
let index = 1;
const produitOptions = `@foreach($produits as $p)<option value="{{ $p->id }}">{{ $p->nom }} ({{ $p->reference }})</option>@endforeach`;

function ajouterLigne() {
    const div = document.createElement('div');
    div.className = 'flex gap-2 items-center ligne-commande';
    div.innerHTML = `
        <select name="lignes[${index}][produit_id]" required
                class="flex-1 border border-stone-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
            <option value="">Choisir un produit</option>
            ${produitOptions}
        </select>
        <input type="number" name="lignes[${index}][quantite]" placeholder="Qté" min="1" required
               class="w-20 border border-stone-200 rounded-lg px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-amber-500">
        <input type="number" name="lignes[${index}][prix_unitaire]" placeholder="Prix GNF" min="0" required
               class="w-32 border border-stone-200 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
        <button type="button" onclick="this.parentElement.remove()"
                class="text-red-400 hover:text-red-600 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    `;
    document.getElementById('lignes-commande').appendChild(div);
    index++;
}
</script>
@endsection
