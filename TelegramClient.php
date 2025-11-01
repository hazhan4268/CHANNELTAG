<?php

namespace TelegramBot\Services;

use TelegramBot\Logger;

class TelegramClient
{
    private $token;
    private $baseUrl;
    private $logger;
    private $maxRetries = 3;
    
    public function __construct($token, Logger $logger = null)
    {
        $this->token = $token;
        $this->baseUrl = "https://api.telegram.org/bot{$token}/";
        $this->logger = $logger ?? new Logger();
    }
    
    /**
     * Send request to Telegram API with retry logic
     */
    private function request($method, array $params = [], $retryCount = 0)
    {
        $url = $this->baseUrl . $method;
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
            ],
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            $this->logger->error("cURL error: {$error}");
            return false;
        }
        
        $data = json_decode($response, true);
        
        if (!$data || !isset($data['ok'])) {
            $this->logger->error("Invalid API response: {$response}");
            return false;
        }
        
        // Handle rate limiting (429) or server errors (5xx)
        if (!$data['ok'] || $httpCode === 429 || ($httpCode >= 500 && $httpCode < 600)) {
            if ($retryCount < $this->maxRetries) {
                // Exponential backoff with jitter
                $delay = pow(2, $retryCount) * (1 + (rand(0, 100) / 100));
                $this->logger->warning("Retrying request after {$delay}s (attempt " . ($retryCount + 1) . "/{$this->maxRetries})");
                usleep($delay * 1000000); // Convert to microseconds
                return $this->request($method, $params, $retryCount + 1);
            }
            
            $errorDesc = $data['description'] ?? "HTTP {$httpCode}";
            $this->logger->error("API error after retries: {$errorDesc}");
            return ['ok' => false, 'description' => $errorDesc];
        }
        
        return $data;
    }
    
    /**
     * Send message
     */
    public function sendMessage($chatId, $text, array $options = [])
    {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
        ];
        
        if (isset($options['parse_mode'])) {
            $params['parse_mode'] = $options['parse_mode'];
        }
        
        if (isset($options['disable_web_page_preview'])) {
            $params['disable_web_page_preview'] = $options['disable_web_page_preview'];
        }
        
        if (isset($options['reply_to_message_id'])) {
            $params['reply_to_message_id'] = $options['reply_to_message_id'];
        }
        
        return $this->request('sendMessage', $params);
    }
    
    /**
     * Set webhook
     */
    public function setWebhook($url, $secretToken, array $allowedUpdates = ['channel_post', 'edited_channel_post'])
    {
        $params = [
            'url' => $url,
            'secret_token' => $secretToken,
            'allowed_updates' => $allowedUpdates,
        ];
        
        return $this->request('setWebhook', $params);
    }
    
    /**
     * Delete webhook
     */
    public function deleteWebhook()
    {
        return $this->request('deleteWebhook');
    }
    
    /**
     * Get webhook info
     */
    public function getWebhookInfo()
    {
        return $this->request('getWebhookInfo');
    }
    
    /**
     * Get bot info
     */
    public function getMe()
    {
        return $this->request('getMe');
    }
}

