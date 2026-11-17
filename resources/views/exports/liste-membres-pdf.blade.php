<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>{{ $titre }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 12mm 10mm;
        }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            font-size: 9px;
            color: #111827;
            margin: 0;
        }

        .header-table {
            width: 100%;
            border-bottom: 3px solid #009CFE;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: middle;
        }

        .org-title {
            font-size: 14px;
            font-weight: bold;
            color: #009CFE;
            margin: 0;
        }

        .doc-title {
            font-size: 11px;
            font-weight: bold;
            margin: 3px 0 0 0;
            text-transform: uppercase;
            color: #1F2937;
        }

        .meta {
            text-align: right;
            font-size: 8.5px;
            color: #4B5563;
            line-height: 1.4;
        }

        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.data-table th {
            background-color: #009CFE;
            color: #000000;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 4px;
            border: 1px solid #6B7280;
            font-size: 8px;
        }

        table.data-table td {
            padding: 5px 4px;
            border: 1px solid #D1D5DB;
            font-size: 8px;
            word-wrap: break-word;
        }

        table.data-table tr:nth-child(even) {
            background-color: #F3F4F6;
        }

        .center {
            text-align: center;
        }

        .footer {
            margin-top: 12px;
            text-align: right;
            font-size: 8px;
            color: #6B7280;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td style="width: 60px;">
                @if(file_exists(public_path('images/logo.png')))
                <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height: 46px; width: auto;">
                @endif
            </td>
            <td>
                <p class="org-title">D.M.W.M / COMITÉ TAMBA</p>
                <p class="doc-title">{{ $titre }}</p>
            </td>
            <td class="meta">
                <div><strong>Date d'édition :</strong> {{ now()->format('d/m/Y à H:i') }}</div>
                <div><strong>Édité par :</strong> {{ auth()->user()?->name ?? 'Système' }}</div>
                <div><strong>Effectif total :</strong> {{ $records->count() }} personne(s)</div>
            </td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th class="center" style="width: 22px;">N°</th>
                <th class="center">Matricule</th>
                <th>Prénom(s)</th>
                <th>Nom</th>
                <th class="center">Genre</th>
                <th class="center">Âge</th>
                <th>Conseil</th>
                <th>Section</th>
                <th class="center">Téléphone</th>
                <th>Profession</th>
                <th>Adresse</th>
                <th>Pièce d'identité</th>
                <th>Proche & Contact</th>
            </tr>
        </thead>
        <tbody>
            @foreach($records as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td class="center"><strong>{{ $item->internal_id }}</strong></td>
                <td>{{ $item->Prenom }}</td>
                <td><strong>{{ $item->Nom }}</strong></td>
                <td class="center">{{ $item->Genre }}</td>
                <td class="center">{{ $item->Age ? $item->Age . ' ans' : '-' }}</td>
                <td>{{ $item->Conseil }}</td>
                <td>{{ $item->Section }}</td>
                <td class="center">{{ $item->Telephone }}</td>
                <td>{{ $item->Profession }}</td>
                <td>{{ $item->Adresse }}</td>
                <td>{{ $item->Type_piece_identite }} ({{ $item->Numero_piece_identite }})</td>
                <td>{{ $item->Parent }} - {{ $item->Contact }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Document officiel généré depuis la plateforme D.M.W.M / COMITÉ TAMBA
    </div>
</body>

</html>