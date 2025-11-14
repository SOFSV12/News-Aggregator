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


    public static function extractAuthorsNeswApiAiService($authors): ?string
    {
        // Case 1: completely empty
        if (empty($authors)) {
            return null;
        }

        // Case 2: single object instead of array
        if (is_array($authors) && isset($authors['name'])) {
            return $authors['name'];
        }

        // Case 3: array of objects
        if (is_array($authors) && isset($authors[0])) {
            // Filter only items that have a name key
            $names = array_filter(array_map(
                fn ($a) => is_array($a) && isset($a['name']) ? $a['name'] : null,
                $authors
            ));

            return !empty($names) ? implode(', ', $names) : null;
        }

        // Case 4: Unexpected string
        if (is_string($authors)) {
            return $authors;
        }

        return null;
    }

    
}