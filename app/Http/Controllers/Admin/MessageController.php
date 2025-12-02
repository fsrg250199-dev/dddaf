<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Client\ConnectionException;

class MessageController extends Controller
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
        $clients = $query->orderBy('name', 'asc')->paginate(20);

        // Mantener parámetros de búsqueda en la paginación
        $clients->appends($request->query());

        return view('admin.messages.index', compact('clients'));
    }
    private function sendMassive(Request $request, array $templates)
    {
        $validated = $request->validate([
            'selected_clients' => 'required|json',
        ]);

        $selectedClients = json_decode($validated['selected_clients'], true);
        if (empty($selectedClients)) {
            return back()->with('info', 'No se seleccionaron clientes.');
        }

        $chunkSize = 25;
        $clients = Client::whereIn('id', $selectedClients)
            ->where('is_active', true)
            ->get()
            ->filter(function ($client) {
                return $client->hasValidPhone();
            })
            ->chunk($chunkSize);

        if ($clients->isEmpty() || $clients->first()->isEmpty()) {
            return back()->with('info', 'No hay clientes válidos para enviar mensajes.');
        }

        $instance = config('services.ultramsg.instance');
        $token = config('services.ultramsg.token');

        $imageUrls = [
            'https://gitlab.com/xerydev/papeleriaimage/-/raw/main/papeleriadani.jpeg',
            'https://gitlab.com/xerydev/papeleriaimage/-/raw/main/pape1.jpeg',
            'https://gitlab.com/xerydev/papeleriaimage/-/raw/main/pape2.jpeg',
            'https://gitlab.com/xerydev/papeleriaimage/-/raw/main/pape3.jpeg',
            'https://gitlab.com/xerydev/papeleriaimage/-/raw/main/pape4.jpeg',
            'https://gitlab.com/xerydev/papeleriaimage/-/raw/main/pape5.jpeg',
        ];



        $totalSent = 0;
        $totalErrors = 0;

        foreach ($clients as $chunk) {
            try {
                $responses = Http::pool(function ($pool) use ($chunk, $templates, $instance, $token, $imageUrls) {
                    foreach ($chunk as $client) {
                        $randomTemplate = $templates[array_rand($templates)];
                        $caption = str_replace('{{nombre}}', $client->name, $randomTemplate);

                        // Selecciona una URL aleatoria de imagen para este cliente
                        $randomImageUrl = $imageUrls[array_rand($imageUrls)];

                        $pool->asForm()->post("https://api.ultramsg.com/{$instance}/messages/image", [
                            'token' => $token,
                            'to' => $client->whatsapp_number,
                            'image' => $randomImageUrl,
                            'caption' => $caption,
                        ]);
                    }
                });
            } catch (ConnectionException $e) {
                Log::error('Error de conexión UltraMSG: ' . $e->getMessage());
                $totalErrors += $chunk->count();
                continue;
            }

            foreach ($responses as $response) {
                if (!$response instanceof Response) {
                    Log::error('Respuesta inválida UltraMSG.');
                    $totalErrors++;
                    continue;
                }

                if ($response->failed()) {
                    Log::error('Error UltraMSG: ' . $response->body());
                    $totalErrors++;
                } else {
                    Log::info('UltraMSG OK: ' . $response->body());
                    $totalSent++;
                }
            }

            usleep(rand(2000000, 5000000));
        }

        $message = "Mensajes procesados. Enviados: {$totalSent}";
        if ($totalErrors > 0) {
            $message .= ", Errores: {$totalErrors}";
        }

        return back()->with('success', $message);
    }



    public function presentarPapeleria(Request $request)
    {
        $templates = [
            "Hola {{nombre}}! Soy Daniel Romo. 👋 Como encontré tu número en Google Maps, quería comentarte que ofrezco servicios digitales rápidos y 100% confiables. ¿Necesitas trámites legales para tu negocio, con entrega rápida? Ofrezco servicios 100% legales, precios competitivos, sin pagos adelantados y atención personalizada.¿Te gustaría hacer crecer tu negocio con estos servicios?ℹ️ Si necesita más información, se la compartimos con mucho gusto. ℹ️Si ya manejas estos servicios, estamos dispuestos a mejorarle precios. ¿Te interesa saber más?",

            "¡Hola {{nombre}}! Soy Daniel Romo. 👋 Ofrezco servicios digitales confiables y rápidos para tu negocio. Trámites legales con entrega inmediata, atención personalizada y precios competitivos.Si ya tienes estos servicios, podemos ofrecerte mejores precios. ℹ️ Para más información, con gusto te la compartimos.¿Quieres saber más?",

            "Buenos días {{nombre}}! Soy Daniel Romo 👋 Quería comentarte que ofrezco servicios digitales legales y rápidos para negocios. Sin pagos adelantados, con atención personalizada y precios competitivos.Si ya manejas estos servicios, estamos dispuestos a mejorarle precios. ℹ️ ¿Deseas más información sobre cómo podemos ayudarte?",

            "Hola {{nombre}}! Soy Daniel Romo 👋 Ofrezco servicios legales rápidos y confiables para negocios. Trámites, atención personalizada, entrega inmediata y precios competitivos.Si ya tienes estos servicios, podemos ofrecerte mejores precios. ℹ️ ¿Quieres que te comparta más detalles?",

            "¡Hola {{nombre}}! Soy Daniel Romo 👋 Encontré tu número en Google Maps y quería comentarte que puedo ayudarte con servicios digitales legales y rápidos para tu negocio. Atención personalizada y precios competitivos garantizados. Si ya manejas estos servicios, podemos mejorarle los precios. ℹ️ ¿Te interesa conocer más?"
        ];

        return $this->sendMassive($request, $templates);
    }


    public function ofertaServicios(Request $request)
    {
        $templates = [
            "🎉 ¡Oferta especial {{nombre}}! Descuentos en trámites de RFC y actas de nacimiento esta semana. Aprovecha nuestros precios únicos en papelería completa.",
            "⚡ {{nombre}}, promoción limitada: 2x1 en impresiones a color o 15% de descuento en trámites federales. ¡Tú decides! Válido hasta fin de mes.",
            "💰 ¡{{nombre}}, no te pierdas esta oportunidad! Paquete especial: RFC + CURP + copia de acta = precio increíble. Tu papelería de confianza te lo ofrece.",
            "🔥 Promoción {{nombre}}: Servicios de papelería con descuentos especiales. Trámites gubernamentales, copias certificadas, impresiones... ¡Todo con el mejor precio!",
            "🌟 {{nombre}}, precios especiales solo por tiempo limitado. Papelería completa: desde trámites oficiales hasta servicios de copiado. ¡Cotiza ya!"
        ];
        return $this->sendMassive($request, $templates);
    }

    public function infoServicios(Request $request)
    {
        $templates = [
            "📞 {{nombre}}, recordatorio de nuestros servicios disponibles: RFC, actas de nacimiento, CURP, credenciales, impresiones, copias y mucho más. ¡Estamos para ayudarte!",
            "🏢 Hola {{nombre}}, tu papelería de confianza tiene todos los servicios que necesitas: gestión de trámites gubernamentales, documentos oficiales y servicios de impresión profesional.",
            "📄 {{nombre}}, ¿sabías que manejamos todos los trámites oficiales? RFC, actas, CURP, credenciales de elector... además de servicios tradicionales de papelería. ¡Consulta!",
            "✅ {{nombre}}, servicios disponibles en tu papelería: 🏛️ Trámites de gobierno 📋 Documentos oficiales 🖨️ Impresiones y copias 📑 Gestión integral de papelería.",
            "⏰ {{nombre}}, recuerda que estamos disponibles para todos tus trámites y necesidades de papelería. Desde documentos gubernamentales hasta servicios de copiado rápido."
        ];
        return $this->sendMassive($request, $templates);
    }

    public function seguimientoClientes(Request $request)
    {
        $templates = [
            "🙏 {{nombre}}, gracias por confiar en nuestra papelería. Seguimos comprometidos en brindarte el mejor servicio en trámites y documentos. ¡Eres muy importante para nosotros!",
            "❤️ Estimado {{nombre}}, valoramos tu preferencia hacia nuestros servicios de papelería. Continuamos trabajando para ofrecerte la mejor atención en todos tus trámites.",
            "✨ {{nombre}}, agradecemos que nos hayas elegido como tu papelería de confianza. Tu satisfacción nos motiva a mejorar cada día nuestros servicios.",
            "🌟 Gracias {{nombre}} por ser parte de nuestra familia en esta papelería. Seguimos aquí para apoyarte con todos tus documentos y trámites oficiales.",
            "💼 {{nombre}}, tu confianza en nuestros servicios de papelería y trámites gubernamentales es muy valiosa. ¡Gracias por elegirnos siempre como tu primera opción!"
        ];
        return $this->sendMassive($request, $templates);
    }
}
