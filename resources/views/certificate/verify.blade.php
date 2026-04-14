<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificação de Certificado - Movimento Casa</title>
    <style>
        *, ::before, ::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
            background-color: #f9fafb;
            color: #374151;
            line-height: 1.5;
        }
        .min-h-screen { min-height: 100vh; }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-center { justify-content: center; }
        .p-4 { padding: 1rem; }
        .w-full { width: 100%; }
        .max-w-lg { max-width: 32rem; }
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-0\.5, .mt-05 { margin-top: 0.125rem; }
        .mt-6 { margin-top: 1.5rem; }
        .pt-2 { padding-top: 0.5rem; }
        .px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .py-4 { padding-top: 1rem; padding-bottom: 1rem; }
        .py-5 { padding-top: 1.25rem; padding-bottom: 1.25rem; }
        .gap-3 { gap: 0.75rem; }
        .gap-4 { gap: 1rem; }
        .space-y-4 > * + * { margin-top: 1rem; }
        .text-2xl { font-size: 1.5rem; }
        .text-lg { font-size: 1.125rem; }
        .text-sm { font-size: 0.875rem; }
        .text-xs { font-size: 0.75rem; }
        .font-bold { font-weight: 700; }
        .font-semibold { font-weight: 600; }
        .font-medium { font-weight: 500; }
        .font-mono { font-family: ui-monospace, monospace; }
        .uppercase { text-transform: uppercase; }
        .tracking-wide { letter-spacing: 0.025em; }
        .text-gray-900 { color: #111827; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-500 { color: #6b7280; }
        .text-gray-400 { color: #9ca3af; }
        .text-blue-700 { color: #1d4ed8; }
        .text-green-800 { color: #166534; }
        .text-green-600 { color: #16a34a; }
        .text-red-800 { color: #991b1b; }
        .text-red-600 { color: #dc2626; }
        .bg-white { background-color: #fff; }
        .bg-green-50 { background-color: #f0fdf4; }
        .bg-red-50 { background-color: #fef2f2; }
        .rounded-xl { border-radius: 0.75rem; }
        .shadow-sm { box-shadow: 0 1px 2px rgba(0,0,0,.05); }
        .border { border: 1px solid; }
        .border-gray-200 { border-color: #e5e7eb; }
        .border-gray-100 { border-color: #f3f4f6; }
        .border-green-200 { border-color: #bbf7d0; }
        .border-red-200 { border-color: #fecaca; }
        .border-b { border-bottom: 1px solid; }
        .border-t { border-top: 1px solid; }
        .overflow-hidden { overflow: hidden; }
        .shrink-0 { flex-shrink: 0; }
        .grid { display: grid; }
        .grid-cols-2 { grid-template-columns: repeat(2, 1fr); }
        .w-6 { width: 1.5rem; }
        .h-6 { height: 1.5rem; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-lg">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Movimento Casa</h1>
            <p class="text-sm text-gray-500 mt-1">Verificação de Certificado</p>
        </div>

        @if ($certificate)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-green-50 border-b border-green-200 px-6 py-4 flex items-center gap-3">
                    <svg class="w-6 h-6 text-green-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.746 3.746 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                    </svg>
                    <div>
                        <p class="font-semibold text-green-800">Certificado Válido</p>
                        <p class="text-sm text-green-600">Este certificado é autêntico e foi emitido pelo Movimento Casa.</p>
                    </div>
                </div>

                <div class="px-6 py-5 space-y-4">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Participante</p>
                        <p class="text-lg font-semibold text-gray-900 mt-0.5">{{ $certificate->member->full_name }}</p>
                    </div>

                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Formação</p>
                        <p class="text-lg font-semibold text-blue-700 mt-0.5">{{ $certificate->formation->title }}</p>
                        @if ($certificate->formation->ministry?->name)
                            <p class="text-sm text-gray-500">Ministério: {{ $certificate->formation->ministry->name }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Data de Conclusão</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ optional($certificate->formationProgress?->completed_at)->format('d/m/Y H:i') ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Emitido em</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">{{ $certificate->issued_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Carga Horária</p>
                            <p class="text-sm font-medium text-gray-900 mt-0.5">
                                {{ $certificate->formation->workload_hours ? number_format((float) $certificate->formation->workload_hours, 2, ',', '.') . ' horas' : 'Não informada' }}
                            </p>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Código de Autenticação</p>
                        <p class="text-sm font-mono font-medium text-gray-900 mt-0.5">{{ $certificate->certificate_code }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-red-50 border-b border-red-200 px-6 py-4 flex items-center gap-3">
                    <svg class="w-6 h-6 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                    <div>
                        <p class="font-semibold text-red-800">Certificado Não Encontrado</p>
                        <p class="text-sm text-red-600">O código informado não corresponde a nenhum certificado emitido.</p>
                    </div>
                </div>
                <div class="px-6 py-5 text-center">
                    <p class="text-sm text-gray-600">Verifique se o código ou QR code está correto e tente novamente.</p>
                </div>
            </div>
        @endif

        <p class="text-center text-xs text-gray-400 mt-6">&copy; {{ date('Y') }} Movimento Casa</p>
    </div>
</body>
</html>
