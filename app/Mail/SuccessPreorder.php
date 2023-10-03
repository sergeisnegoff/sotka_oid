<?php

namespace App\Mail;

use App\Models\PreorderCheckout;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuccessPreorder extends Mailable
{
    use Queueable, SerializesModels;

    public $checkout;
    public $user;
    public $showPreorderLink;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PreorderCheckout $checkout, $showPreorderLink = false)
    {
        $this->checkout = $checkout;
        $this->user = $checkout->user;
        $this->showPreorderLink = $showPreorderLink;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.preorder');
    }
}
