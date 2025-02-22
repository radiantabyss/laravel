<?php
namespace RA;

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
        if ( config('ra.enable_job_process_restart') && !isset($GLOBALS['__jobs_count']) ) {
            $GLOBALS['__jobs_start_time'] = time();
            $GLOBALS['__jobs_count'] = 0;
        }

        $this->run();

        //restart process after 30 jobs
        if ( config('ra.enable_job_process_restart') ) {
            $GLOBALS['__jobs_count']++;
            $max_jobs_count = config('ra.max_jobs_per_process:'.$this->job->getQueue()) ?? config('ra.max_jobs_per_process');

            //check if max jobs count was reached and the process has been running for at least 30 seconds
            if ( $GLOBALS['__jobs_count'] >= $max_jobs_count && (time() - $GLOBALS['__jobs_start_time']) > 30 ) {
                $job_id = $this->job->getJobId();

                if ( $job_id ) {
                    \DB::table('jobs')->where('id', $this->job->getJobId())->delete();
                }

                die();
            }
        }
    }
}
