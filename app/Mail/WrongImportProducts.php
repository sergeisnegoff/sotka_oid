<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WrongImportProducts extends Mailable
{
    use Queueable, SerializesModels;

    public $wrongProducts;
    public $newProducts;
    public $subject;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $wrongProducts, array $newProducts, string $subject)
    {
        $this->wrongProducts = $wrongProducts;
        $this->subject = $subject;
        $this->newProducts = $newProducts;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->subject == "Загрузка заказов") {
            return $this->subject($this->subject)->markdown('email.wrongImportProducts', [
                'wrongProducts' => $this->wrongProducts,
                'newProducts' => $this->newProducts
            ]);
        }
        if ($this->subject == "Обновление предзаказа") {
            return $this->subject($this->subject)->markdown('email.updatePreorderReport', [
                'noBarcodeRows' => $this->wrongProducts,
                'noProductsRows' => $this->newProducts
            ]);
        }

    }
}
