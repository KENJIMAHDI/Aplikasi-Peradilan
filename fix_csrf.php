<?php
$dir = __DIR__ . '/resources/views';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $original = $content;

        // Fix the broken variable injection first
        $content = str_replace("\n                    @csrfid)", "id)", $content);
        $content = str_replace("@csrfid)", "id)", $content);

        // Remove ALL @csrf from the file entirely to start fresh
        $content = str_replace('@csrf', '', $content);

        // Re-inject @csrf right after EVERY <form ...>
        // Use a safer regex
        $content = preg_replace('/(<form[^>]*>)/i', "$1\n@csrf\n", $content);

        // But wait! This might inject @csrf multiple times if I run it. I just cleaned them, so it should be exactly 1 per form.
        // Some forms might be on a single line, some multi line. `<form ... >`
        
        if ($original !== $content) {
            file_put_contents($file->getPathname(), $content);
            echo "Fixed CSRF: " . $file->getFilename() . "\n";
        }
    }
}
echo "Done fixing CSRF.\n";
