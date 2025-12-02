@extends('layouts.admin')

@section('title', 'Clientes')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-6 space-y-4 md:space-y-0">
        <h1 class="text-2xl font-bold text-gray-800">Gestión de Clientes</h1>
        <a href="{{ route('admin.clients.create') }}"
            class="px-4 py-2 bg-verde-claro text-white rounded-lg hover:bg-verde-claro transition-colors duration-200 flex items-center space-x-2">
            <i class="fas fa-plus-circle"></i>
            <span>Nuevo Cliente</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6 border">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-comment-dots text-verde-claro mr-2"></i>
            Mensajes para Clientes
        </h2>

        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-verde-claro mt-1 mr-3"></i>
                <div>
                    <h3 class="font-semibold text-verde-oscuro mb-2">Envío de mensajes promocionales</h3>
                    <p class="text-sm text-verde-oscuro">
                        Selecciona uno o más clientes y elige el tipo de mensaje a enviar.
                        Los mensajes se personalizarán automáticamente con el nombre de cada cliente.
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <form action="{{ route('admin.messages.presentar-papeleria') }}" method="POST" class="message-form">
                @csrf
                <input type="hidden" name="selected_clients" id="selected_clients_presentacion">
                <button type="submit"
                    class="w-full px-4 py-2.5 font-bold bg-green-700 text-white rounded-lg hover:bg-green-800 transition-colors duration-200">
                    📄 Presentar Papelería
                </button>
            </form>

            {{-- <form action="{{ route('admin.messages.oferta-servicios') }}" method="POST" class="message-form">
                @csrf
                <input type="hidden" name="selected_clients" id="selected_clients_oferta">
                <button type="submit"
                    class="w-full px-4 py-2.5 font-bold bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                    🎉 Ofertas Especiales
                </button>
            </form>

            <form action="{{ route('admin.messages.info-servicios') }}" method="POST" class="message-form">
                @csrf
                <input type="hidden" name="selected_clients" id="selected_clients_info">
                <button type="submit"
                    class="w-full px-4 py-2.5 font-bold bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                    📋 Info Servicios
                </button>
            </form>

            <form action="{{ route('admin.messages.seguimiento-clientes') }}" method="POST" class="message-form">
                @csrf
                <input type="hidden" name="selected_clients" id="selected_clients_seguimiento">
                <button type="submit"
                    class="w-full px-4 py-2.5 font-bold bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                    🙏 Seguimiento
                </button>
            </form> --}}
        </div>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6 border">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <i class="fas fa-filter text-verde-claro mr-2"></i>
            Filtros de Búsqueda
        </h2>

        <form method="GET" action="{{ route('admin.message.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                        Buscar (nombre, teléfono o email)
                    </label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}"
                        placeholder="Ej. Juan Pérez o 3312345678"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-verde-claro focus:border-verde-claro">
                </div>

                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Estado del cliente
                    </label>
                    <select id="status" name="status"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-verde-claro focus:border-verde-claro">
                        <option value="">Todos los estados</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Activos</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.message.index') }}"
                    class="px-5 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Limpiar Filtros
                </a>
                <button type="submit"
                    class="px-5 py-2 bg-verde-claro text-white rounded-lg hover:bg-verde-oscuro transition-colors">
                    Aplicar Filtros
                </button>
            </div>
        </form>
    </div>

    <!-- Selección Masiva -->
    <div class="bg-white rounded-2xl shadow-sm p-6 mb-6 border">
        <div class="flex justify-between items-center">
            <button id="select-all"
                class="px-4 py-2 bg-verde-claro text-white rounded-lg hover:bg-verde-oscuro transition-colors">
                <i class="fas fa-check-square mr-2"></i>Seleccionar Todos
            </button>
        </div>
    </div>

    <!-- Paginación -->
    @if ($clients->hasPages())
        <div class="mt-6">
            {{ $clients->links() }}
        </div>
    @endif

    <!-- Tabla de Clientes -->
    <div class="bg-white rounded-2xl shadow-sm border overflow-hidden">
        <!-- Vista Desktop -->
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left">
                            <input type="checkbox" id="select-all-checkbox" class="rounded">
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">#</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Nombre</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Teléfono</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Estado</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-700 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($clients as $client)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="client-checkbox rounded" data-id="{{ $client->id }}">
                            </td>
                            <td class="px-4 py-3 text-sm font-medium">{{ $client->id }}</td>
                            <td class="px-4 py-3">
                                <div class="text-sm font-bold text-gray-900">{{ $client->name }}</div>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $client->phone }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $client->email ?? 'N/A' }}</td>
                            <td class="px-4 py-3">
                                <span
                                    class="px-2 py-1 text-xs rounded-full {{ $client->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $client->is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex gap-2">
                                    <a href="{{ route('admin.clients.edit', $client) }}"
                                        class="px-3 py-1 bg-verde-claro text-white rounded hover:bg-verde-oscuro text-xs">
                                        Editar
                                    </a>

                                    <form action="{{ route('admin.clients.toggle-status', $client) }}" method="POST"
                                        class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="px-3 py-1 {{ $client->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded text-xs">
                                            {{ $client->is_active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Vista Mobile -->
        <div class="md:hidden space-y-4 p-4">
            @foreach ($clients as $client)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center">
                            <input type="checkbox" class="client-checkbox rounded mr-3" data-id="{{ $client->id }}">
                            <div>
                                <h3 class="font-bold text-gray-900">{{ $client->name }}</h3>
                                <p class="text-xs text-gray-500">ID: {{ $client->id }}</p>
                            </div>
                        </div>
                        <span
                            class="px-2 py-1 text-xs rounded-full {{ $client->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $client->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                    <div class="space-y-1 text-sm text-gray-600">
                        <div><i class="fas fa-phone mr-2"></i>{{ $client->phone }}</div>
                        <div><i class="fas fa-envelope mr-2"></i>{{ $client->email ?? 'N/A' }}</div>
                    </div>
                    <div class="flex gap-2 mt-3">
                        <a href="{{ route('admin.clients.edit', $client) }}"
                            class="flex-1 px-3 py-1 bg-verde-claro text-white rounded hover:bg-verde-oscuro text-xs text-center">
                            Editar
                        </a>
                        <form action="{{ route('admin.clients.toggle-status', $client) }}" method="POST"
                            class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="w-full px-3 py-1 {{ $client->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded text-xs">
                                {{ $client->is_active ? 'Desactivar' : 'Activar' }}
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection


@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: '{{ session('success') }}',
                confirmButtonColor: '#1e40af',
                customClass: {
                    title: 'text-verde-oscuro',
                    confirmButton: 'bg-verde-oscuro hover:bg-verde-claro px-4 py-2 rounded-lg'
                }
            });
        });
    </script>
@endif
@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const selectAllBtn = document.getElementById("select-all");
            const selectAllCheckbox = document.getElementById("select-all-checkbox");
            const clientCheckboxes = document.querySelectorAll(".client-checkbox");

            // Seleccionar todos
            selectAllBtn.addEventListener("click", function() {
                const shouldCheck = !selectAllCheckbox.checked;
                selectAllCheckbox.checked = shouldCheck;
                clientCheckboxes.forEach(cb => cb.checked = shouldCheck);
            });

            // Sincronizar checkbox principal con individuales
            selectAllCheckbox.addEventListener("change", function() {
                clientCheckboxes.forEach(cb => cb.checked = this.checked);
            });

            // Validar y enviar formularios
            document.querySelectorAll(".message-form").forEach(form => {
                form.addEventListener("submit", function(e) {
                    e.preventDefault();

                    // Obtener IDs de clientes seleccionados
                    const selectedClients = Array.from(clientCheckboxes)
                        .filter(cb => cb.checked)
                        .map(cb => parseInt(cb.dataset.id));

                    if (selectedClients.length === 0) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Selecciona clientes',
                            text: 'Debes seleccionar al menos un cliente para enviar mensajes.',
                            confirmButtonColor: '#1e40af',
                            customClass: {
                                title: 'text-verde-oscuro',
                                confirmButton: 'bg-verde-oscuro hover:bg-verde-claro px-4 py-2 rounded-lg'
                            }
                        });
                        return;
                    }

                    // Llenar el input hidden con los IDs seleccionados
                    const hiddenInput = this.querySelector('input[name="selected_clients"]');
                    hiddenInput.value = JSON.stringify(selectedClients);

                    // Mostrar confirmación y deshabilitar botón
                    const button = this.querySelector('button[type="submit"]');
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Enviando...';

                    // Enviar el formulario
                    this.submit();
                });
            });
        });
    </script>
@endsection
