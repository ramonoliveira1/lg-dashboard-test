<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LG Electronics – Dashboard de Produção')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'system-ui', 'sans-serif'] },
                }
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <style>
        /* Smooth modal transitions */
        .modal-backdrop { transition: opacity 0.2s ease; }
        .modal-backdrop.hidden { opacity: 0; pointer-events: none; }
        .modal-backdrop.flex  { opacity: 1; }

        /* Glassmorphism */
        .glass { background: rgba(255,255,255,0.7); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }

        /* Card hover lift */
        .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .card-hover:hover { transform: translateY(-3px); box-shadow: 0 12px 24px -8px rgba(0,0,0,0.12); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }

        .prose-ai h1, .prose-ai h2, .prose-ai h3 { color: #1f2937; font-weight: 700; }
        .prose-ai p { color: #4b5563; line-height: 1.7; }
        .prose-ai ul, .prose-ai ol { color: #4b5563; }
        .prose-ai strong { color: #111827; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen font-sans antialiased">

    
    <div class="fixed inset-0 -z-10">
        <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:20px_20px] opacity-50"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-red-100/40 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-blue-100/30 rounded-full blur-3xl"></div>
    </div>

    
    <header class="glass border-b border-gray-200/60 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 bg-gradient-to-br from-red-500 to-red-700 rounded-lg flex items-center justify-center shadow-md shadow-red-200">
                    <span class="text-white font-black text-sm leading-none">LG</span>
                </div>
                <div>
                    <h1 class="text-base font-bold text-gray-900 leading-none tracking-tight">LG Electronics</h1>
                    <p class="text-[11px] text-gray-400 mt-0.5 font-medium">Dashboard de Produção · Planta A</p>
                </div>
            </div>
            <div class="text-[11px] text-gray-400 font-medium hidden sm:block">
                Janeiro de 2026
            </div>
        </div>
    </header>

    
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <footer class="text-center text-[11px] text-gray-300 py-6 font-medium">
        &copy; {{ date('Y') }} LG Electronics — Dashboard de Eficiência de Produção
    </footer>

    @stack('scripts')
</body>
</html>

