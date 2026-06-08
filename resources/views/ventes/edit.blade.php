@extends('layouts.app')
@section('title', 'Modifier ' . $vente->numero)

@section('content')
<div class="p-6">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('ventes.show', $vente) }}" class="text-stone-400 hover:text-stone-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-2xl font-semibold text-stone-800">Modifier — {{ $vente->numero }}</h2>
    </div>

    <form method="POST" action="{{ route('ventes.update', $vente) }}" id="form-vente">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Produits --}}
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white border border-stone-200 rounded-xl p-4">
                    <input type="text" id="search-produit" placeholder="Rechercher un produit..."
                           class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div class="bg-white border border-stone-200 rounded-xl overflow-hidden">
                    <div class="divide-y divide-stone-100 max-h-96 overflow-y-auto" id="liste-produits">
                        @foreach($produits as $produit)
                        <div class="produit-item flex items-center justify-between px-4 py-3 hover:bg-amber-50 cursor-pointer transition"
                             data-id="{{ $produit->id }}"
                             data-nom="{{ $produit->nom }}"
                             data-prix="{{ $produit->prix_vente }}"
                             data-stock="{{ $produit->stock_actuel }}"
                             data-ref="{{ $produit->reference }}"
                             onclick="ajouterProduit(this)">
                            <div>
                                <p class="text-sm font-medium text-stone-800">{{ $produit->nom }}</p>
                                <p class="text-xs text-stone-400">{{ $produit->marque->nom }} — {{ $produit->reference }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-amber-600">{{ number_format($produit->prix_vente, 0, ',', ' ') }} GNF</p>
                                <p class="text-xs text-stone-400">Stock : {{ $produit->stock_actuel }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Panier + paiement --}}
            <div class="space-y-4">
                <div class="bg-white border border-stone-200 rounded-xl p-4">
                    <label class="block text-sm font-medium text-stone-700 mb-2">Client</label>
                    <select name="client_id"
                            class="w-full border border-stone-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                        <option value="">— Client passager —</option>
                        @foreach($clients as $client)
                        <option value="{{ $client->id }}" {{ $vente->client_id == $client->id ? 'selected' : '' }}>
                            {{ $client->nom }} {{ $client->prenom }} ({{ $client->telephone }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="bg-white border border-stone-200 rounded-xl p-4">
                    <h3 class="font-medium text-stone-800 text-sm mb-3">🛒 Panier</h3>
                    <div id="panier-vide" class="text-center py-6 text-stone-400 text-sm hidden">
                        Cliquez sur un produit pour l'ajouter
                    </div>
                    <div id="panier-lignes" class="space-y-2"></div>
                </div>

                <div class="bg-white border border-stone-200 rounded-xl p-4 space-y-3">
                    <div class="flex justify-between text-sm text-stone-600">
                        <span>Sous-total</span>
                        <span id="affiche-ht">0 GNF</span>
                    </div>
                    <div class="flex items-center justify-between text-sm text-stone-600">
                        <label>Remise (GNF)</label>
                        <input type="number" name="remise" id="remise" value="{{ $vente->remise }}" min="0"
                               oninput="calculerTotal()"
                               class="w-32 border border-stone-200 rounded-lg px-2 py-1 text-sm text-right focus:outline-none focus:ring-2 focus:ring-amber-500">
                    </div>
                    <div class="border-t border-stone-100 pt-3 flex justify-between font-semibold text-stone-800">
                        <span>TOTAL TTC</span>
                        <span id="affiche-ttc" class="text-amber-600 text-lg">0 GNF</span>
                    </div>
                </div>

                <div class="bg-white border border-stone-200 rounded-xl p-4">
                    <label class="block text-sm font-medium text-stone-700 mb-3">Mode de paiement</label>
                    <div class="grid grid-cols-2 gap-2">
                        @foreach(['especes'=>'💵 Espèces','carte'=>'💳 Carte','mobile_money'=>'📱 Mobile Money','credit'=>'📋 Crédit'] as $val => $label)
                        <label class="flex items-center gap-2 border border-stone-200 rounded-lg p-2 cursor-pointer hover:border-amber-400 transition has-[:checked]:border-amber-500 has-[:checked]:bg-amber-50">
                            <input type="radio" name="mode_paiement" value="{{ $val }}"
                                   {{ $vente->mode_paiement === $val ? 'checked' : '' }}
                                   class="accent-amber-600">
                            <span class="text-sm">{{ $label }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-amber-600 hover:bg-amber-700 text-white py-3 rounded-xl font-semibold text-sm transition">
                    💾 Enregistrer les modifications
                </button>
            </div>
        </div>
    </form>
</div>

<script>
// Pré-charger les lignes existantes
let panier = {
    @foreach($vente->lignes as $ligne)
    "{{ $ligne->produit_id }}": {
        id: "{{ $ligne->produit_id }}",
        nom: "{{ $ligne->produit->nom }}",
        prix: {{ $ligne->prix_unitaire }},
        stock: {{ $ligne->produit->stock_actuel + $ligne->quantite }},
        ref: "{{ $ligne->produit->reference }}",
        quantite: {{ $ligne->quantite }}
    },
    @endforeach
};

function ajouterProduit(el) {
    const id = el.dataset.id;
    if (panier[id]) {
        if (panier[id].quantite >= panier[id].stock) {
            alert(`Stock insuffisant (max: ${panier[id].stock})`);
            return;
        }
        panier[id].quantite++;
    } else {
        panier[id] = {
            id, nom: el.dataset.nom,
            prix: parseFloat(el.dataset.prix),
            stock: parseInt(el.dataset.stock),
            ref: el.dataset.ref, quantite: 1
        };
    }
    rendrePanel();
}

function changerQuantite(id, val) {
    const q = parseInt(val);
    if (q <= 0) { supprimerLigne(id); return; }
    if (q > panier[id].stock) {
        alert(`Stock max : ${panier[id].stock}`);
        document.getElementById(`qte-${id}`).value = panier[id].quantite;
        return;
    }
    panier[id].quantite = q;
    rendrePanel();
}

function supprimerLigne(id) {
    delete panier[id];
    rendrePanel();
}

function rendrePanel() {
    const lignesDiv  = document.getElementById('panier-lignes');
    const panierVide = document.getElementById('panier-vide');
    lignesDiv.innerHTML = '';
    const ids = Object.keys(panier);

    if (ids.length === 0) {
        panierVide.classList.remove('hidden');
        calculerTotal(); return;
    }
    panierVide.classList.add('hidden');

    ids.forEach((id, index) => {
        const item = panier[id];
        const st   = item.prix * item.quantite;
        lignesDiv.innerHTML += `
            <div class="flex items-center gap-2 bg-stone-50 rounded-lg p-2">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-stone-800 truncate">${item.nom}</p>
                    <p class="text-xs text-stone-400">${item.prix.toLocaleString('fr-FR')} GNF</p>
                </div>
                <input type="number" id="qte-${id}" value="${item.quantite}" min="1" max="${item.stock}"
                       onchange="changerQuantite('${id}', this.value)"
                       class="w-14 border border-stone-200 rounded px-1 py-0.5 text-xs text-center focus:outline-none">
                <span class="text-xs font-semibold text-amber-600 w-24 text-right">
                    ${st.toLocaleString('fr-FR')} GNF
                </span>
                <button type="button" onclick="supprimerLigne('${id}')" class="text-red-400 hover:text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
                <input type="hidden" name="lignes[${index}][produit_id]" value="${id}">
                <input type="hidden" name="lignes[${index}][quantite]"   value="${item.quantite}" id="h-qte-${id}">
            </div>`;
    });
    calculerTotal();
}

function calculerTotal() {
    let ht = 0;
    Object.values(panier).forEach(i => ht += i.prix * i.quantite);
    const remise = parseFloat(document.getElementById('remise').value) || 0;
    const ttc    = Math.max(0, ht - remise);
    document.getElementById('affiche-ht').textContent  = ht.toLocaleString('fr-FR')  + ' GNF';
    document.getElementById('affiche-ttc').textContent = ttc.toLocaleString('fr-FR') + ' GNF';
}

document.getElementById('search-produit').addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('.produit-item').forEach(el => {
        el.style.display = (el.dataset.nom.toLowerCase() + el.dataset.ref.toLowerCase()).includes(q) ? '' : 'none';
    });
});

// Initialiser l'affichage au chargement
rendrePanel();
</script>
@endsection
