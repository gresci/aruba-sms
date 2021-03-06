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
        if(is_array($recipient)) {
            $recipient = implode(',', array_map(function ($item){return '+39'.$item;},$recipient));
        } else {
            $recipient = '+39'.$recipient;
        }
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
