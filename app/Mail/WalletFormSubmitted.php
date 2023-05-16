<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;

class WalletFormSubmitted extends Mailable implements ShouldQueue
{
    use Queueable;

    public $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function build()
    {
        return $this
            ->to(config('mail.wallet_submitted.address', config('mail.stub.address')))
            ->subject(trans('mails.subjects.wallet_submitted'))
            ->markdown('mails.wallet-submitted');
    }
}
