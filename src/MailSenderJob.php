<?php
namespace RA;

class MailSenderJob extends Job
{
    public function handle() {
        \Mail::to($this->params['to'])->send($this->params['Mail']);
    }
}
