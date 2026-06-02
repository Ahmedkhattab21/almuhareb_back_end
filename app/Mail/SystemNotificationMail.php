<?php

namespace App\Mail;

use App\Models\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SystemNotificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public Notifications $notification;

    public function __construct(Notifications $notification)
    {
        $this->notification = $notification;
    }

    public function build(): self
    {
        return $this
            ->subject($this->notification->title)
            ->view('emails.system-notification')
            ->with([
                'notification' => $this->notification,
            ]);
    }
}
