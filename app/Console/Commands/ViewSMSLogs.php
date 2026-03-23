<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ViewSMSLogs extends Command
{
    protected $signature = 'sms:logs';
    protected $description = 'View SMS logs';

    public function handle()
    {
        $logFile = storage_path('logs/sms.log');
        
        if (!file_exists($logFile)) {
            $this->info('No SMS logs found yet.');
            return 0;
        }
        
        $logs = file_get_contents($logFile);
        
        if (empty($logs)) {
            $this->info('No SMS logs found.');
            return 0;
        }
        
        $this->info('=================================');
        $this->info('SMS Logs');
        $this->info('=================================');
        $this->newLine();
        
        echo $logs;
        
        return 0;
    }
}