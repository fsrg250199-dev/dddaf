<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Scope para clientes activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Formatear teléfono para WhatsApp
    public function getWhatsappNumberAttribute()
    {
        // Limpiamos el número y le agregamos @c.us para WhatsApp
        $cleanPhone = preg_replace('/[^0-9]/', '', $this->phone);
        return $cleanPhone . '@c.us';
    }

    // Verificar si el teléfono es válido
    public function hasValidPhone()
    {
        return !empty($this->phone) && strlen(preg_replace('/[^0-9]/', '', $this->phone)) >= 10;
    }
}
