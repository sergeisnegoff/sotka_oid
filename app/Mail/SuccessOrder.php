<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SuccessOrder extends Mailable
{
    use Queueable, SerializesModels;
    public $order;
    public $products;
    public $address;
    public $user;

    /**
     * Create a new message instance.
     *
     * @param Order $order
     * @param $products
     */
    public function __construct(Order $order, $products, $address, $user)
    {
        $this->theme =  'Новый заказ с сайта'.env('APP_NAME');
        $this->order = $order;
        $this->products = $products;
        $this->address = $address;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.order');
    }
}
