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
            border: 2pt solid #f4b8b8;
            margin: 8pt;
            padding: 8pt;
        }

        .frame-inner {
            border: 1pt dashed rgba(239, 68, 68, 0.35);
        }

        .content {
            text-align: center;
            padding: 60pt 60pt 40pt 60pt;
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
            padding-top: 28pt;
            font-size: 8pt;
            color: #374151;
            line-height: 1.8;
        }
    </style>
</head>
<body>
    <div class="frame">
        <div class="frame-inner">
            <div class="content">
                <div class="logo">
                    <img src="{{ public_path('image/logo_casa_sm.png') }}" alt="Logo">
                </div>

                <div class="eyebrow">Certificado de Conclusao</div>

                <div class="org-name">Movimento Casa</div>

                <hr class="divider">

                <div class="label-text">Certificamos que</div>

                <div class="member-name">{{ $member->full_name }}</div>

                <div class="label-text">concluiu com aproveitamento a formacao</div>

                <div class="formation-title">{{ $formation->title }}</div>

                @if ($formation->ministry?->name)
                    <div class="ministry-text">vinculada ao ministerio {{ $formation->ministry->name }}</div>
                @endif

                <div class="meta-block">
                    Data de conclusao: {{ optional($progress->completed_at)->format('d/m/Y H:i') ?? '-' }}<br>
                    Carga horaria: {{ $formation->workload_hours ? number_format((float) $formation->workload_hours, 2, ',', '.') . ' horas' : 'Nao informada' }}<br>
                    @if ($progress->quiz_score !== null)
                        Nota final: {{ number_format((float) $progress->quiz_score, 2, ',', '.') }}%<br>
                    @endif
                    Codigo de autenticacao: {{ $certificateCode }}<br>
                    Emitido em: {{ $issuedAt->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>
