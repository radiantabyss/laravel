<?php
namespace Lumi\Core;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $params;

    public function __construct($params) {
        $this->params = $params;
    }

    public function handle() {
        if ( config('lumi-core.enable_job_process_restart') && !isset($GLOBALS['__jobs_count']) ) {
            $GLOBALS['__jobs_count'] = 0;
        }

        $this->run();

        //restart process after 30 jobs
        if ( config('lumi-core.enable_job_process_restart') ) {
            $GLOBALS['__jobs_count']++;
            $max_jobs_count = config('lumi-core.max_jobs_per_process:'.$this->getQueue()) ?? config('lumi-core.max_jobs_per_process');

            if ( $GLOBALS['__jobs_count'] == $max_jobs_count ) {
                \DB::table('jobs')->where('id', $this->getJobId())->delete();
                die();
            }
        }
    }
}
