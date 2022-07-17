<?php
namespace RA\Core;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailSenderJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $to;
    protected $mail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($to, $mail)
    {
        $this->to = $to;
        $this->mail = $mail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Mail::to($this->to)->send($this->mail);
    }
}
