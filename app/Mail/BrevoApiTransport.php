<?php

namespace App\Mail;

use Illuminate\Support\Facades\Http;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;
use Illuminate\Support\Facades\Log;

class BrevoApiTransport extends AbstractTransport
{
    public function __construct(protected string $apiKey)
    {
        parent::__construct();
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());
        
        $from = $email->getFrom()[0];
        $to = array_map(fn($addr) => [
            'email' => $addr->getAddress(),
            'name' => $addr->getName() ?: $addr->getAddress()
        ], $email->getTo());
        
        $payload = [
            'sender' => [
                'email' => $from->getAddress(),
                'name' => $from->getName() ?: $from->getAddress()
            ],
            'to' => $to,
            'subject' => $email->getSubject(),
        ];
        
        // Add HTML body if available
        if ($email->getHtmlBody()) {
            $payload['htmlContent'] = $email->getHtmlBody();
        }
        
        // Add text body if available
        if ($email->getTextBody()) {
            $payload['textContent'] = $email->getTextBody();
        }
        
        try {
            $response = Http::withHeaders([
                'api-key' => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://api.brevo.com/v3/smtp/email', $payload);
            
            if (!$response->successful()) {
                throw new \Exception('Brevo API Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('Brevo API Error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function __toString(): string
    {
        return 'brevo';
    }
}