<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Pin extends Mailable
{
    use Queueable, SerializesModels;

    public  $pin;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($pin)
    {
        $this->pin = $pin;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from('upwork@live.com','upwork')
            ->replyTo('upwork@live.com', 'upwork')
            ->subject('Thank you for contacting upwork.')
            ->markdown('emails.pinmail')
            ->with($this->pin);
    }
}
