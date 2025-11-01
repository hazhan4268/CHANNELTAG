<?php

namespace TelegramBot;

class Logger
{
    private $logDir;
    
    public function __construct()
    {
        $this->logDir = __DIR__ . '/../logs';
        if (!is_dir($this->logDir)) {
            @mkdir($this->logDir, 0755, true);
        }
    }
    
    /**
     * Write log message
     */
    public function log($level, $message, array $context = [])
    {
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context) : '';
        $logMessage = "[{$timestamp}] [{$level}] {$message}{$contextStr}\n";
        
        // Write to daily log file
        $logFile = $this->logDir . '/app-' . date('Y-m-d') . '.log';
        @file_put_contents($logFile, $logMessage, FILE_APPEND);
        
        // Also output for CLI
        if (php_sapi_name() === 'cli') {
            echo $logMessage;
        }
    }
    
    public function info($message, array $context = [])
    {
        $this->log('INFO', $message, $context);
    }
    
    public function warning($message, array $context = [])
    {
        $this->log('WARNING', $message, $context);
    }
    
    public function error($message, array $context = [])
    {
        $this->log('ERROR', $message, $context);
    }
    
    public function debug($message, array $context = [])
    {
        $this->log('DEBUG', $message, $context);
    }
}

