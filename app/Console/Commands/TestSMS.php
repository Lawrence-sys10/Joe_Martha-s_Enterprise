<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\SMSService;

class TestSMS extends Command
{
    protected $signature = 'sms:test {phone?} {message?}';
    protected $description = 'Test SMS configuration';

    public function handle(SMSService $smsService)
    {
        $this->info('=================================');
        $this->info('Testing SMS Configuration');
        $this->info('=================================');
        $this->newLine();
        
        // Get phone number from argument or ask
        $phone = $this->argument('phone');
        if (!$phone) {
            $phone = $this->ask('Enter phone number to test (e.g., 233593001501)', '233593001501');
        }
        
        // Get message from argument or ask
        $message = $this->argument('message');
        if (!$message) {
            $message = $this->ask('Enter test message', 'JM-EMS Test: Your SMS configuration is working!');
        }
        
        $this->info("Sending test SMS to: {$phone}");
        $this->info("Message: {$message}");
        $this->newLine();
        
        $result = $smsService->sendSMS($phone, $message);
        
        if ($result) {
            $this->info('✓ Test SMS sent successfully!');
            $this->info('Check your phone for the message.');
        } else {
            $this->error('✗ Failed to send SMS. Check logs for details.');
            $this->info('View logs: php artisan sms:logs');
        }
        
        return 0;
    }
}