<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        @page {
            margin: 10mm;
            size: A4 landscape;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #3f3f46;
            background: #fffdfb;
        }

        /* Wrapper ocupa exatamente a area util da pagina (297 - 20mm x 210 - 20mm) */
        .page {
            display: table;
            width: 277mm;
            height: 190mm;
            border: 2.5px solid #e9b8b8;
        }

        .page-inner {
            display: table;
            width: 100%;
            height: 100%;
            border: 1px solid #f4d0d0;
            margin: 5px;
            width: calc(100% - 10px);
            height: calc(100% - 10px);
        }

        /* Celula central para alinhar verticalmente */
        .content-cell {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 0 25mm;
        }

        .eyebrow {
            font-size: 10px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #78716c;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .org-name {
            font-size: 26px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 6px;
            letter-spacing: 0.02em;
        }

        .divider {
            width: 60mm;
            height: 2px;
            background: #e9b8b8;
            margin: 8px auto;
        }

        .certifies-text {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 6px;
        }

        .member-name {
            font-size: 36px;
            font-weight: 700;
            color: #1657b8;
            line-height: 1.1;
            margin-bottom: 8px;
        }

        .concluded-text {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 4px;
        }

        .formation-title {
            font-size: 20px;
            font-weight: 700;
            color: #2d23b6;
            line-height: 1.2;
            margin-bottom: 4px;
        }

        .ministry-text {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 0;
        }

        .divider-thin {
            width: 80mm;
            height: 1px;
            background: #e5e7eb;
            margin: 10px auto;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            font-size: 9px;
            color: #374151;
            padding: 1px 6px;
            text-align: center;
            line-height: 1.4;
        }

        .meta-label {
            font-weight: 700;
            color: #111827;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="page-inner">
            <div class="content-cell">

                <div class="eyebrow">Certificado de Conclusao</div>

                <div class="org-name">Movimento Casa</div>

                <div class="divider"></div>

                <p class="certifies-text">Certificamos que</p>

                <div class="member-name">{{ $member->full_name }}</div>

                <p class="concluded-text">concluiu com aproveitamento a formacao</p>

                <div class="formation-title">{{ $formation->title }}</div>

                @if ($formation->ministry?->name)
                    <p class="ministry-text">vinculada ao ministerio <strong>{{ $formation->ministry->name }}</strong></p>
                @endif

                <div class="divider-thin"></div>

                <table class="meta-table">
                    <tr>
                        <td>
                            <span class="meta-label">Data de conclusao</span><br>
                            {{ optional($progress->completed_at)->format('d/m/Y \a\s H:i') ?? '-' }}
                        </td>
                        <td>
                            <span class="meta-label">Carga horaria</span><br>
                            {{ $formation->workload_hours ? number_format((float) $formation->workload_hours, 0, ',', '.') . 'h' : 'Nao informada' }}
                        </td>
                        <td>
                            <span class="meta-label">Codigo de autenticacao</span><br>
                            {{ $certificateCode }}
                        </td>
                        <td>
                            <span class="meta-label">Emitido em</span><br>
                            {{ $issuedAt->format('d/m/Y \a\s H:i') }}
                        </td>
                    </tr>
                </table>

            </div>
        </div>
    </div>
</body>
</html>
