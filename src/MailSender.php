<?php
namespace RA\Core;

use RA\Core\MailSenderJob;

class MailSender
{
    public static function send($Mail, $to, $params) {
        if ( !$to ) {
            return;
        }

        \Queue::push(new MailSenderJob($to, new $Mail($params)));
    }
}
