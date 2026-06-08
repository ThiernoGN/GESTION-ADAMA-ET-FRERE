<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1a1a1a; }
    .header { display: flex; justify-content: space-between; margin-bottom: 30px; }
    .logo { font-size: 22px; font-weight: bold; color: #92400e; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    th { background: #1c1917; color: white; padding: 8px; text-align: left; }
    td { padding: 7px 8px; border-bottom: 1px solid #e7e5e4; }
    .total-row td { font-weight: bold; background: #fef3c7; }
    .footer { margin-top: 40px; font-size: 10px; color: #78716c; text-align: center; }
</style>
</head>
<body>
<div class="header">
    <div>
        <div class="logo">🌸 Parfumerie</div>
        <p style="color:#78716c;margin-top:4px">Votre boutique de luxe</p>
    </div>
    <div style="text-align:right">
        <p><strong>Facture N° {{ $vente->numero }}</strong></p>
        <p>Date : {{ $vente->created_at->format('d/m/Y H:i') }}</p>
        <p>Vendeur : {{ $vente->vendeur->name }}</p>
    </div>
</div>

@if($vente->client)
<div style="background:#f5f5f4;padding:10px;border-radius:6px;margin-bottom:20px">
    <strong>Client :</strong> {{ $vente->client->nom }} {{ $vente->client->prenom }}<br>
    <strong>Tél :</strong> {{ $vente->client->telephone ?? 'N/A' }}
</div>
@endif

<table>
    <thead>
        <tr>
            <th>Produit</th>
            <th>Qté</th>
            <th>Prix unit.</th>
            <th>Sous-total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vente->lignes as $ligne)
        <tr>
            <td>{{ $ligne->produit->nom }}</td>
            <td>{{ $ligne->quantite }}</td>
            <td>{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} GNF</td>
            <td>{{ number_format($ligne->sous_total, 0, ',', ' ') }} GNF</td>
        </tr>
        @endforeach
        @if($vente->remise > 0)
        <tr><td colspan="3" style="text-align:right">Remise</td>
            <td>- {{ number_format($vente->remise, 0, ',', ' ') }} GNF</td></tr>
        @endif
        <tr class="total-row">
            <td colspan="3" style="text-align:right">TOTAL TTC</td>
            <td>{{ number_format($vente->total_ttc, 0, ',', ' ') }} GNF</td>
        </tr>
    </tbody>
</table>

<p><strong>Mode de paiement :</strong> {{ strtoupper($vente->mode_paiement) }}</p>
<div class="footer">Merci de votre confiance — parfumerie@example.com</div>
</body>
</html>