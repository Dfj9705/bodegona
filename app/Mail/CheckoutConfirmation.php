<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CheckoutConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The checkout confirmation data.
     */
    public array $confirmation;

    /**
     * Create a new message instance.
     */
    public function __construct(array $confirmation)
    {
        $this->confirmation = $confirmation;
    }

    /**
     * Build the message.
     */
    public function build(): self
    {
        return $this
            ->subject(config('checkout.confirmation_subject'))
            ->view('emails.checkout-confirmation')
            ->with([
                'confirmation' => $this->confirmation,
            ]);
    }
}
