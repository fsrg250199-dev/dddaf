<?php

namespace App\Imports;

use App\Models\Client;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ClientsImport implements
    ToModel,
    WithHeadingRow,
    WithValidation,
    SkipsOnError,
    SkipsOnFailure,
    WithBatchInserts,
    WithChunkReading
{
    use SkipsErrors, SkipsFailures, Importable;

    private $rowNumber = 0;
    private $imported = 0;
    private $skipped = 0;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $this->rowNumber++;

        // Log para depuración
        Log::info("Fila {$this->rowNumber} - Datos recibidos:", $row);

        // Obtener nombre (compatible con exportación e importación)
        $nombre = isset($row['nombre']) ? trim($row['nombre']) : (isset($row['name']) ? trim($row['name']) : '');

        // Obtener teléfono (compatible con exportación e importación)
        $telefonoRaw = $row['telefono'] ?? $row['phone'] ?? $row['tel'] ?? '';

        // Convertir a string si es necesario y limpiar (solo números)
        $telefono = preg_replace('/[^0-9]/', '', (string)$telefonoRaw);

        // Log de procesamiento
        Log::info("Fila {$this->rowNumber} - Procesado:", [
            'nombre' => $nombre,
            'telefono_raw' => $telefonoRaw,
            'telefono_limpio' => $telefono,
            'longitud_telefono' => strlen($telefono)
        ]);

        // Validar que tengamos los datos mínimos (nombre Y teléfono con al menos 10 dígitos)
        if (empty($nombre)) {
            $this->skipped++;
            Log::warning("Fila {$this->rowNumber} - OMITIDA: Nombre vacío");
            return null;
        }

        if (strlen($telefono) < 10) {
            $this->skipped++;
            Log::warning("Fila {$this->rowNumber} - OMITIDA: Teléfono inválido", [
                'telefono' => $telefono,
                'longitud' => strlen($telefono)
            ]);
            return null;
        }

        // Obtener email (compatible con ambos formatos)
        $email = isset($row['email']) ? trim($row['email']) : (isset($row['correo']) ? trim($row['correo']) : null);
        if ($email === '' || $email === 'N/A') $email = null;

        // Obtener dirección (compatible con ambos formatos)
        $direccion = isset($row['direccion']) ? trim($row['direccion']) : (isset($row['address']) ? trim($row['address']) : null);
        if ($direccion === '' || $direccion === 'N/A') $direccion = null;

        // Obtener notas (compatible con ambos formatos)
        $notas = isset($row['notas']) ? trim($row['notas']) : (isset($row['notes']) ? trim($row['notes']) : null);
        if ($notas === '' || $notas === 'N/A') $notas = null;

        // Verificar si ya existe este cliente por teléfono
        $existente = Client::where('phone', $telefono)->first();

        if ($existente) {
            Log::info("Fila {$this->rowNumber} - Cliente existente, actualizando ID: {$existente->id}");
            $existente->update([
                'name'    => $nombre,
                'email'   => $email,
                'address' => $direccion,
                'notes'   => $notas,
            ]);
            $this->imported++;
            return null; // No crear nuevo
        }

        // Si existe ID en el archivo, intentar actualizar
        if (!empty($row['id'])) {
            $client = Client::find($row['id']);
            if ($client) {
                Log::info("Fila {$this->rowNumber} - Actualizando cliente ID: {$row['id']}");
                $client->update([
                    'name'    => $nombre,
                    'phone'   => $telefono,
                    'email'   => $email,
                    'address' => $direccion,
                    'notes'   => $notas,
                ]);
                $this->imported++;
                return null;
            }
        }

        $this->imported++;
        Log::info("Fila {$this->rowNumber} - Creando nuevo cliente: {$nombre}");

        return new Client([
            'name'      => $nombre,
            'phone'     => $telefono,
            'email'     => $email,
            'address'   => $direccion,
            'notes'     => $notas,
            'is_active' => true,
        ]);
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }

    /**
     * Reglas de validación
     */
    public function rules(): array
    {
        return [
            'nombre' => 'nullable|string|max:255',
            'name' => 'nullable|string|max:255',
            'telefono' => 'nullable|max:20',
            'phone' => 'nullable|max:20',
            'email' => 'nullable|email|max:255',
            'correo' => 'nullable|email|max:255',
            'direccion' => 'nullable|max:500',
            'address' => 'nullable|max:500',
            'notas' => 'nullable|max:1000',
            'notes' => 'nullable|max:1000',
        ];
    }

    /**
     * Obtener estadísticas de importación
     */
    public function getStats()
    {
        return [
            'imported' => $this->imported,
            'skipped' => $this->skipped,
            'total' => $this->rowNumber
        ];
    }
}
