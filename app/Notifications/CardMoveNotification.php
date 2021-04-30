<?php

namespace App\Notifications;

use App\ListModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CardMoveNotification extends Notification
{
    use Queueable;

    protected $card = null;
    protected $fromList = null;
    protected $toList = null;

    /**
     * Create a new notification instance.
     *
     * @param \App\Card $card
     * @param array $listData
     *
     * @return void
     */
    public function __construct($card, $listData)
    {
        $this->card = $card;
        $this->fromList = $listData['from'];
        $this->toList = $listData['to'];
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['array'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $user = auth()->user();

        return [
            'user_id' => $user->id,
            'card_id' => $this->card->id,
            'from' => $this->fromList,
            'to' => $this->toList,
        ];
    }
}
