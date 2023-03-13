<?php
namespace RA\Core;

use RA\Core\MailSenderJob;

class MailSender
{
    public static function run($Mail, $to, $params) {
        if ( !$to ) {
            return;
        }

        \Queue::push(new MailSenderJob([
            'to' => $to,
            'Mail' => new $Mail($params),
        ]));
    }

    public static function send($Mail, $to, $params) {
        self::run($Mail, $to, $params);
    }
}
