<?php

namespace App\Exports;

use App\Models\Client;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    /**
     * Retorna la colección de clientes
     */
    public function collection()
    {
        return Client::orderBy('name')->get();
    }

    /**
     * Mapea los datos de cada cliente
     */
    public function map($client): array
    {
        return [
            $client->id,
            $client->name,
            $client->phone,
            $client->email ?? '',
            $client->address ?? '',
            $client->notes ?? '',
            $client->is_active ? 'Activo' : 'Inactivo',
            $client->created_at->format('d/m/Y H:i'),
        ];
    }

    /**
     * Encabezados de las columnas (coinciden con la plantilla de importación)
     */
    public function headings(): array
    {
        return [
            'id',
            'nombre',
            'telefono',
            'email',
            'direccion',
            'notas',
            'estado',
            'fecha_registro',
        ];
    }

    /**
     * Estilos de la hoja
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }
}
