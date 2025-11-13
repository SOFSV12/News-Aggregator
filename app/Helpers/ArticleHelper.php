<?php 

namespace App\Helpers;

class ArticleHelper
{
    public static function extractAuthorNyTimes(string $byline): string
    {
        if (str_starts_with($byline, 'By ')) {
            return trim(substr($byline, 3));
        }
        
        return $byline ?: 'Unknown';
    }

    public static function extractImageUrlNyTimes(array $multimedia): ?string
    {
        // Prefer the mediumThreeByTwo210 format, fall back to thumbnail
        foreach ($multimedia as $media) {
            if ($media['format'] === 'mediumThreeByTwo210') {
                return $media['url'] ?? null;
            }
        }
        
        // Fallback to any image URL
        foreach ($multimedia as $media) {
            if ($media['type'] === 'image' && !empty($media['url'])) {
                return $media['url'];
            }
        }
        
        return null;
    }

    
}