<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Tu contraseña ha sido cambiada')
            ->greeting('Hola ' . $notifiable->nombre_usuario . '!')
            ->line('Se ha cambiado la contraseña de tu cuenta.')
            ->line('Si no realizaste este cambio, por favor contacta al administrador inmediatamente.')
            ->action('Acceder al sistema', url('/login'))
            ->line('Gracias por usar nuestra aplicación!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'titulo' => 'Cambio de contraseña',
            'mensaje' => 'Se ha actualizado la contraseña de tu cuenta',
            'fecha' => now()->toDateTimeString(),
            'accion' => url('/configuracion'),
            'tipo' => 'password_changed'
        ];
    }
}