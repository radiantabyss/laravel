<?php
namespace RA\Core;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Mail extends Mailable
{
    use Queueable, SerializesModels;

    private $params;

    public function __construct($params) {
        $this->params = $params;
    }
}
