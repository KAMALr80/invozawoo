<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SystemNotification extends Notification
{
    use Queueable;

    protected $title;
    protected $message;
    protected $url;
    protected $icon;

    /**
     * Create a new notification instance.
     */
    public function __construct($title, $message, $url = '#', $icon = 'fas fa-info-circle')
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
        $this->icon = $icon;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
            'icon' => $this->icon,
            'time' => now()->toDateTimeString(),
        ];
    }
}
