<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Certificado</title>
    <style>
        @page {
            margin: 0;
            size: A4 landscape;
        }

        * {
            box-sizing: border-box;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 297mm;
            height: 210mm;
            overflow: hidden;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #3f3f46;
            background: #fffdfb;
        }

        .page {
            position: relative;
            overflow: hidden;
            width: 297mm;
            height: 210mm;
            padding: 14mm 20mm 10mm;
            border: 2px solid #f4b8b8;
            background: linear-gradient(180deg, #fffefe 0%, #fff9f9 100%);
            page-break-inside: avoid;
            page-break-after: avoid;
        }

        .content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .safe-line {
            position: absolute;
            inset: 8px;
            border: 1px dashed #ef4444;
            opacity: 0.35;
            z-index: 1;
        }

        .pattern {
            position: absolute;
            inset: 0;
            z-index: 0;
        }

        .icon {
            position: absolute;
            color: #a1a1aa;
            opacity: 0.28;
        }

        .icon svg {
            display: block;
            width: 100%;
            height: 100%;
        }

        .icon-book {
            width: 38px;
            height: 44px;
        }

        .icon-cross {
            width: 36px;
            height: 48px;
        }

        .icon-fish {
            width: 52px;
            height: 24px;
        }

        .icon-mountain {
            width: 40px;
            height: 32px;
        }

        .icon-church {
            width: 50px;
            height: 50px;
        }

        .eyebrow {
            font-size: 12px;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: #57534e;
            font-weight: 700;
            margin-top: 8px;
        }

        .title {
            font-size: 28px;
            font-weight: 700;
            color: #111827;
            margin-top: 10px;
            margin-bottom: 16px;
        }

        .text {
            font-size: 15px;
            line-height: 1.4;
            margin: 0;
            color: #5b5560;
        }

        .member-name {
            font-size: 40px;
            line-height: 1.1;
            font-weight: 700;
            color: #1657b8;
            margin: 14px 0 12px;
        }

        .formation-title {
            font-size: 24px;
            line-height: 1.2;
            font-weight: 700;
            margin: 8px 0 8px;
            color: #2d23b6;
        }

        .meta {
            margin-top: 16px;
            font-size: 11px;
            line-height: 1.5;
            color: #18181b;
        }

        .meta-line {
            margin: 0;
        }

        .meta-label {
            font-weight: 700;
        }

        .ministry-line {
            margin-top: 2px;
        }

        .top-logo {
            width: 22px;
            height: 26px;
            margin: 0 auto 6px;
            color: #a1a1aa;
        }

        .top-logo svg {
            width: 100%;
            height: 100%;
        }

        .dot {
            color: #71717a;
        }
    </style>
</head>
<body>
    <div class="page">
        <div class="safe-line"></div>

        <div class="pattern">
            <div class="icon icon-cross" style="top: 14px; left: 22px;">@include('pdf.partials.icons.cross')</div>
            <div class="icon icon-fish" style="top: 42px; left: 190px;">@include('pdf.partials.icons.fish')</div>
            <div class="icon icon-mountain" style="top: 16px; left: 420px;">@include('pdf.partials.icons.mountain')</div>
            <div class="icon icon-book" style="top: 32px; left: 650px;">@include('pdf.partials.icons.book')</div>
            <div class="icon icon-mountain" style="top: 20px; right: 120px;">@include('pdf.partials.icons.mountain')</div>
            <div class="icon icon-cross" style="top: 14px; right: 20px;">@include('pdf.partials.icons.cross')</div>

            <div class="icon icon-book" style="top: 110px; left: 30px;">@include('pdf.partials.icons.book')</div>
            <div class="icon icon-mountain" style="top: 94px; left: 100px;">@include('pdf.partials.icons.mountain')</div>
            <div class="icon icon-church" style="top: 150px; left: 132px;">@include('pdf.partials.icons.church')</div>
            <div class="icon icon-cross" style="top: 110px; right: 108px;">@include('pdf.partials.icons.cross')</div>
            <div class="icon icon-church" style="top: 132px; right: 18px;">@include('pdf.partials.icons.church')</div>

            <div class="icon icon-fish" style="top: 252px; left: 18px;">@include('pdf.partials.icons.fish')</div>
            <div class="icon icon-book" style="top: 332px; left: 52px;">@include('pdf.partials.icons.book')</div>
            <div class="icon icon-mountain" style="top: 418px; left: 110px;">@include('pdf.partials.icons.mountain')</div>
            <div class="icon icon-cross" style="top: 470px; left: 122px;">@include('pdf.partials.icons.cross')</div>
            <div class="icon icon-church" style="bottom: 12px; left: 10px;">@include('pdf.partials.icons.church')</div>
            <div class="icon icon-book" style="bottom: 18px; left: 100px;">@include('pdf.partials.icons.book')</div>
            <div class="icon icon-fish" style="bottom: 52px; left: 174px;">@include('pdf.partials.icons.fish')</div>
            <div class="icon icon-mountain" style="bottom: 4px; left: 230px;">@include('pdf.partials.icons.mountain')</div>

            <div class="icon icon-book" style="top: 226px; right: 64px;">@include('pdf.partials.icons.book')</div>
            <div class="icon icon-fish" style="top: 332px; right: 16px;">@include('pdf.partials.icons.fish')</div>
            <div class="icon icon-mountain" style="top: 406px; right: 106px;">@include('pdf.partials.icons.mountain')</div>
            <div class="icon icon-mountain" style="top: 404px; right: 18px;">@include('pdf.partials.icons.mountain')</div>
            <div class="icon icon-cross" style="bottom: 42px; right: 170px;">@include('pdf.partials.icons.cross')</div>
            <div class="icon icon-book" style="bottom: 28px; right: 96px;">@include('pdf.partials.icons.book')</div>
            <div class="icon icon-church" style="bottom: 12px; right: 20px;">@include('pdf.partials.icons.church')</div>
            <div class="icon icon-fish" style="bottom: 20px; right: 160px;">@include('pdf.partials.icons.fish')</div>
        </div>

        <div class="content">
            <div class="top-logo">@include('pdf.partials.icons.book')</div>
            <div class="eyebrow">Certificado de conclusao</div>
            <div class="title">Movimento Casa</div>

            <p class="text">Certificamos que</p>

            <div class="member-name">{{ $member->full_name }}</div>

            <p class="text">concluiu com aproveitamento a formacao</p>

            <div class="formation-title">{{ $formation->title }}</div>

            <p class="text ministry-line">
                @if ($formation->ministry?->name)
                    vinculada ao ministerio {{ $formation->ministry->name }}
                @endif
            </p>

            <div class="meta">
                <p class="meta-line"><span class="meta-label">Data de conclusao:</span> {{ optional($progress->completed_at)->format('d/m/Y H:i') }}</p>
                <p class="meta-line"><span class="meta-label">Carga horaria:</span> {{ $formation->workload_hours ? number_format((float) $formation->workload_hours, 2, ',', '.') . ' horas' : 'Nao informada' }}</p>
                <p class="meta-line"><span class="meta-label">Codigo de autenticacao:</span> {{ $certificateCode }}</p>
                <p class="meta-line"><span class="meta-label">Emitido em:</span> {{ $issuedAt->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>
</body>
</html>
