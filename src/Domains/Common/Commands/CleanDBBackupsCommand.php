<?php
namespace RA\Domains\Common\Commands;

use Illuminate\Console\Command;

class CleanDBBackupsCommand extends Command
{
    protected $signature = 'ra:clean-db-backups';
    protected $description = 'Deletes old backups';

    public function handle() {
        date_default_timezone_set('Europe/Bucharest');

        //backup lifetimes for each frequency
        $frequencies = [
            'every_10_minutes' => 6 * 3600,
            'hourly' => 7 * 24 * 3600,
            'daily' => 90 * 24 * 3600,
        ];

        foreach ( $frequencies as $frequency => $ttl ) {
            $folder_path = storage_path().'/db-backups/'.$frequency;
            if ( !file_exists($folder_path) ) {
                continue;
            }

            $files = scandir($folder_path);
            foreach ( $files as $file ) {
                if ( in_array($file, ['.', '..', '.gitignore']) ) {
                    continue;
                }

                if ( filectime($folder_path.'/'.$file) < strtotime('now') - $ttl ) {
                    unlink($folder_path.'/'.$file);
                }
            }
        }
    }
}
