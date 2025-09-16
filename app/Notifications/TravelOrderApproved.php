<?php

namespace App\Notifications;

use App\Models\TravelOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TravelOrderApproved extends Notification
{
    use Queueable;

    protected $travelOrder;

    /**
     * Create a new notification instance.
     */
    public function __construct(TravelOrder $travelOrder)
    {
        $this->travelOrder = $travelOrder;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Travel Order Approved')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your travel order has been approved.')
            ->line('Order ID: ' . $this->travelOrder->order_id)
            ->line('Destination: ' . $this->travelOrder->destination)
            ->line('Departure Date: ' . $this->travelOrder->departure_date)
            ->line('Return Date: ' . $this->travelOrder->return_date)
            ->line('Thank you for using our travel service!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'travel_order_approved',
            'travel_order_id' => $this->travelOrder->id,
            'order_id' => $this->travelOrder->order_id,
            'destination' => $this->travelOrder->destination,
            'departure_date' => $this->travelOrder->departure_date,
            'return_date' => $this->travelOrder->return_date,
            'approved_at' => $this->travelOrder->approved_at
                ? $this->travelOrder->approved_at->format('Y-m-d H:i:s')
                : null,
        ];
    }
}