<?php
$dir = __DIR__ . '/app/Models';
$files = glob($dir . '/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    // Use regex to find protected $fillable = [...]; across multiple lines
    // and replace it with protected $guarded = [];
    $newContent = preg_replace('/protected\s+\$fillable\s*=\s*\[.*?\];/s', 'protected $guarded = [];', $content);
    // Fallback for single line array notation like ['a', 'b'];
    $newContent = preg_replace('/protected\s+\$fillable\s*=\s*array\(.*?\);/s', 'protected $guarded = [];', $newContent);
    
    if ($content !== $newContent) {
        file_put_contents($file, $newContent);
        echo "Updated: " . basename($file) . "\n";
    }
}
echo "Done.\n";
