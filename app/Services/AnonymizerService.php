<?php

namespace App\Services;

class AnonymizerService
{
    /**
     * Meredaksi teks putusan untuk publikasi (menyembunyikan nama, NIK, dll).
     *
     * @param string $text
     * @param array $sensitiveWords Array of words/names to anonymize
     * @return string
     */
    public function anonymizeText(string $text, array $sensitiveWords = []): string
    {
        // 1. Redaksi NIK (16 digit angka)
        $text = preg_replace('/\b\d{16}\b/', '****************', $text);
        
        // 2. Redaksi No HP (10-13 digit angka diawali 08 atau 62)
        $text = preg_replace('/(\b(08|62)\d{8,11}\b)/', '08**********', $text);

        // 3. Redaksi nama spesifik yang diberikan
        foreach ($sensitiveWords as $word) {
            if (empty(trim($word))) continue;
            
            // Ambil huruf pertama, sisanya diganti bintang
            $firstLetter = mb_substr($word, 0, 1);
            $stars = str_repeat('*', mb_strlen($word) - 1);
            
            // Case-insensitive replace with exact word boundary
            $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
            $text = preg_replace($pattern, $firstLetter . $stars, $text);
        }

        return $text;
    }
}
