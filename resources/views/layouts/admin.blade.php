<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel Administrativo')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Favicon -->
    <link rel="icon"
        href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>📝</text></svg>">
    <!-- Google Fonts -->
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Roboto+Mono:wght@300;400;500&display=swap"
        rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2b4642;
            --primary-light: #3a5a55;
            --primary-dark: #1d3531;
            --accent-color: #d4af37;
            --accent-light: #e6c45c;
            --accent-dark: #b8941f;
            --text-light: #f8fafc;
            --text-dark: #1e293b;
            --bg-light: #f8fafc;
            --bg-gray: #f1f5f9;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --error-color: #ef4444;
        }
    </style>
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-800" style="font-family: 'Poppins', sans-serif;">
    <div class="flex min-h-screen">

        <!-- Sidebar -->
        <nav id="sidebar"
            class="bg-gradient-to-br from-[#1d3531] via-[#2b4642] to-[#1d3531] shadow-2xl md:w-64 flex-shrink-0
                   transform -translate-x-full md:translate-x-0
                   fixed md:relative top-0 left-0 h-full
                   transition-transform duration-300 ease-in-out
                   z-50 md:z-0">
            <div class="h-full flex flex-col">
                <!-- Logo -->
                <div class="flex flex-col items-center justify-center h-28 px-4 border-b border-[#3a5a55]">
                    <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center space-y-2">
                        <div class="relative">
                            <img src="{{ asset('img/papeleria.jpeg') }}" alt="Logo"
                                class="h-16 w-16 object-contain rounded-full shadow-lg border-2 border-[#d4af37]" />
                            <span
                                class="absolute -bottom-1 -right-1 h-5 w-5 bg-[#d4af37] rounded-full border-2 border-[#1d3531]"></span>
                        </div>
                        <p class="font-light text-xs text-[#e6c45c] tracking-wider">OFRECER SERVICIOS</p>
                    </a>
                </div>

                <!-- Menú -->
                <div class="flex-1 overflow-y-auto py-4 px-2">
                    <div class="space-y-1">
                        <!-- Principal -->
                        <div class="px-3 pt-4 pb-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-[#e6c45c]">Principal</span>
                        </div>
                        <x-nav-link href="{{ route('admin.dashboard') }}" icon="gauge-high" :active="request()->routeIs('admin.dashboard')">
                            Dashboard
                        </x-nav-link>

                        <!-- Clientes -->
                        <div class="px-3 pt-4 pb-2">
                            <span class="text-xs font-semibold uppercase tracking-wider text-[#e6c45c]">Clientes</span>
                        </div>
                        <x-nav-link href="{{ route('admin.clients.index') }}" icon="users"
                            :active="request()->routeIs('admin.clients.index')">Clientes</x-nav-link>
                        <x-nav-link href="{{ route('admin.message.index') }}" icon="envelope"
                            :active="request()->routeIs('admin.message.index')">Mensajes</x-nav-link>
                    </div>
                </div>

                <!-- Pie de sidebar -->
                <div class="p-4 border-t border-[#3a5a55]">
                    @auth
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="button" id="logout-button"
                                class="flex items-center space-x-2 w-full px-4 py-3 text-sm font-medium text-[#f8fafc] hover:bg-[#3a5a55] hover:text-[#e6c45c] rounded-lg transition-all duration-200 group">
                                <i class="fas fa-sign-out-alt text-[#e6c45c] group-hover:text-[#e6c45c] mr-2"></i>
                                <span>Cerrar Sesión</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}"
                            class="flex items-center space-x-2 px-4 py-3 text-sm font-medium text-[#f8fafc] hover:bg-[#3a5a55] hover:text-[#e6c45c] rounded-lg transition-all duration-200 group">
                            <i class="fas fa-sign-in-alt text-[#e6c45c] group-hover:text-[#e6c45c] mr-2"></i>
                            <span>Iniciar Sesión</span>
                        </a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Overlay -->
        <div id="overlay" class="fixed inset-0 bg-black bg-opacity-70 hidden z-20 md:hidden backdrop-blur-sm"></div>

        <!-- Contenido principal -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gradient-to-br from-[#f8fafc] to-[#f1f5f9]">
            <!-- Header -->
            <header class="bg-white/90 backdrop-blur-md shadow-sm sticky top-0 z-20 border-b border-gray-200/50">
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center space-x-4">
                        <button id="menu-toggle"
                            class="md:hidden text-[#1e293b] focus:outline-none hover:text-[#2b4642] transition-colors">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                        <h1 class="text-xl font-bold text-[#1e293b] tracking-tight">@yield('title', 'Dashboard')</h1>
                    </div>

                    <div class="flex items-center space-x-4">

                        <!-- Perfil de usuario -->
                        <div class="flex items-center space-x-2 group cursor-pointer">
                            <div
                                class="h-9 w-9 rounded-full bg-gradient-to-r from-[#2b4642] to-[#1d3531] flex items-center justify-center text-white font-semibold shadow-md">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="hidden md:block">
                                <span
                                    class="text-sm font-medium text-[#1e293b] group-hover:text-[#2b4642]">{{ Auth::user()->name }}</span>
                                <p class="text-xs text-gray-500">Administrador</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Breadcrumbs -->
            <div class="px-6 pt-4">
                <nav class="flex" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2">
                        <li class="inline-flex items-center">
                            <a href="{{ route('admin.dashboard') }}"
                                class="inline-flex items-center text-sm font-medium text-gray-500 hover:text-[#2b4642] transition-colors">
                                <i class="fas fa-home mr-2 text-[#2b4642]"></i>
                                Inicio
                            </a>
                        </li>
                        @yield('breadcrumbs')
                    </ol>
                </nav>
            </div>

            <!-- Contenido -->
            <div class="p-6">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-sm p-6 mb-6 border border-white/50">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    @yield('scripts')
    <script>
        document.getElementById('logout-button')?.addEventListener('click', function(e) {
            Swal.fire({
                title: '¿Cerrar sesión?',
                text: 'Se cerrará tu sesión actual.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#2b4642',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Sí, salir',
                cancelButtonText: 'Cancelar',
                background: '#f8fafc',
                color: '#1e293b',
                customClass: {
                    title: 'text-[#2b4642]',
                    confirmButton: 'bg-[#2b4642] hover:bg-[#3a5a55] px-4 py-2 rounded-lg text-white',
                    cancelButton: 'bg-gray-600 hover:bg-gray-500 px-4 py-2 rounded-lg text-white'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        });

        const menuToggle = document.getElementById('menu-toggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('overlay');

        menuToggle?.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            document.body.classList.toggle('overflow-hidden');
        });

        overlay?.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        });
    </script>
</body>

</html>
