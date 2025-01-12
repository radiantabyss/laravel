<?php
namespace App\Domains\Common\Commands;

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
        }

        $url = env('MONITOR_LOGS_SLACK_CHANNEL');
        $data = [
            'text' => 'Error - Check Logs '.date('d M @ H:i'),
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($data))
        ]);

        $result = curl_exec($ch);
        curl_close($ch);

        file_put_contents(storage_path().'/last_log', $last_changed_at);
    }
}
