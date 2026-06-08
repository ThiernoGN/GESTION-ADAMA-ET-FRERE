<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family:Liberation Sans, sans-serif; font-size: 12px; color: #1c1917; padding: 30px; }

        .header { display: flex; justify-content: space-between; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #e7e5e4; }
        .logo    { font-size: 24px; font-weight: bold; color: #92400e; }
        .logo p  { font-size: 11px; color: #78716c; margin-top: 4px; font-weight: normal; }

        .info-box { background: #fafaf9; border: 1px solid #e7e5e4; border-radius: 6px; padding: 12px 16px; margin-bottom: 20px; }
        .info-box h4 { font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #78716c; margin-bottom: 8px; }

        .grid-2 { display: flex; gap: 20px; margin-bottom: 20px; }
        .grid-2 > div { flex: 1; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead th { background: #1c1917; color: white; padding: 9px 12px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.04em; }
        tbody tr:nth-child(even) { background: #fafaf9; }
        tbody td { padding: 9px 12px; border-bottom: 1px solid #e7e5e4; }

        tfoot td { padding: 8px 12px; }
        .total-row td { background: #fef3c7; font-weight: bold; font-size: 13px; }

        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-red   { background: #fee2e2; color: #991b1b; }
        .badge-yellow{ background: #fef9c3; color: #854d0e; }

        .footer { margin-top: 30px; padding-top: 15px; border-top: 1px solid #e7e5e4; text-align: center; font-size: 10px; color: #a8a29e; }

                
        .header {
            width: 100%;
            border-bottom: 2px solid #e7e5e4;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .left-block {
            float: left;
            width: 60%;
        }

        .right-block {
            float: right;
            width: 35%;
            text-align: right;
        }

        .logo {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #d97706;
        }

        .company-name {
            font-size: 22px;
            font-weight: bold;
            color: #92400e;
            margin-top: 8px;
        }

        .company-desc {
            font-size: 12px;
            color: #78716c;
            margin-top: 3px;
        }

        .invoice-title {
            font-size: 26px;
            font-weight: bold;
            color: #92400e;
        }

        .invoice-number {
            font-size: 14px;
            font-weight: bold;
            margin-top: 8px;
        }

        .invoice-date {
            color: #78716c;
            font-size: 12px;
            margin-top: 4px;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 11px;
            margin-top: 8px;
        }

        .badge-green {
            background: #dcfce7;
            color: #166534;
        }

        .badge-red {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-yellow {
            background: #fef9c3;
            color: #854d0e;
        }

        .clearfix::after {
            content: "";
            display: block;
            clear: both;
        }

   </style>
   
</head>
<body>

{{-- En-tête --}}
<div class="header clearfix">

    <!-- Bloc gauche -->
    <div class="left-block">
        <img
            src="file://{{ public_path('images/logo.jpeg') }}"
            alt="Logo"
            class="logo"
        >

        <div class="company-name">
            ADAMA & FRÈRE
        </div>

        <div class="company-desc">
            Votre boutique de luxe — Labé
        </div>

        <div class="company-desc">
            📞 +224 621 51 31 91
        </div>

        <div class="company-desc">
            ✉️ mamadouadama3311@gmail.com
        </div>
    </div>

    <!-- Bloc droit -->
    <div class="right-block">
        <div class="invoice-title">
            FACTURE
        </div>

        <div class="invoice-number">
            N° {{ $vente->numero }}
        </div>

        <div class="invoice-date">
            {{ $vente->created_at->format('d/m/Y à H:i') }}
        </div>

        @if($vente->statut === 'payee')
            <span class="badge badge-green">PAYÉE</span>
        @elseif($vente->statut === 'annulee')
            <span class="badge badge-red">ANNULÉE</span>
        @else
            <span class="badge badge-yellow">EN COURS</span>
        @endif
    </div>

</div>

{{-- Infos client & vendeur --}}
<div class="grid-2">
    <div class="info-box">
        <h4>Client</h4>
        @if($vente->client)
        <p><strong>{{ $vente->client->nom }} {{ $vente->client->prenom }}</strong></p>
        @if($vente->client->telephone)
        <p style="color:#78716c;">Tél : {{ $vente->client->telephone }}</p>
        @endif
        @if($vente->client->adresse)
        <p style="color:#78716c;">{{ $vente->client->adresse }}</p>
        @endif
        <p style="color:#d97706; margin-top:4px;">⭐ {{ $vente->client->points_fidelite }} points fidélité</p>
        @else
        <p style="color:#78716c; font-style:italic;">Client passager</p>
        @endif
    </div>
    <div class="info-box">
        <h4>Détails de vente</h4>
        <p><strong>Vendeur :</strong> {{ $vente->vendeur->name }}</p>
        <p style="margin-top:4px;"><strong>Paiement :</strong> {{ $vente->label_paiement }}</p>
        <p style="margin-top:4px;"><strong>Date :</strong> {{ $vente->created_at->format('d/m/Y H:i') }}</p>
    </div>
</div>

{{-- Tableau produits --}}
<table>
    <thead>
        <tr>
            <th>Produit</th>
            <th>Référence</th>
            <th style="text-align:center;">Qté</th>
            <th style="text-align:right;">Prix unit.</th>
            <th style="text-align:right;">Sous-total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($vente->lignes as $ligne)
        <tr>
            <td>
                <strong>{{ $ligne->produit->nom }}</strong><br>
                <span style="color:#78716c; font-size:11px;">{{ $ligne->produit->marque->nom }}</span>
            </td>
            <td style="color:#78716c;">{{ $ligne->produit->reference }}</td>
            <td style="text-align:center;">{{ $ligne->quantite }}</td>
            <td style="text-align:right;">{{ number_format($ligne->prix_unitaire, 0, ',', ' ') }} GNF</td>
            <td style="text-align:right; font-weight:600;">{{ number_format($ligne->sous_total, 0, ',', ' ') }} GNF</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4" style="text-align:right; color:#78716c;">Sous-total HT</td>
            <td style="text-align:right;">{{ number_format($vente->total_ht, 0, ',', ' ') }} GNF</td>
        </tr>
        @if($vente->remise > 0)
        <tr>
            <td colspan="4" style="text-align:right; color:#16a34a;">Remise accordée</td>
            <td style="text-align:right; color:#16a34a;">- {{ number_format($vente->remise, 0, ',', ' ') }} GNF</td>
        </tr>
        @endif
        <tr class="total-row">
            <td colspan="4" style="text-align:right;">TOTAL TTC</td>
            <td style="text-align:right; color:#92400e;">{{ number_format($vente->total_ttc, 0, ',', ' ') }} GNF</td>
        </tr>
    </tfoot>
</table>

<div class="footer">
    Merci de votre confiance !  Adama&Frere — mamadouadama3311@gmail.com — +224 621-51-31-91

</div>

</body>
</html>
