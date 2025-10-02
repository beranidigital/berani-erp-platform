<?php

namespace Webkul\Support\Services;

use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Webkul\Support\Models\EmailLog;

class EmailService
{
    public function send(string $view, string $mailClass, array $payload, array $attachments = [])
    {
        try {
            // Handle cases where there might not be an authenticated user
            $currentUser = Auth::user();
            if ($currentUser) {
                $payload['from'] = [
                    'address' => $currentUser->email,
                    'name'    => $currentUser->name,
                ];

                if ($currentUser->defaultCompany) {
                    $payload['from']['company'] = $currentUser->defaultCompany->toArray();
                }
            } else {
                // Use default from configuration
                $payload['from'] = [
                    'address' => config('mail.from.address'),
                    'name'    => config('mail.from.name'),
                ];
                
                $companyInfo = config('app.name');
                if ($companyInfo) {
                    $payload['from']['company'] = [
                        'name' => $companyInfo,
                    ];
                }
            }

            Mail::to($payload['to']['address'], '"'.addslashes($payload['to']['name']).'"')
                ->send((new $mailClass($view, $payload))->withAttachments($attachments));

            $this->logEmail($payload['to']['address'], $payload['to']['name'], $payload['subject'], 'sent');

            return true;
        } catch (Exception $e) {
            $this->logEmail($payload['to']['address'], $payload['to']['name'], $payload['subject'], 'failed', $e->getMessage());

            // Log the error for debugging but don't throw it to allow the transaction to complete
            \Log::error('Email sending failed: ' . $e->getMessage(), [
                'payload' => $payload,
                'trace' => $e->getTraceAsString()
            ]);

            // Return false instead of throwing exception to allow application to be saved
            return false;
        }
    }

    protected function logEmail(string $recipientEmail, string $recipientName, string $subject, string $status, ?string $errorMessage = null)
    {
        EmailLog::create([
            'recipient_email' => $recipientEmail,
            'recipient_name'  => $recipientName,
            'subject'         => $subject,
            'status'          => $status,
            'error_message'   => $errorMessage,
            'sent_at'         => now(),
        ]);
    }
}
