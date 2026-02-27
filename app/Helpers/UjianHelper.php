<?php

namespace App\Helpers;

class UjianHelper
{
    /**
     * Check if a submission (file or notes) is valid/present.
     *
     * @param string|null $ljkFile  The database field for the uploaded file path.
     * @param string|null $notes    The database field for the rich text notes.
     * @return bool
     */
    public static function hasSubmission(string|array|null $ljkFile, ?string $notes): bool
    {
        // 1. Check if file exists (simple string check)
        if (!empty($ljkFile)) {
            return true; // File uploaded -> Automatically valid
        }

        // 2. Check notes content
        // If notes is null or empty string, it's invalid
        if (empty($notes)) {
            return false;
        }

        // 3. Check for image tags (img src)
        // If the student uploaded an image directly into the editor, it counts as a submission
        if (str_contains($notes, '<img')) {
            return true;
        }

        // 4. Check for iframe (e.g. embedded video/pdf)
        if (str_contains($notes, '<iframe')) {
            return true;
        }

        // 5. Clean text content to check for actual words
        // Strip all tags to get raw text
        $cleanText = strip_tags($notes);

        // Remove known empty entities like non-breaking spaces
        $cleanText = str_replace('&nbsp;', '', $cleanText);

        // Trim whitespace (spaces, tabs, newlines)
        $cleanText = trim($cleanText);

        // If there's anything left (even one letter), it's a valid submission
        return !empty($cleanText);
    }
}
