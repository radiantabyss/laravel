<?php
namespace RA\Core;

class MailSenderJob extends Job
{
    public function handle() {
        \Mail::to($this->params['to'])->send($this->params['Mail']);
    }
}
