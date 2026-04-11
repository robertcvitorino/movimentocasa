<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        @page {
            margin: 0;
            size: 842pt 595pt;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #3f3f46;
            margin: 0;
            padding: 0;
        }

        .frame {
            border: 2pt solid #3b82f6;
            margin: 8pt;
            min-height: 565pt;
        }

        .content {
            text-align: center;
            padding: 50pt 60pt 20pt 60pt;
        }

        .logo img {
            width: 40pt;
            height: 40pt;
        }

        .eyebrow {
            font-size: 8pt;
            letter-spacing: 0.15em;
            text-transform: uppercase;
            color: #57534e;
            font-weight: bold;
            padding-top: 8pt;
        }

        .org-name {
            font-size: 26pt;
            font-weight: bold;
            color: #111827;
            padding: 6pt 0 0 0;
        }

        .divider {
            border: none;
            border-top: 1.5pt solid #3b82f6;
            width: 120pt;
            margin: 12pt auto;
        }

        .label-text {
            font-size: 10pt;
            color: #6b7280;
            font-style: italic;
        }

        .member-name {
            font-size: 32pt;
            font-weight: bold;
            color: #1e40af;
            padding: 8pt 0;
        }

        .formation-title {
            font-size: 18pt;
            font-weight: bold;
            color: #111827;
            padding: 4pt 0;
        }

        .ministry-text {
            font-size: 10pt;
            color: #6b7280;
            font-style: italic;
            padding-top: 2pt;
        }

        .meta-block {
            padding-top: 18pt;
            font-size: 8pt;
            color: #374151;
            line-height: 1.6;
        }

        .qr-section {
            padding-top: 10pt;
        }

        .qr-section img {
            width: 50pt;
            height: 50pt;
        }

        .qr-label {
            font-size: 6pt;
            color: #9ca3af;
            padding-top: 3pt;
        }
    </style>
</head>
<body>
    <div class="frame">
            <div class="content">
                <div class="logo">
                    <img src="{{ public_path('image/logo_casa_sm.png') }}" alt="Logo">
                </div>

                <div class="eyebrow">Certificado de Conclusão</div>

                <div class="org-name">Movimento Casa</div>

                <hr class="divider">

                <div class="label-text">Certificamos que</div>

                <div class="member-name">{{ $member->full_name }}</div>

                <div class="label-text">concluiu com aproveitamento a formação</div>

                <div class="formation-title">{{ $formation->title }}</div>

                @if ($formation->ministry?->name)
                    <div class="ministry-text">vinculada ao ministério {{ $formation->ministry->name }}</div>
                @endif

                <div class="meta-block">
                    Data de conclusão: {{ optional($progress->completed_at)->format('d/m/Y H:i') ?? '-' }}<br>
                    Carga horária: {{ $formation->workload_hours ? number_format((float) $formation->workload_hours, 2, ',', '.') . ' horas' : 'Não informada' }}<br>
                    @if ($progress->quiz_score !== null)
                        Nota final: {{ number_format((float) $progress->quiz_score, 2, ',', '.') }}%<br>
                    @endif
                    Código de autenticação: {{ $certificateCode }}<br>
                    Emitido em: {{ $issuedAt->format('d/m/Y H:i') }}
                </div>

                @if (!empty($qrCodeBase64))
                    <div class="qr-section">
                        <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code">
                        <div class="qr-label">Escaneie para verificar autenticidade</div>
                    </div>
                @endif
            </div>
    </div>
</body>
</html>
