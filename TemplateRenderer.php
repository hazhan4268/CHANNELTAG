<?php

namespace TelegramBot\Services;

use TelegramBot\Repositories\TagsRepo;
use TelegramBot\Repositories\SettingsRepo;

class TemplateRenderer
{
    private $tagsRepo;
    private $settingsRepo;
    private $locale;
    
    public function __construct(TagsRepo $tagsRepo, SettingsRepo $settingsRepo)
    {
        $this->tagsRepo = $tagsRepo;
        $this->settingsRepo = $settingsRepo;
        $this->locale = $this->settingsRepo->get('locale', 'fa');
    }
    
    /**
     * Render template with context variables
     */
    public function render($template, array $context = [])
    {
        // Get tags and IDs
        $tags = $this->tagsRepo->getEnabledByType('tag');
        $ids = $this->tagsRepo->getEnabledByType('id');
        
        // Get separators
        $tagSeparator = $this->settingsRepo->get('tag_separator', ' · ');
        $idSeparator = $this->settingsRepo->get('id_separator', ' | ');
        
        // Build replacements
        $replacements = [
            '{tags}' => implode($tagSeparator, array_column($tags, 'value')),
            '{ids}' => implode($idSeparator, array_column($ids, 'value')),
            '{channel_username}' => $context['channel_username'] ?? '',
            '{channel_id}' => $context['channel_id'] ?? '',
            '{post_type}' => $context['post_type'] ?? 'text',
            '{message_id}' => $context['message_id'] ?? '',
            '{date}' => $context['date'] ?? date('Y-m-d H:i:s'),
            '{permalink}' => $context['permalink'] ?? '',
            '{custom1}' => $context['custom1'] ?? '',
            '{custom2}' => $context['custom2'] ?? '',
            '{custom3}' => $context['custom3'] ?? '',
        ];
        
        // Render template
        $output = str_replace(array_keys($replacements), array_values($replacements), $template);
        
        // Clean up empty lines
        $output = preg_replace('/\n{3,}/', "\n\n", $output);
        
        return trim($output);
    }
    
    /**
     * Escape for MarkdownV2
     * Only escape actual text, not placeholders that are already replaced
     */
    public function escapeMarkdownV2($text)
    {
        // Don't escape if it's already processed
        // Only escape special characters that aren't part of variables
        $chars = ['_', '*', '[', ']', '(', ')', '~', '`', '>', '#', '+', '-', '=', '|', '{', '}', '.', '!'];
        $result = '';
        $len = mb_strlen($text, 'UTF-8');
        
        for ($i = 0; $i < $len; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            if (in_array($char, $chars)) {
                $result .= '\\' . $char;
            } else {
                $result .= $char;
            }
        }
        
        return $result;
    }
    
    /**
     * Prepare text for parse mode
     */
    public function prepareForParseMode($text, $parseMode = 'MarkdownV2')
    {
        if ($parseMode === 'MarkdownV2') {
            // Don't escape variables that should remain as placeholders
            // Only escape when actually sending (after variable replacement)
            return $text;
        }
        
        return $text;
    }
}

