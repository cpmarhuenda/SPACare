<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Mensaje;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class NuevoMensajeRecibido extends Notification
{
    use Queueable;

    public Mensaje $mensaje;

    public function __construct(Mensaje $mensaje)
    {
        $this->mensaje = $mensaje;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $remitente = $this->mensaje->remitente?->nombre_completo ?? 'Usuario desconocido';

          $imagePath = public_path('images/logo_spacare.png');

     $base64 = '';
        if (File::exists($imagePath)) {
        $type = pathinfo($imagePath, PATHINFO_EXTENSION);
        $data = File::get($imagePath);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    }



        return (new MailMessage)
            ->subject('Nuevo mensaje recibido')
            ->greeting("Hola {$notifiable->name},")
            ->line("Has recibido un nuevo mensaje de {$remitente}.")
            ->line('Contenido del mensaje:')
            ->line($this->mensaje->contenido)
            ->action('Ver mensajes', url('/admin/resource/mensaje-recibido-resource/index-page'))
            ->line('Gracias por usar e-SPA.')
               ->line(' '); 
    }
}
