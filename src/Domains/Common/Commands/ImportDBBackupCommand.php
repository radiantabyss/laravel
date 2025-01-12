<?php
namespace App\Domains\Common\Commands;

use Illuminate\Console\Command;

class ImportDBBackupCommand extends Command
{
    protected $signature = 'ra:import-db-backup';
    protected $description = 'Imports the latest db backup zip archive from /storage/db-backups (not from subfolders)';

    public function handle() {
        start_load_time();

        if ( config('app.env') == 'production' ) {
            return;
        }

        $files = scandir(storage_path().'/db-backups');
        rsort($files);

        $zip_file = null;
        foreach ( $files as $file ) {
            if ( preg_match('/.zip$/', $file) ) {
                $zip_file = $file;
                break;
            }
        }


        if ( !$zip_file ) {
            echo "Error. No zip archive found.\n";
            return;
        }

        echo "Zip file found: ".$zip_file."\nUnzipping\n";
        exec('unzip -o '.storage_path().'/db-backups/'.$zip_file.' -d '.storage_path().'/db-backups');
        echo "Unzip complete\n";

        $sql_file = pathinfo($zip_file)['filename'];

        echo "Dropping database ".env('DB_DATABASE')." \n";
        \DB::statement('DROP DATABASE `'.env('DB_DATABASE').'`');

        echo "Creating database ".env('DB_DATABASE')." \n";
        \DB::statement('CREATE DATABASE `'.env('DB_DATABASE').'`');
        \DB::statement('USE `'.env('DB_DATABASE').'`');

        echo "Importing\n";
        exec('mysql -u root --password="" '.env('DB_DATABASE').' < "'.storage_path().'\\db-backups\\'.$sql_file.'"');

        echo "Deleting files\n";
        unlink(storage_path().'/db-backups/'.$zip_file);
        unlink(storage_path().'/db-backups/'.$sql_file);

        echo "Success!\n";
        get_load_time();
    }
}
