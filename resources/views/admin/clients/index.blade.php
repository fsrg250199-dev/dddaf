@extends('layouts.admin')

@section('title', 'Clientes')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Clientes</h1>

        <div class="flex gap-2">
            <!-- Botón Descargar Plantilla -->
            <a href="{{ route('admin.clients.template') }}"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors flex items-center">
                <i class="fas fa-file-download mr-2"></i> Plantilla
            </a>

            <!-- Botón Importar -->
            <button onclick="document.getElementById('importModal').classList.remove('hidden')"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center">
                <i class="fas fa-file-import mr-2"></i> Importar
            </button>

            <!-- Botón Exportar -->
            <a href="{{ route('admin.clients.export') }}"
                class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center">
                <i class="fas fa-file-export mr-2"></i> Exportar
            </a>

            <!-- Botón Nuevo Cliente -->
            <a href="{{ route('admin.clients.create') }}"
                class="px-4 py-2 bg-verde-claro text-white rounded-lg hover:bg-verde-oscuro transition-colors flex items-center">
                <i class="fas fa-plus mr-2"></i> Nuevo Cliente
            </a>
        </div>
    </div>

    <!-- Modal de Importación -->
    <div id="importModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-lg bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Importar Clientes</h3>
                <button onclick="document.getElementById('importModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <form action="{{ route('admin.clients.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Seleccionar archivo Excel
                    </label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-verde-claro">
                    <p class="text-xs text-gray-500 mt-1">
                        Formatos permitidos: .xlsx, .xls, .csv (máx. 2MB)
                    </p>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Columnas requeridas:</strong><br>
                        nombre, telefono
                    </p>
                </div>

                <div class="flex gap-2">
                    <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')"
                        class="flex-1 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="flex-1 px-4 py-2 bg-verde-oscuro text-white rounded-lg hover:bg-verde-claro transition-colors">
                        Importar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alertas SweetAlert -->
    @if (session('success') || session('info') || session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if (session('success'))
                    Swal.fire({
                        icon: 'success',
                        title: 'Éxito',
                        text: '{{ session('success') }}',
                        confirmButtonColor: '#1e40af',
                        background: '#f8fafc',
                        customClass: {
                            title: 'text-blue-800',
                            confirmButton: 'bg-verde-oscuro hover:bg-verde-claro px-4 py-2 rounded-lg'
                        }
                    });
                @endif

                @if (session('info'))
                    Swal.fire({
                        icon: 'info',
                        title: 'Aviso',
                        text: '{{ session('info') }}',
                        confirmButtonColor: '#1e40af',
                        background: '#f8fafc',
                        customClass: {
                            title: 'text-blue-800',
                            confirmButton: 'bg-verde-oscuro hover:bg-verde-claro px-4 py-2 rounded-lg'
                        }
                    });
                @endif

                @if (session('error'))
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '{{ session('error') }}',
                        confirmButtonColor: '#dc2626',
                        background: '#f8fafc',
                        customClass: {
                            title: 'text-red-800',
                            confirmButton: 'bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg'
                        }
                    });
                @endif
            });
        </script>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <table class="w-full table-auto border-collapse">
            <thead>
                <tr class="bg-gray-100 text-left">
                    <th class="px-4 py-2 border-b">Nombre</th>
                    <th class="px-4 py-2 border-b">Teléfono</th>
                    <th class="px-4 py-2 border-b">Email</th>
                    <th class="px-4 py-2 border-b">Dirección</th>
                    <th class="px-4 py-2 border-b">Estado</th>
                    <th class="px-4 py-2 border-b">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-2 border-b">{{ $client->name }}</td>
                        <td class="px-4 py-2 border-b">{{ $client->phone }}</td>
                        <td class="px-4 py-2 border-b">{{ $client->email ?? 'N/A' }}</td>
                        <td class="px-4 py-2 border-b">{{ $client->address ?? 'N/A' }}</td>
                        <td class="px-4 py-2 border-b">
                            @if ($client->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Activo</span>
                            @else
                                <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Inactivo</span>
                            @endif
                        </td>
                        <td class="px-4 py-2 border-b flex gap-2">
                            <a href="{{ route('admin.clients.edit', $client) }}"
                                class="px-2 py-1 bg-verde-oscuro text-white rounded hover:bg-verde-claro transition-colors">
                                Editar
                            </a>

                            <form action="{{ route('admin.clients.toggle-status', $client) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="px-2 py-1 rounded text-white transition-colors {{ $client->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }}">
                                    {{ $client->is_active ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center text-gray-500">No hay clientes registrados.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $clients->links() }}
        </div>
    </div>
@endsection
