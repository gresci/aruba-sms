<?php

namespace NotificationChannels\SmscAruba;

use Illuminate\Notifications\Notification;
use NotificationChannels\SmscAruba\Exceptions\CouldNotSendNotification;

class SmscArubaChannel
{
    /** @var \NotificationChannels\SmscAruba\SmscArubaApi */
    protected $smsc;

    public function __construct(SmscArubaApi $smsc)
    {
        $this->smsc = $smsc;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     *
     * @throws  \NotificationChannels\SmscAruba\Exceptions\CouldNotSendNotification
     */
    public function send($notifiable, Notification $notification)
    {
        $to = $notifiable->routeNotificationFor('smsaruba');

        if (empty($to)) {
            throw CouldNotSendNotification::missingRecipient();
        }

        $message = $notification->toSmscAruba($notifiable);

        if (is_string($message)) {
            $message = new SmscArubaMessage($message);
        }

        $this->sendMessage($to, $message);
    }

    protected function sendMessage($recipient, SmscArubaMessage $message)
    {
        if (mb_strlen($message->content) > 800) {
            throw CouldNotSendNotification::contentLengthLimitExceeded();
        }

        $params = [
            'rcpt'  => $recipient,
            'data'     => $message->content,
        ];

        if($message->from)
            $params['sender'] = $message->from;

        $this->smsc->send($params);
    }
}
