<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Carte de Membre - {{ $membre->internal_id }}</title>
    <style>
        @page {
            size: 85.6mm 54mm;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: DejaVu Sans, Arial, sans-serif;
            background-color: #ffffff;
            color: #111827;
        }

        .card {
            width: 85.6mm;
            height: 54mm;
            position: relative;
            overflow: hidden;
            box-sizing: border-box;
            border: 0.5pt solid #CBD5E1;
            background: #F8FAFC;
        }

        /* Bandeau supérieur bleu officiel */
        .header {
            background-color: #009CFE;
            color: #ffffff;
            padding: 2.2mm 3.5mm;
            height: 9.5mm;
            box-sizing: border-box;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: middle;
            padding: 0;
        }

        .org-name {
            font-size: 7.5pt;
            font-weight: bold;
            letter-spacing: 0.3px;
            margin: 0;
        }

        .card-subtitle {
            font-size: 5.5pt;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #E0F2FE;
            margin: 0.5mm 0 0 0;
        }

        .matricule-badge {
            background-color: #ffffff;
            color: #0284C7;
            font-size: 6.5pt;
            font-weight: bold;
            padding: 1mm 2mm;
            border-radius: 2mm;
            text-align: center;
        }

        /* Corps de la carte */
        .body-table {
            width: 100%;
            border-collapse: collapse;
            padding: 2.5mm 3.5mm;
        }

        .photo-cell {
            width: 21mm;
            vertical-align: top;
            padding-top: 2.5mm;
            padding-left: 3.5mm;
        }

        .photo-box {
            width: 19mm;
            height: 23mm;
            border: 1pt solid #009CFE;
            border-radius: 2mm;
            background-color: #E0F2FE;
            text-align: center;
            vertical-align: middle;
            overflow: hidden;
        }

        .initials {
            font-size: 13pt;
            font-weight: bold;
            color: #0284C7;
            line-height: 23mm;
        }

        .info-cell {
            vertical-align: top;
            padding: 2.2mm 3.5mm 0 2.5mm;
        }

        .member-name {
            font-size: 8.5pt;
            font-weight: bold;
            color: #0F172A;
            text-transform: uppercase;
            margin: 0 0 1.5mm 0;
            border-bottom: 0.5pt solid #CBD5E1;
            padding-bottom: 1mm;
        }

        .field-row {
            font-size: 6.2pt;
            margin-bottom: 1.1mm;
            color: #334155;
        }

        .field-label {
            font-weight: bold;
            color: #64748B;
            text-transform: uppercase;
            font-size: 5.5pt;
        }

        .field-value {
            font-weight: bold;
            color: #0F172A;
        }

        .pill-section {
            display: inline-block;
            background-color: #DCFCE7;
            color: #166534;
            padding: 0.4mm 1.5mm;
            border-radius: 1mm;
            font-weight: bold;
            font-size: 6pt;
        }

        /* Pied de carte */
        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 6.5mm;
            background-color: #0F172A;
            color: #F8FAFC;
            padding: 1.2mm 3.5mm;
            box-sizing: border-box;
            font-size: 5.2pt;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }
    </style>
</head>

<body>
    <div class="card">
        {{-- En-tête de la carte --}}
        <div class="header">
            <table class="header-table">
                <tr>
                    <td style="width: 8mm;">
                        @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height: 6.5mm; width: auto; background: #fff; border-radius: 1mm; padding: 0.3mm;">
                        @endif
                    </td>
                    <td>
                        <p class="org-name">D.M.W.M / COMITÉ TAMBA</p>
                        <p class="card-subtitle">Carte Officielle de Membre</p>
                    </td>
                    <td style="text-align: right;">
                        <span class="matricule-badge">{{ $membre->internal_id }}</span>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Contenu principal : Photo / Initiales + Informations --}}
        <table class="body-table">
            <tr>
                <td class="photo-cell">
                    <div class="photo-box">
                        <span class="initials">
                            {{ mb_strtoupper(mb_substr($membre->Prenom ?? '', 0, 1) . mb_substr($membre->Nom ?? '', 0, 1)) }}
                        </span>
                    </div>
                </td>
                <td class="info-cell">
                    <p class="member-name">{{ $membre->Prenom }} {{ $membre->Nom }}</p>

                    <div class="field-row">
                        <span class="field-label">Conseil :</span>
                        <span class="field-value">{{ $membre->Conseil }}</span>
                    </div>

                    <div class="field-row">
                        <span class="field-label">Section :</span>
                        <span class="pill-section">{{ $membre->Section }}</span>
                    </div>

                    <div class="field-row">
                        <span class="field-label">Profession :</span>
                        <span class="field-value">{{ $membre->Profession ?? 'Non renseignée' }}</span>
                    </div>

                    <div class="field-row">
                        <span class="field-label">Téléphone :</span>
                        <span class="field-value">{{ $membre->Telephone ?? '-' }}</span>
                    </div>

                    <div class="field-row">
                        <span class="field-label">Adresse :</span>
                        <span class="field-value">{{ \Illuminate\Support\Str::limit($membre->Adresse ?? '-', 28) }}</span>
                    </div>
                </td>
            </tr>
        </table>

        {{-- Pied de carte --}}
        <div class="footer">
            <table class="footer-table">
                <tr>
                    <td>Délivrée le {{ now()->format('d/m/Y') }}</td>
                    <td style="text-align: right; color: #38BDF8; font-weight: bold;">MEMBRE ACTIF • TAMBACOUNDA</td>
                </tr>
            </table>
        </div>
    </div>
</body>

</html>