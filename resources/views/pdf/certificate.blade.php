<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #1f2937;
            margin: 0;
            padding: 0;
        }

        .page {
            border: 12px solid #d97706;
            margin: 24px;
            padding: 48px 56px;
            height: 90%;
        }

        .eyebrow {
            font-size: 18px;
            letter-spacing: 5px;
            text-transform: uppercase;
            color: #92400e;
            text-align: center;
        }

        .title {
            font-size: 40px;
            font-weight: bold;
            text-align: center;
            margin-top: 18px;
            margin-bottom: 24px;
        }

        .text {
            font-size: 18px;
            line-height: 1.7;
            text-align: center;
        }

        .member-name {
            font-size: 34px;
            font-weight: bold;
            text-align: center;
            margin: 28px 0 10px;
        }

        .formation-title {
            font-size: 26px;
            font-weight: bold;
            text-align: center;
            margin: 12px 0 24px;
            color: #92400e;
        }

        .meta {
            width: 100%;
            margin-top: 40px;
        }

        .meta td {
            padding: 8px 0;
            font-size: 15px;
        }

        .meta .label {
            font-weight: bold;
            width: 180px;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="eyebrow">Certificado de conclusao</div>
        <div class="title">Grupo de Oracao Movimento Casa</div>

        <div class="text">
            Certificamos que
        </div>

        <div class="member-name">{{ $member->full_name }}</div>

        <div class="text">
            concluiu com aproveitamento a formacao
        </div>

        <div class="formation-title">{{ $formation->title }}</div>

        <div class="text">
            @if ($formation->ministry?->name)
                vinculada ao ministerio {{ $formation->ministry->name }}.
            @endif
        </div>

        <table class="meta">
            <tr>
                <td class="label">Data de conclusao:</td>
                <td>{{ optional($progress->completed_at)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Carga horaria:</td>
                <td>{{ $formation->workload_hours ? $formation->workload_hours . ' horas' : 'Nao informada' }}</td>
            </tr>
            <tr>
                <td class="label">Nota final:</td>
                <td>{{ number_format((float) $progress->quiz_score, 2, ',', '.') }}%</td>
            </tr>
            <tr>
                <td class="label">Codigo de autenticacao:</td>
                <td>{{ $certificateCode }}</td>
            </tr>
            <tr>
                <td class="label">Emitido em:</td>
                <td>{{ $issuedAt->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
