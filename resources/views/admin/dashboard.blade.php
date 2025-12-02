@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Bienvenido, {{ auth()->user()->name }}</h1>
        <p class="text-gray-600 mt-2">Resumen de tu papelería</p>
    </div>

    <!-- Tarjetas de resumen -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total de clientes -->
        <div class="bg-white rounded-2xl shadow-sm border p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total de Clientes</p>
                    <h3 class="text-3xl font-bold text-gray-800 mt-2">{{ $totalClients }}</h3>
                </div>
                <div class="p-3 bg-verde-claro/10 rounded-full">
                    <i class="fas fa-users text-2xl text-verde-oscuro"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-gray-500">
                <i class="fas fa-chart-line mr-1 text-green-500"></i>
                <span>{{ $monthlyStats['clients_this_month'] }} nuevos este mes</span>
            </div>
        </div>

        <!-- Clientes activos -->
        <div class="bg-white rounded-2xl shadow-sm border p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Clientes Activos</p>
                    <h3 class="text-3xl font-bold text-green-600 mt-2">{{ $activeClients }}</h3>
                </div>
                <div class="p-3 bg-green-100 rounded-full">
                    <i class="fas fa-check-circle text-2xl text-green-600"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full"
                        style="width: {{ $totalClients > 0 ? ($activeClients / $totalClients) * 100 : 0 }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $totalClients > 0 ? number_format(($activeClients / $totalClients) * 100, 1) : 0 }}% del total
                </p>
            </div>
        </div>

        <!-- Clientes inactivos -->
        <div class="bg-white rounded-2xl shadow-sm border p-6 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Clientes Inactivos</p>
                    <h3 class="text-3xl font-bold text-red-600 mt-2">{{ $inactiveClients }}</h3>
                </div>
                <div class="p-3 bg-red-100 rounded-full">
                    <i class="fas fa-pause-circle text-2xl text-red-600"></i>
                </div>
            </div>
            <div class="mt-4">
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-red-600 h-2 rounded-full"
                        style="width: {{ $totalClients > 0 ? ($inactiveClients / $totalClients) * 100 : 0 }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1">
                    {{ $totalClients > 0 ? number_format(($inactiveClients / $totalClients) * 100, 1) : 0 }}% del total
                </p>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="grid grid-cols-1 md:grid-cols-1 gap-6 mb-8">

        <!-- Últimos clientes -->
        <div class="bg-white rounded-2xl shadow-sm border p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Últimos Clientes</h3>
                <a href="{{ route('admin.clients.index') }}" class="text-sm text-verde-oscuro hover:underline">
                    Ver todos
                </a>
            </div>
            <div class="space-y-3">
                @forelse($recentClients as $client)
                    <div class="flex items-center justify-between p-2 hover:bg-gray-50 rounded">
                        <div class="flex items-center">
                            <div
                                class="w-8 h-8 bg-verde-claro rounded-full flex items-center justify-center text-white text-sm font-bold">
                                {{ strtoupper(substr($client->name, 0, 1)) }}
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-800">{{ $client->name }}</p>
                                <p class="text-xs text-gray-500">{{ $client->phone }}</p>
                            </div>
                        </div>
                        <span
                            class="px-2 py-1 text-xs rounded-full {{ $client->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $client->is_active ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                @empty
                    <p class="text-gray-500 text-sm">No hay clientes registrados</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Acceso rápido -->
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Acceso Rápido</h3>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.clients.index') }}"
                class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-users text-2xl text-verde-oscuro mb-2"></i>
                <span class="text-sm font-medium">Clientes</span>
            </a>
            <a href="{{ route('admin.clients.create') }}"
                class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-plus text-2xl text-green-600 mb-2"></i>
                <span class="text-sm font-medium">Nuevo Cliente</span>
            </a>
            <a href="{{ route('admin.message.index') }}"
                class="flex flex-col items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                <i class="fas fa-envelope text-2xl text-blue-600 mb-2"></i>
                <span class="text-sm font-medium">Mensajes</span>
            </a>
        </div>
    </div>
@endsection
