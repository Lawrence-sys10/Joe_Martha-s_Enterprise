<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SMSService
{
    protected $apiKey;
    protected $senderId;
    protected $apiUrl;
    protected $username;

    public function __construct()
    {
        $this->apiKey = env('SMS_API_KEY');
        $this->senderId = env('SMS_SENDER_ID', 'JM-EMS');
        $this->apiUrl = env('SMS_API_URL', 'https://api.africastalking.com/version1/messaging');
        $this->username = env('SMS_USERNAME', 'sandbox');
    }

    public function sendSMS($phoneNumber, $message)
    {
        // Format phone number for Ghana
        $phoneNumber = $this->formatPhoneNumber($phoneNumber);
        
        // Check if API key is configured
        if (empty($this->apiKey) || $this->apiKey === 'your_api_key_here' || $this->apiKey === 'atsk_your_api_key_here') {
            $this->logSMS($phoneNumber, $message, 'API NOT CONFIGURED - Set SMS_API_KEY in .env');
            echo "  ⚠️ SMS API not configured. Message logged to storage/logs/sms.log\n";
            echo "  📝 To configure, add to .env: SMS_API_KEY=your_africastalking_api_key\n";
            return true; // Return true since we logged it
        }
        
        try {
            // Africa's Talking API requires the username and apiKey in headers
            $response = Http::withHeaders([
                'apiKey' => $this->apiKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post($this->apiUrl, [
                'username' => $this->username,
                'to' => $phoneNumber,
                'message' => $message,
                'from' => $this->senderId,
            ]);
            
            $responseData = $response->json();
            
            if ($response->successful() && isset($responseData['SMSMessageData']['Recipients'])) {
                $recipients = $responseData['SMSMessageData']['Recipients'];
                $sent = false;
                
                foreach ($recipients as $recipient) {
                    if ($recipient['status'] === 'Success') {
                        $sent = true;
                        break;
                    }
                }
                
                if ($sent) {
                    $this->logSMS($phoneNumber, $message, 'SENT SUCCESSFULLY', $responseData);
                    echo "  ✓ SMS sent successfully to {$phoneNumber}\n";
                    return true;
                } else {
                    $error = $recipients[0]['status'] ?? 'Unknown error';
                    $this->logSMS($phoneNumber, $message, 'FAILED', $error);
                    echo "  ✗ SMS failed: {$error}\n";
                    return false;
                }
            } else {
                $error = $response->body();
                $this->logSMS($phoneNumber, $message, 'FAILED', $error);
                echo "  ✗ SMS failed: " . substr($error, 0, 100) . "\n";
                return false;
            }
        } catch (\Exception $e) {
            $this->logSMS($phoneNumber, $message, 'ERROR', $e->getMessage());
            echo "  ✗ SMS error: " . $e->getMessage() . "\n";
            return false;
        }
    }

    private function logSMS($phoneNumber, $message, $status, $details = null)
    {
        $smsLog = storage_path('logs/sms.log');
        $logEntry = "[" . date('Y-m-d H:i:s') . "]\n";
        $logEntry .= "To: {$phoneNumber}\n";
        $logEntry .= "Message: {$message}\n";
        $logEntry .= "Status: {$status}\n";
        if ($details) {
            if (is_array($details)) {
                $logEntry .= "Details: " . json_encode($details) . "\n";
            } else {
                $logEntry .= "Details: {$details}\n";
            }
        }
        $logEntry .= "----------------------------------------\n";
        file_put_contents($smsLog, $logEntry, FILE_APPEND);
    }

    public function sendLowStockAlert($product, $admin)
    {
        $message = "JM-EMS Alert: Low stock on {$product->name}. Current stock: {$product->stock_quantity} {$product->unit}s. Minimum required: {$product->minimum_stock}. Please restock soon!";
        
        // Truncate message if too long (max 160 chars for SMS)
        if (strlen($message) > 160) {
            $message = substr($message, 0, 157) . '...';
        }
        
        echo "  📱 Sending SMS to: {$admin->phone}\n";
        return $this->sendSMS($admin->phone, $message);
    }

    protected function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // Format for Ghana numbers (e.g., 233XXXXXXXXX)
        if (strlen($phone) == 10 && substr($phone, 0, 1) == '0') {
            $phone = '233' . substr($phone, 1);
        } elseif (strlen($phone) == 9) {
            $phone = '233' . $phone;
        } elseif (strlen($phone) == 12 && substr($phone, 0, 3) == '233') {
            // Already in correct format
            $phone = $phone;
        }
        
        return $phone;
    }
    
    public function testConnection()
    {
        // Test the connection with a simple message to yourself
        $testMessage = "JM-EMS Test: Your SMS configuration is working!";
        return $this->sendSMS('233593001501', $testMessage);
    }
}