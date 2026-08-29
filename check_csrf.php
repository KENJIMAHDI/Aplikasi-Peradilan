<?php
$dir = __DIR__ . '/resources/views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') { // blade.php
        $content = file_get_contents($file->getPathname());
        $original = $content;

        // Find all <form ...> that don't have @csrf immediately or soon after
        // This regex is tricky. Instead, we can just replace <form ...> with <form ...>\n@csrf
        // But we have to make sure we don't duplicate it.
        
        // Remove existing @csrf to avoid duplicates
        $content = str_replace('@csrf', '', $content);
        
        // Add @csrf right after <form> or <form ...>
        $content = preg_replace('/(<form[^>]*>)/i', "$1\n                    @csrf", $content);
        
        if ($original !== $content) {
            file_put_contents($file->getPathname(), $content);
            echo "Updated CSRF: " . $file->getFilename() . "\n";
        }
    }
}
echo "Done CSRF Check.\n";
