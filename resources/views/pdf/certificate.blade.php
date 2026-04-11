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
            margin: 0;
            padding: 0;
            width: 842pt;
            height: 595pt;
        }

        .bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 842pt;
            height: 595pt;
        }

        .bg img {
            width: 842pt;
            height: 595pt;
        }

        .border-frame {
            position: fixed;
            top: 8pt;
            left: 8pt;
            width: 822pt;
            height: 575pt;
            border: 2.5pt solid #3b82f6;
        }

        .page {
            width: 842pt;
            height: 595pt;
            text-align: center;
        }

        .content {
            padding: 36pt 80pt 24pt 80pt;
            text-align: center;
            color: #1e293b;
        }

        .logo img {
            width: 44pt;
            height: 44pt;
        }

        .eyebrow {
            font-size: 7.5pt;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            padding-top: 6pt;
        }

        .org-name {
            font-size: 24pt;
            font-weight: bold;
            color: #0f172a;
            padding: 4pt 0 0 0;
        }

        .divider {
            border: none;
            border-top: 1.5pt solid #3b82f6;
            width: 100pt;
            margin: 10pt auto;
        }

        .label-text {
            font-size: 9.5pt;
            color: #475569;
            font-style: italic;
        }

        .member-name {
            font-size: 30pt;
            font-weight: bold;
            color: #1e40af;
            padding: 6pt 0 4pt 0;
        }

        .formation-title {
            font-size: 16pt;
            font-weight: bold;
            color: #0f172a;
            padding: 2pt 0;
        }

        .ministry-text {
            font-size: 9pt;
            color: #64748b;
            font-style: italic;
            padding-top: 2pt;
        }

        .meta-block {
            padding-top: 16pt;
            font-size: 7.5pt;
            color: #334155;
            line-height: 1.7;
        }

        .qr-section {
            padding-top: 10pt;
            text-align: center;
        }

        .qr-section img {
            width: 48pt;
            height: 48pt;
        }

        .qr-label {
            font-size: 5.5pt;
            color: #94a3b8;
            padding-top: 2pt;
        }
    </style>
</head>
<body>
    <div class="bg">
        <img src="{{ public_path('cetificado/certificate-bg.png') }}" alt="">
    </div>

    <div class="border-frame"></div>

    <div class="page">
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
                Data de conclusão: {{ optional($progress->completed_at)->format('d/m/Y H:i') ?? '-' }}&nbsp;&nbsp;|&nbsp;&nbsp;
                Carga horária: {{ $formation->workload_hours ? number_format((float) $formation->workload_hours, 2, ',', '.') . ' horas' : 'Não informada' }}<br>
                @if ($progress->quiz_score !== null)
                    Nota final: {{ number_format((float) $progress->quiz_score, 2, ',', '.') }}%&nbsp;&nbsp;|&nbsp;&nbsp;
                @endif
                Código de autenticação: {{ $certificateCode }}&nbsp;&nbsp;|&nbsp;&nbsp;
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
