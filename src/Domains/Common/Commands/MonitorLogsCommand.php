<?php
namespace RA\Domains\Common\Commands;

use Illuminate\Console\Command;

//every 5 minutes
class MonitorLogsCommand extends Command
{
    protected $signature = 'ra:monitor-logs';
    protected $description = 'Checks if has been a change in the logs.';

    public function handle()
    {
        if ( !env('MONITOR_LOGS') ) {
            return;
        }

        if ( !file_exists(storage_path().'/last_log') ) {
            file_put_contents(storage_path().'/last_log', '');
        }

        //check if there are any logs
		$logs = scandir(storage_path().'/logs');
        if ( count($logs) < 4 ) {
            return;
        }

        //check if there are any changes
        $last_changed_at = filemtime(storage_path().'/logs/'.$logs[count($logs)- 1]);
        if ( $last_changed_at == file_get_contents(storage_path().'/last_log') ) {
            //check if last log is today
            if ( $last_changed_at > strtotime('-1 day') ) {
                return;
            }

            //for older logs notify daily at 10 am
            if ( date('H:i') != '10:00' ) {
                return;
            }

            if ( date('N') > 5 ) {
                return;
            }
        }

        try {
            Http::post(env('MONITOR_LOGS_RELAYJAY_CHANNEL'), [
                'message' => 'Error - Check Logs',
            ]);
        }
        catch(\Exception $e) {}

        file_put_contents(storage_path().'/last_log', $last_changed_at);
    }
}
