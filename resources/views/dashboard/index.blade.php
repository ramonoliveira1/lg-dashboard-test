@extends('layouts.app')

@section('title', 'Dashboard de Produção – Janeiro 2026')

@section('content')

<div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-extrabold text-gray-900 tracking-tight">Eficiência de Produção</h2>
        <p class="text-sm text-gray-400 mt-1 font-medium">Planta A &mdash; Janeiro de 2026</p>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <button
            id="btn-ai-analysis"
            onclick="handleAiClick()"
            class="group inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-red-200 hover:shadow-lg hover:shadow-red-300 transition-all duration-200"
        >
            <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
            </svg>
            Gerar análise com IA
        </button>

        <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2">
            <select
                id="product_line"
                name="product_line"
                onchange="this.form.submit()"
                class="rounded-xl border border-gray-200 text-sm px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent bg-white shadow-sm font-medium text-gray-700 appearance-none pr-10 bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%2020%2020%22%20fill%3D%22%236b7280%22%3E%3Cpath%20fill-rule%3D%22evenodd%22%20d%3D%22M5.23%207.21a.75.75%200%20011.06.02L10%2011.168l3.71-3.938a.75.75%200%20111.08%201.04l-4.25%204.5a.75.75%200%2001-1.08%200l-4.25-4.5a.75.75%200%2001.02-1.06z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:20px] bg-[right_8px_center] bg-no-repeat"
            >
                <option value="">Todas as linhas</option>
                @foreach ($productLines as $line)
                    <option value="{{ $line }}" {{ $selectedLine === $line ? 'selected' : '' }}>
                        {{ $line }}
                    </option>
                @endforeach
            </select>
            @if ($selectedLine)
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center gap-1 text-xs text-red-500 hover:text-red-700 font-semibold bg-red-50 hover:bg-red-100 px-3 py-2 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    Limpar
                </a>
            @endif
        </form>
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-10">
    @forelse ($summary as $row)
        @php
            $eff = $row->efficiency;
            $gradient = $eff >= 95
                ? 'from-emerald-500 to-green-600'
                : ($eff >= 85 ? 'from-amber-400 to-yellow-500' : 'from-red-500 to-rose-600');
            $bgBorder = $eff >= 95
                ? 'border-emerald-100'
                : ($eff >= 85 ? 'border-amber-100' : 'border-red-100');
            $textColor = $eff >= 95
                ? 'text-emerald-600'
                : ($eff >= 85 ? 'text-amber-500' : 'text-red-500');
            $badgeBg = $eff >= 95
                ? 'bg-emerald-50 text-emerald-600'
                : ($eff >= 85 ? 'bg-amber-50 text-amber-600' : 'bg-red-50 text-red-500');
            $label = $eff >= 95 ? '● Ótimo' : ($eff >= 85 ? '● Regular' : '● Crítico');
            $shadowColor = $eff >= 95
                ? 'shadow-emerald-100'
                : ($eff >= 85 ? 'shadow-amber-100' : 'shadow-red-100');
        @endphp
        <div class="card-hover bg-white rounded-2xl border {{ $bgBorder }} shadow-sm {{ $shadowColor }} p-5 flex flex-col gap-3 relative overflow-hidden">
            <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r {{ $gradient }}"></div>

            <div class="flex items-center justify-between mt-1">
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                    {{ $row->product_line }}
                </span>
                <span class="text-[10px] px-2 py-0.5 rounded-full font-bold {{ $badgeBg }}">
                    {{ $label }}
                </span>
            </div>

            <div class="flex items-baseline gap-1">
                <span class="text-4xl font-black {{ $textColor }} tabular-nums">
                    {{ number_format($eff, 1) }}
                </span>
                <span class="text-lg font-bold {{ $textColor }}">%</span>
            </div>

            <div class="w-full bg-gray-100 rounded-full h-1.5">
                <div class="h-1.5 rounded-full bg-gradient-to-r {{ $gradient }} transition-all duration-500"
                     style="width: {{ min($eff, 100) }}%"></div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-1">
                <div class="bg-gray-50 rounded-lg px-3 py-2">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Produzido</p>
                    <p class="text-sm font-bold text-gray-700 tabular-nums">{{ number_format($row->total_produced) }}</p>
                </div>
                <div class="bg-gray-50 rounded-lg px-3 py-2">
                    <p class="text-[10px] text-gray-400 uppercase tracking-wider font-semibold">Defeitos</p>
                    <p class="text-sm font-bold text-red-500 tabular-nums">{{ number_format($row->total_defects) }}</p>
                </div>
            </div>
        </div>
    @empty
        <div class="col-span-4 text-center text-gray-400 py-16">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            <p class="text-sm font-medium">Nenhum dado encontrado para o filtro selecionado.</p>
        </div>
    @endforelse
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-10">

    
    <div class="xl:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center gap-2 mb-5">
            <div class="w-2 h-2 rounded-full bg-red-500"></div>
            <h3 class="text-sm font-bold text-gray-700">Eficiência por Linha</h3>
        </div>
        <canvas id="efficiencyChart" height="220"></canvas>
    </div>

    
    <div class="xl:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                <h3 class="text-sm font-bold text-gray-700">Registros Diários</h3>
            </div>
            <span class="text-[11px] text-gray-400 font-semibold bg-gray-50 px-2.5 py-1 rounded-md">
                {{ $dailyRecords->count() }} registro(s)
            </span>
        </div>
        <div class="overflow-x-auto max-h-[420px] overflow-y-auto">
            <table id="daily-table" class="min-w-full text-sm">
                <thead class="bg-gray-50/80 sticky top-0 z-10 backdrop-blur">
                    <tr>
                        <th class="sortable-th px-5 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer select-none hover:text-gray-600 transition-colors group"
                            data-sort="date" data-col="0">
                            <span class="inline-flex items-center gap-1">
                                Data
                                <svg class="sort-icon w-3 h-3 opacity-0 group-hover:opacity-40 transition-opacity" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M8 10l4-4 4 4M8 14l4 4 4-4"/></svg>
                            </span>
                        </th>
                        <th class="sortable-th px-5 py-3 text-left text-[11px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer select-none hover:text-gray-600 transition-colors group"
                            data-sort="string" data-col="1">
                            <span class="inline-flex items-center gap-1">
                                Linha
                                <svg class="sort-icon w-3 h-3 opacity-0 group-hover:opacity-40 transition-opacity" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M8 10l4-4 4 4M8 14l4 4 4-4"/></svg>
                            </span>
                        </th>
                        <th class="sortable-th px-5 py-3 text-right text-[11px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer select-none hover:text-gray-600 transition-colors group"
                            data-sort="number" data-col="2">
                            <span class="inline-flex items-center gap-1 justify-end">
                                Produzido
                                <svg class="sort-icon w-3 h-3 opacity-0 group-hover:opacity-40 transition-opacity" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M8 10l4-4 4 4M8 14l4 4 4-4"/></svg>
                            </span>
                        </th>
                        <th class="sortable-th px-5 py-3 text-right text-[11px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer select-none hover:text-gray-600 transition-colors group"
                            data-sort="number" data-col="3">
                            <span class="inline-flex items-center gap-1 justify-end">
                                Defeitos
                                <svg class="sort-icon w-3 h-3 opacity-0 group-hover:opacity-40 transition-opacity" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M8 10l4-4 4 4M8 14l4 4 4-4"/></svg>
                            </span>
                        </th>
                        <th class="sortable-th px-5 py-3 text-right text-[11px] font-bold text-gray-400 uppercase tracking-wider cursor-pointer select-none hover:text-gray-600 transition-colors group"
                            data-sort="number" data-col="4">
                            <span class="inline-flex items-center gap-1 justify-end">
                                Eficiência
                                <svg class="sort-icon w-3 h-3 opacity-0 group-hover:opacity-40 transition-opacity" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M8 10l4-4 4 4M8 14l4 4 4-4"/></svg>
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($dailyRecords as $record)
                        @php $e = $record->efficiency; @endphp
                        <tr class="hover:bg-gray-50/60 transition-colors"
                            data-date="{{ $record->production_date->format('Y-m-d') }}"
                            data-line="{{ $record->product_line }}"
                            data-produced="{{ $record->produced_quantity }}"
                            data-defects="{{ $record->defect_quantity }}"
                            data-efficiency="{{ $e }}">
                            <td class="px-5 py-3 text-gray-500 whitespace-nowrap tabular-nums font-medium">
                                {{ $record->production_date->format('d/m/Y') }}
                            </td>
                            <td class="px-5 py-3 font-semibold text-gray-800">{{ $record->product_line }}</td>
                            <td class="px-5 py-3 text-right text-gray-600 tabular-nums font-medium">{{ number_format($record->produced_quantity) }}</td>
                            <td class="px-5 py-3 text-right tabular-nums font-medium">
                                <span class="text-red-400 bg-red-50 px-2 py-0.5 rounded-md text-xs font-bold">
                                    {{ number_format($record->defect_quantity) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-right">
                                <span class="inline-flex items-center gap-1 font-bold text-xs px-2 py-0.5 rounded-md
                                    {{ $e >= 95 ? 'text-emerald-600 bg-emerald-50' : ($e >= 85 ? 'text-amber-600 bg-amber-50' : 'text-red-500 bg-red-50') }}">
                                    {{ number_format($e, 1) }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-300 text-sm font-medium">
                                Nenhum registro encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

<div id="modal-token" class="modal-backdrop fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 p-6 border border-gray-100">
        <div class="flex items-center justify-between mb-5">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                </div>
                <h3 class="text-base font-bold text-gray-900">Configurar API Key</h3>
            </div>
            <button onclick="closeModal('modal-token')" class="text-gray-300 hover:text-gray-500 transition-colors text-xl">&times;</button>
        </div>
        <p class="text-sm text-gray-400 mb-5 leading-relaxed">
            Informe sua API Key do <strong class="text-gray-600">Google Gemini</strong> para gerar análises com IA.
            A chave será salva no servidor.
        </p>
        <input
            id="input-api-key"
            type="password"
            placeholder="Cole sua API Key aqui..."
            class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 focus:border-transparent mb-1 font-medium placeholder:text-gray-300"
        />
        <p id="token-error" class="text-xs text-red-500 mb-4 hidden font-medium"></p>
        <div class="flex justify-end gap-2 mt-2">
            <button onclick="closeModal('modal-token')"
                class="px-4 py-2.5 text-sm text-gray-500 hover:text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-colors">
                Cancelar
            </button>
            <button id="btn-save-key" onclick="saveKeyAndAnalyze()"
                class="px-5 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white text-sm font-semibold rounded-xl shadow-md shadow-red-200 transition-all duration-200">
                Salvar e gerar análise
            </button>
        </div>
    </div>
</div>

<div id="modal-analysis" class="modal-backdrop fixed inset-0 z-50 hidden items-center justify-center bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl mx-4 max-h-[85vh] flex flex-col overflow-hidden border border-gray-100">
        <div class="flex items-center justify-between p-5 border-b border-gray-100 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-gradient-to-br from-red-500 to-red-600 rounded-lg flex items-center justify-center shadow-sm">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-gray-900">Análise com IA</h3>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="clearToken()" title="Alterar API Key"
                    class="text-[11px] text-gray-400 hover:text-red-500 font-semibold underline underline-offset-2 transition-colors">
                    Alterar Key
                </button>
                <button onclick="closeModal('modal-analysis')" class="text-gray-300 hover:text-gray-500 transition-colors text-xl">&times;</button>
            </div>
        </div>
        <div id="analysis-content" class="p-6 flex-1 min-h-0 overflow-y-auto prose prose-sm prose-ai max-w-none">
        </div>
    </div>
</div>

@push('scripts')
<script>
    const summaryData = @json($summary->values());
    const labels  = summaryData.map(r => r.product_line);
    const effData = summaryData.map(r => parseFloat(r.efficiency));

    const colors = effData.map(e =>
        e >= 95 ? '#10b981' : (e >= 85 ? '#f59e0b' : '#ef4444')
    );
    const bgColors = effData.map(e =>
        e >= 95 ? '#d1fae5' : (e >= 85 ? '#fef3c7' : '#fee2e2')
    );

    new Chart(document.getElementById('efficiencyChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Eficiência (%)',
                data: effData,
                backgroundColor: colors,
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.6,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#f9fafb',
                    bodyColor: '#d1d5db',
                    padding: 12,
                    cornerRadius: 10,
                    displayColors: false,
                    titleFont: { size: 13, weight: '700' },
                    callbacks: {
                        label: ctx => `  ${ctx.parsed.y.toFixed(1)}%`
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    min: 70,
                    max: 100,
                    ticks: {
                        callback: v => v + '%',
                        color: '#9ca3af',
                        font: { size: 11, weight: '600' }
                    },
                    grid: { color: '#f3f4f6', drawBorder: false }
                },
                x: {
                    ticks: {
                        color: '#6b7280',
                        font: { size: 11, weight: '600' }
                    },
                    grid: { display: false, drawBorder: false }
                }
            }
        }
    });

    const CSRF = document.querySelector('meta[name="csrf-token"]').content;
    const HEADERS = {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': CSRF,
        'Accept': 'application/json',
    };

    function openModal(id) {
        const el = document.getElementById(id);
        el.classList.remove('hidden');
        el.classList.add('flex');
    }

    function closeModal(id) {
        const el = document.getElementById(id);
        el.classList.add('hidden');
        el.classList.remove('flex');
    }

    async function handleAiClick() {
        try {
            const res = await fetch('{{ route("analysis.status") }}', {
                headers: { 'Accept': 'application/json' },
            });
            const data = await res.json();

            if (data.has_key) {
                requestAnalysis();
            } else {
                document.getElementById('input-api-key').value = '';
                document.getElementById('token-error').classList.add('hidden');
                openModal('modal-token');
            }
        } catch {
            openModal('modal-token');
        }
    }

    async function saveKeyAndAnalyze() {
        const key = document.getElementById('input-api-key').value.trim();
        const err = document.getElementById('token-error');

        if (key.length < 10) {
            err.textContent = 'A API Key parece ser muito curta.';
            err.classList.remove('hidden');
            return;
        }

        const btn = document.getElementById('btn-save-key');
        btn.disabled = true;
        btn.textContent = 'Salvando...';

        try {
            const res = await fetch('{{ route("analysis.configure") }}', {
                method: 'POST',
                headers: HEADERS,
                body: JSON.stringify({ api_key: key }),
            });

            const data = await res.json();

            if (!res.ok) {
                const msg = data.errors?.api_key?.[0] || data.message || 'Erro ao salvar a key.';
                err.textContent = msg;
                err.classList.remove('hidden');
                return;
            }

            closeModal('modal-token');
            requestAnalysis();
        } catch (e) {
            err.textContent = 'Erro de conexão: ' + e.message;
            err.classList.remove('hidden');
        } finally {
            btn.disabled = false;
            btn.textContent = 'Salvar e gerar análise';
        }
    }

    function clearToken() {
        closeModal('modal-analysis');
        document.getElementById('input-api-key').value = '';
        document.getElementById('token-error').classList.add('hidden');
        openModal('modal-token');
    }

    async function requestAnalysis() {
        const content = document.getElementById('analysis-content');
        content.innerHTML = `
            <div class="flex flex-col items-center justify-center py-16 text-gray-300">
                <svg class="animate-spin h-8 w-8 mb-4 text-red-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <span class="text-sm font-medium text-gray-400">Gerando análise com Gemini...</span>
                <span class="text-xs text-gray-300 mt-1">Isso pode levar alguns segundos</span>
            </div>`;
        openModal('modal-analysis');

        try {
            const res = await fetch('{{ route("analysis.generate") }}', {
                method: 'POST',
                headers: HEADERS,
            });

            const data = await res.json();

            if (!res.ok) {
                content.innerHTML = `
                    <div class="text-center py-12">
                        <div class="w-12 h-12 mx-auto mb-4 bg-red-50 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                        </div>
                        <p class="text-red-500 font-bold text-sm mb-1">Erro</p>
                        <p class="text-sm text-gray-400">${data.error || 'Erro desconhecido.'}</p>
                    </div>`;
                return;
            }

            content.innerHTML = marked.parse(data.analysis);
        } catch (err) {
            content.innerHTML = `
                <div class="text-center py-12">
                    <p class="text-red-500 font-bold text-sm mb-1">Erro de conexão</p>
                    <p class="text-sm text-gray-400">${err.message}</p>
                </div>`;
        }
    }

    
    ['modal-token', 'modal-analysis'].forEach(id => {
        document.getElementById(id).addEventListener('click', function(e) {
            if (e.target === this) closeModal(id);
        });
    });

    (function() {
        const table = document.getElementById('daily-table');
        if (!table) return;

        const tbody = table.querySelector('tbody');
        const headers = table.querySelectorAll('.sortable-th');
        const dataAttrs = ['date', 'line', 'produced', 'defects', 'efficiency'];

        let currentSort = { col: null, dir: 'asc' };

        
        const ICON_BOTH = '<path d="M8 10l4-4 4 4M8 14l4 4 4-4"/>';
        const ICON_ASC  = '<path d="M8 14l4-4 4 4"/>';
        const ICON_DESC = '<path d="M8 10l4 4 4-4"/>';

        headers.forEach(th => {
            th.addEventListener('click', function() {
                const col = parseInt(this.dataset.col);
                const type = this.dataset.sort;

                
                if (currentSort.col === col) {
                    currentSort.dir = currentSort.dir === 'asc' ? 'desc' : 'asc';
                } else {
                    currentSort.col = col;
                    currentSort.dir = 'asc';
                }

                
                headers.forEach(h => {
                    const svg = h.querySelector('.sort-icon');
                    const hCol = parseInt(h.dataset.col);
                    if (hCol === col) {
                        svg.innerHTML = currentSort.dir === 'asc' ? ICON_ASC : ICON_DESC;
                        svg.style.opacity = '1';
                        h.classList.add('text-gray-700');
                        h.classList.remove('text-gray-400');
                    } else {
                        svg.innerHTML = ICON_BOTH;
                        svg.style.opacity = '';
                        h.classList.remove('text-gray-700');
                        h.classList.add('text-gray-400');
                    }
                });

                
                const rows = Array.from(tbody.querySelectorAll('tr'));
                const attr = dataAttrs[col];

                rows.sort((a, b) => {
                    let va = a.dataset[attr];
                    let vb = b.dataset[attr];

                    if (!va && !vb) return 0;
                    if (!va) return 1;
                    if (!vb) return -1;

                    if (type === 'number') {
                        va = parseFloat(va);
                        vb = parseFloat(vb);
                    } else if (type === 'date') {
                        vb = vb;
                    }

                    let cmp;
                    if (type === 'number') {
                        cmp = va - vb;
                    } else {
                        cmp = String(va).localeCompare(String(vb), 'pt-BR');
                    }

                    return currentSort.dir === 'asc' ? cmp : -cmp;
                });

                
                rows.forEach(r => tbody.appendChild(r));
            });
        });
    })();
</script>
@endpush

