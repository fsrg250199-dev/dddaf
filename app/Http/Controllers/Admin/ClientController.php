<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use App\Imports\ClientsImport;
use App\Exports\ClientsExport;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class ClientController extends Controller
{

    public function index(Request $request)
    {
        $query = Client::query();

        // Filtro de búsqueda por nombre, teléfono o email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por estado (activo/inactivo)
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Ordenar por nombre y paginar
        $clients = $query->orderBy('id', 'asc')->paginate(20);

        // Mantener parámetros de búsqueda en la paginación
        $clients->appends($request->query());

        return view('admin.clients.index', compact('clients'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        // Limpiar el teléfono (solo números)
        $validated['phone'] = preg_replace('/[^0-9]/', '', $validated['phone']);

        // Crear el cliente
        Client::create($validated);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente creado exitosamente');
    }

    public function show(Client $client)
    {
        return view('admin.clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'is_active' => 'boolean'
        ]);

        // Limpiar el teléfono (solo números)
        $validated['phone'] = preg_replace('/[^0-9]/', '', $validated['phone']);

        // Actualizar el cliente
        $client->update($validated);

        return redirect()->route('admin.clients.index')
            ->with('success', 'Cliente actualizado exitosamente');
    }

    public function destroy(Client $client)
    {
        try {
            $client->delete();
            return redirect()->route('admin.clients.index')
                ->with('success', 'Cliente eliminado exitosamente');
        } catch (\Exception $e) {
            return redirect()->route('admin.clients.index')
                ->with('error', 'Error al eliminar el cliente');
        }
    }

    /**
     * Cambiar Status
     */
    public function toggleStatus(Client $client)
    {
        $client->update(['is_active' => !$client->is_active]);

        $status = $client->is_active ? 'activado' : 'desactivado';

        return redirect()->back()
            ->with('success', "Cliente {$status} exitosamente");
    }

    public function getClients(Request $request)
    {
        $query = Client::active();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");
            });
        }

        $clients = $query->select('id', 'name', 'phone')
            ->orderBy('name')
            ->limit(50)
            ->get();

        return response()->json($clients);
    }

    /**
     * Exportar clientes a Excel
     */
    public function export()
    {
        return Excel::download(new ClientsExport, 'clientes_' . date('Y-m-d_His') . '.xlsx');
    }

    /**
     * Importar clientes desde Excel
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        try {
            $import = new ClientsImport;
            Excel::import($import, $request->file('file'));

            $stats = $import->getStats();
            $erroresCount = count($import->failures());

            $message = "Se procesaron {$stats['total']} filas: ";
            $message .= "{$stats['imported']} importados/actualizados";

            if ($stats['skipped'] > 0) {
                $message .= ", {$stats['skipped']} omitidos";
            }

            if ($erroresCount > 0) {
                $message .= ", {$erroresCount} con errores de validación";
            }

            Log::info('Importación completada', $stats);

            return redirect()->route('admin.clients.index')
                ->with('success', $message);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errores = [];

            foreach ($failures as $failure) {
                $errores[] = "Fila {$failure->row()}: " . implode(', ', $failure->errors());
            }

            Log::error('Errores de validación en importación', ['errores' => $errores]);

            return redirect()->route('admin.clients.index')
                ->with('error', 'Errores de validación: ' . implode(' | ', array_slice($errores, 0, 3)));
        } catch (\Exception $e) {
            Log::error('Error en importación de clientes', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'file' => $request->file('file')->getClientOriginalName()
            ]);

            return redirect()->route('admin.clients.index')
                ->with('error', 'Error al importar clientes: ' . $e->getMessage());
        }
    }

    /**
     * Descargar plantilla de ejemplo
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="plantilla_clientes.csv"',
        ];

        $columns = ['nombre', 'telefono', 'email', 'direccion', 'notas'];
        $example = ['Juan Pérez', '3331234567', 'juan@example.com', 'Calle Ejemplo #123', 'Cliente frecuente'];

        $callback = function () use ($columns, $example) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, $example);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
