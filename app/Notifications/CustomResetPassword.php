<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomResetPassword extends Notification
{
    use Queueable;

    /**
     * El token de restablecimiento
     *
     * @var string
     */
    public string $token;

    /**
     * Crear nueva notificación
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Canales de notificación
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Construir el mail
     */
    public function toMail(object $notifiable): MailMessage
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('e-SPA · Restablecimiento de contraseña') // 👈 asunto del mail
            ->greeting('Hola 👋')
            ->line('Has solicitado restablecer tu contraseña en **e-SPA**.')
            ->action('Crear nueva contraseña', $url)
            ->line('Por seguridad, este enlace caduca en '  //[
            //    'count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')
            //])
                . config('auth.passwords.' . config('auth.defaults.passwords') . '.expire') 
    . ' minutos.') 
            ->line('Si no fuiste tú quien solicitó el cambio, puedes ignorar este correo.');
    }

    /**
     * Representación en array (no usada aquí)
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
