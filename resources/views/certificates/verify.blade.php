<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Certificado - Movimento Casa</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: system-ui, -apple-system, 'Segoe UI', sans-serif;
            background: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header {
            width: 100%;
            background: #1e3a5f;
            padding: 16px 0;
            text-align: center;
        }

        .header img {
            height: 36px;
            vertical-align: middle;
        }

        .header span {
            color: #ffffff;
            font-size: 18px;
            font-weight: 700;
            margin-left: 10px;
            vertical-align: middle;
        }

        .container {
            width: 100%;
            max-width: 560px;
            padding: 32px 20px;
        }

        .card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08), 0 4px 16px rgba(0,0,0,0.04);
            overflow: hidden;
        }

        .card-status {
            background: #f0fdf4;
            border-bottom: 1px solid #bbf7d0;
            padding: 20px 24px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .check-icon {
            width: 40px;
            height: 40px;
            background: #22c55e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .check-icon svg {
            width: 22px;
            height: 22px;
            color: #ffffff;
        }

        .status-text {
            font-size: 18px;
            font-weight: 700;
            color: #15803d;
        }

        .status-sub {
            font-size: 13px;
            color: #4ade80;
            margin-top: 2px;
        }

        .card-body {
            padding: 24px;
        }

        .member-name {
            font-size: 24px;
            font-weight: 700;
            color: #1e40af;
            margin-bottom: 4px;
        }

        .formation-name {
            font-size: 16px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 16px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
            margin-bottom: 20px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-item.full {
            grid-column: 1 / -1;
        }

        .info-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #94a3b8;
            font-weight: 600;
            margin-bottom: 2px;
        }

        .info-value {
            font-size: 14px;
            color: #334155;
        }

        .divider {
            border: none;
            border-top: 1px solid #e2e8f0;
            margin: 20px 0;
        }

        .download-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #1e40af;
            color: #ffffff;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.2s;
        }

        .download-link:hover {
            background: #1e3a8a;
        }

        .download-link svg {
            width: 18px;
            height: 18px;
        }

        .footer {
            margin-top: 24px;
            text-align: center;
            font-size: 12px;
            color: #94a3b8;
        }

        @media (max-width: 480px) {
            .info-grid { grid-template-columns: 1fr; }
            .member-name { font-size: 20px; }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('image/logo_casa_dark.png') }}" alt="Logo">
        <span>Movimento Casa</span>
    </div>

    <div class="container">
        <div class="card">
            <div class="card-status">
                <div class="check-icon">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <div>
                    <div class="status-text">Certificado Verificado</div>
                    <div class="status-sub">Este certificado é autêntico</div>
                </div>
            </div>

            <div class="card-body">
                <div class="member-name">{{ $certificate->member->full_name }}</div>
                <div class="formation-name">{{ $certificate->formation->title }}</div>

                <div class="info-grid">
                    @if ($certificate->formation->ministry?->name)
                        <div class="info-item full">
                            <span class="info-label">Ministério</span>
                            <span class="info-value">{{ $certificate->formation->ministry->name }}</span>
                        </div>
                    @endif

                    <div class="info-item">
                        <span class="info-label">Data de Conclusão</span>
                        <span class="info-value">{{ optional($certificate->formationProgress?->completed_at)->format('d/m/Y H:i') ?? '-' }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Carga Horária</span>
                        <span class="info-value">{{ $certificate->formation->workload_hours ? number_format((float) $certificate->formation->workload_hours, 0, ',', '.') . ' horas' : '-' }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Código do Certificado</span>
                        <span class="info-value">{{ $certificate->certificate_code }}</span>
                    </div>

                    <div class="info-item">
                        <span class="info-label">Emitido em</span>
                        <span class="info-value">{{ $certificate->issued_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>

                <hr class="divider">

                @if ($certificate->pdf_path)
                    <a href="{{ Storage::disk('public')->url($certificate->pdf_path) }}" target="_blank" class="download-link">
                        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Baixar Certificado PDF
                    </a>
                @endif
            </div>
        </div>

        <div class="footer">
            &copy; {{ date('Y') }} Movimento Casa. Verificação de autenticidade de certificado.
        </div>
    </div>
</body>
</html>
