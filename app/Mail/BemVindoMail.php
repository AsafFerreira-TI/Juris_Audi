<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BemVindoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $nome;

    public function __construct($nome)
    {
        $this->nome = $nome;
    }

    public function build()
    {
        return $this->subject('Bem-vindo ao Juris Controller!')
                    ->view('emails.bemvindo');
    }
}
