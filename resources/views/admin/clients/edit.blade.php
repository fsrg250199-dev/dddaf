@extends('layouts.admin')

@section('title', 'Editar Cliente')

@section('content')
    <div class="flex items-center mb-6">
        <a href="{{ route('admin.clients.index') }}" class="text-gray-600 hover:text-gray-900 mr-4">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-gray-800">Editar Cliente</h1>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <form action="{{ route('admin.clients.update', $client) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre Completo *
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $client->name) }}" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('name') border-red-500 @enderror"
                        placeholder="Ej. Juan Pérez González">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Teléfono -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                        Teléfono *
                    </label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone', $client->phone) }}" required
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('phone') border-red-500 @enderror"
                        placeholder="Ej. 3312345678">
                    @error('phone')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $client->email) }}"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror"
                        placeholder="ejemplo@correo.com">
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">
                        Estado
                    </label>
                    <select id="is_active" name="is_active"
                        class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="1" {{ old('is_active', $client->is_active) == '1' ? 'selected' : '' }}>Activo
                        </option>
                        <option value="0" {{ old('is_active', $client->is_active) == '0' ? 'selected' : '' }}>Inactivo
                        </option>
                    </select>
                    @error('is_active')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Dirección -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                    Dirección
                </label>
                <textarea id="address" name="address" rows="3"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('address') border-red-500 @enderror"
                    placeholder="Dirección completa del cliente">{{ old('address', $client->address) }}</textarea>
                @error('address')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Notas -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notas Adicionales
                </label>
                <textarea id="notes" name="notes" rows="3"
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-500 @enderror"
                    placeholder="Observaciones, preferencias, etc.">{{ old('notes', $client->notes) }}</textarea>
                @error('notes')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('admin.clients.index') }}"
                    class="px-6 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-verde-claro text-white rounded-lg hover:bg-verde-oscuro transition-colors flex items-center">
                    <i class="fas fa-save mr-2"></i>
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>

    <script>
        // Formatear teléfono mientras se escribe
        document.getElementById('phone').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Solo números
            if (value.length > 10) value = value.substring(0, 10);
            e.target.value = value;
        });
    </script>
@endsection
