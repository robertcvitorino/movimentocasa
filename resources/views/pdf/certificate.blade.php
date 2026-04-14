<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        @page {
            margin: 0;
            size: 210mm 297mm;
        }

        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #3f3f46;
            background: #ffffff;
        }

        .page {
            position: relative;
            width: 210mm;
            height: 297mm;
            overflow: hidden;
        }

        .border-frame {
            position: absolute;
            top: 8mm;
            left: 8mm;
            right: 8mm;
            bottom: 8mm;
            border: 2px solid #d1d5db;
        }

        .icon {
            position: absolute;
            color: #c4c4cc;
            opacity: 0.18;
        }

        .icon svg {
            display: block;
            width: 100%;
            height: 100%;
        }

        .icon-sm { width: 24px; height: 24px; }
        .icon-md { width: 30px; height: 30px; }

        .center-block {
            position: absolute;
            top: 0;
            left: 0;
            width: 210mm;
            height: 297mm;
            text-align: center;
        }

        .top-logo {
            margin-top: 28mm;
            display: inline-block;
            width: 24px;
            height: 28px;
            color: #9ca3af;
        }

        .top-logo svg {
            width: 100%;
            height: 100%;
        }

        .eyebrow {
            font-size: 11px;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            color: #57534e;
            font-weight: 700;
            margin-top: 6mm;
        }

        .title {
            font-size: 30px;
            font-weight: 700;
            color: #111827;
            margin-top: 5mm;
        }

        .separator {
            display: block;
            width: 60px;
            height: 2px;
            background: #d1d5db;
            margin: 6mm auto;
            border: none;
        }

        .text {
            font-size: 14px;
            line-height: 1.4;
            color: #5b5560;
        }

        .member-name {
            font-size: 34px;
            line-height: 1.1;
            font-weight: 700;
            color: #111827;
            margin-top: 7mm;
            padding: 0 20mm;
            word-wrap: break-word;
        }

        .text-after-name {
            margin-top: 7mm;
        }

        .formation-title {
            font-size: 22px;
            line-height: 1.15;
            font-weight: 700;
            color: #1657b8;
            margin-top: 4mm;
            padding: 0 20mm;
            word-wrap: break-word;
        }

        .ministry-line {
            margin-top: 3mm;
            font-size: 13px;
            color: #6b7280;
            font-style: italic;
        }

        .meta-area {
            position: absolute;
            bottom: 18mm;
            left: 20mm;
            right: 20mm;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            vertical-align: middle;
            padding: 0;
        }

        .qr-td {
            width: 82px;
            padding-right: 5mm;
        }

        .qr-td img {
            width: 82px;
            height: 82px;
        }

        .meta {
            font-size: 10px;
            line-height: 1.7;
            color: #374151;
        }

        .meta-line {
            margin: 0;
        }

        .meta-label {
            font-weight: 700;
        }
    </style>
</head>
<body>
<div class="page">
    {{-- Decorative border --}}
    <div class="border-frame"></div>

    {{-- Background decorative icons --}}
    <div class="icon icon-md" style="position: absolute; top: 14mm; left: 14mm;">@include('pdf.partials.icons.cross')</div>
    <div class="icon icon-sm" style="position: absolute; top: 18mm; left: 55mm;">@include('pdf.partials.icons.fish')</div>
    <div class="icon icon-sm" style="position: absolute; top: 14mm; left: 105mm;">@include('pdf.partials.icons.mountain')</div>
    <div class="icon icon-md" style="position: absolute; top: 16mm; left: 155mm;">@include('pdf.partials.icons.book')</div>
    <div class="icon icon-sm" style="position: absolute; top: 14mm; left: 188mm;">@include('pdf.partials.icons.cross')</div>

    <div class="icon icon-sm" style="position: absolute; top: 50mm; left: 14mm;">@include('pdf.partials.icons.book')</div>
    <div class="icon icon-md" style="position: absolute; top: 70mm; left: 18mm;">@include('pdf.partials.icons.church')</div>
    <div class="icon icon-sm" style="position: absolute; top: 50mm; left: 186mm;">@include('pdf.partials.icons.mountain')</div>
    <div class="icon icon-md" style="position: absolute; top: 72mm; left: 184mm;">@include('pdf.partials.icons.cross')</div>

    <div class="icon icon-sm" style="position: absolute; top: 120mm; left: 12mm;">@include('pdf.partials.icons.fish')</div>
    <div class="icon icon-sm" style="position: absolute; top: 150mm; left: 16mm;">@include('pdf.partials.icons.mountain')</div>
    <div class="icon icon-sm" style="position: absolute; top: 120mm; left: 188mm;">@include('pdf.partials.icons.book')</div>
    <div class="icon icon-sm" style="position: absolute; top: 150mm; left: 186mm;">@include('pdf.partials.icons.fish')</div>

    <div class="icon icon-md" style="position: absolute; top: 200mm; left: 14mm;">@include('pdf.partials.icons.cross')</div>
    <div class="icon icon-sm" style="position: absolute; top: 225mm; left: 18mm;">@include('pdf.partials.icons.church')</div>
    <div class="icon icon-sm" style="position: absolute; top: 200mm; left: 186mm;">@include('pdf.partials.icons.mountain')</div>
    <div class="icon icon-md" style="position: absolute; top: 228mm; left: 184mm;">@include('pdf.partials.icons.church')</div>

    <div class="icon icon-md" style="position: absolute; top: 268mm; left: 14mm;">@include('pdf.partials.icons.book')</div>
    <div class="icon icon-sm" style="position: absolute; top: 272mm; left: 60mm;">@include('pdf.partials.icons.fish')</div>
    <div class="icon icon-sm" style="position: absolute; top: 268mm; left: 110mm;">@include('pdf.partials.icons.mountain')</div>
    <div class="icon icon-md" style="position: absolute; top: 270mm; left: 158mm;">@include('pdf.partials.icons.cross')</div>
    <div class="icon icon-sm" style="position: absolute; top: 268mm; left: 188mm;">@include('pdf.partials.icons.church')</div>

    {{-- Main content --}}
    <div class="center-block">
        <div class="top-logo">@include('pdf.partials.icons.church')</div>

        <div class="eyebrow">Certificado de conclusao</div>

        <div class="title">Movimento Casa</div>

        <div class="separator"></div>

        <p class="text">Certificamos que</p>

        <div class="member-name">{{ $member->full_name }}</div>

        <p class="text text-after-name">concluiu com aproveitamento a formacao</p>

        <div class="formation-title">{{ $formation->title }}</div>

        @if ($formation->ministry?->name)
            <p class="ministry-line">vinculada ao ministerio {{ $formation->ministry->name }}</p>
        @endif
    </div>

    {{-- Bottom metadata with QR --}}
    <div class="meta-area">
        <table class="meta-table">
            <tr>
                <td class="qr-td">
                    <img src="{{ $qrCodeDataUri }}" alt="QR Code" />
                </td>
                <td>
                    <div class="meta">
                        <p class="meta-line"><span class="meta-label">Data de conclusao:</span> {{ optional($progress->completed_at)->format('d/m/Y H:i') }}</p>
                        <p class="meta-line"><span class="meta-label">Carga horaria:</span> {{ $formation->workload_hours ? number_format((float) $formation->workload_hours, 2, ',', '.') . ' horas' : 'Nao informada' }}</p>
                        <p class="meta-line"><span class="meta-label">Nota final:</span> {{ $progress->quiz_score !== null ? number_format((float) $progress->quiz_score, 2, ',', '.') . '%' : 'N/A' }}</p>
                        <p class="meta-line"><span class="meta-label">Codigo de autenticacao:</span> {{ $certificateCode }}</p>
                        <p class="meta-line"><span class="meta-label">Emitido em:</span> {{ $issuedAt->format('d/m/Y H:i') }}</p>
                    </div>
                </td>
            </tr>
        </table>
    </div>
</div>
</body>
</html>
