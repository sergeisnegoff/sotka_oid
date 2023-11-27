<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WrongImportProducts extends Mailable
{
    use Queueable, SerializesModels;

    public $products;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $wrongProducts, string $subject)
    {
        $this->products = $wrongProducts;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)->markdown('email.wrongImportProducts', ['products' => $this->products]);
    }
}
