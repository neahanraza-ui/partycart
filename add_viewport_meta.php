<?php
/**
 * Script to add viewport meta tags to PHP files that are missing them
 * Run this once to ensure all pages have proper responsive meta tags
 */

$directory = __DIR__;
$files = glob($directory . '/*.php');

$viewport_meta = '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
$added_count = 0;
$skipped_count = 0;

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Skip if already has viewport meta tag
    if (stripos($content, 'viewport') !== false) {
        echo "✓ Skipped: " . basename($file) . " (already has viewport meta)\n";
        $skipped_count++;
        continue;
    }
    
    // Skip if no <head> tag
    if (stripos($content, '<head>') === false) {
        echo "⊘ Skipped: " . basename($file) . " (no <head> tag found)\n";
        $skipped_count++;
        continue;
    }
    
    // Add viewport meta tag after <meta charset>
    if (preg_match('/<meta\s+charset=["\']UTF-8["\']\s*>/i', $content)) {
        $content = preg_replace(
            '/(<meta\s+charset=["\']UTF-8["\']\s*>)/i',
            "$1\n  " . $viewport_meta,
            $content,
            1
        );
        file_put_contents($file, $content);
        echo "✓ Added to: " . basename($file) . "\n";
        $added_count++;
    } else {
        echo "⊘ Skipped: " . basename($file) . " (no charset meta found)\n";
        $skipped_count++;
    }
}

echo "\n=== Summary ===\n";
echo "Added viewport meta to: $added_count files\n";
echo "Skipped: $skipped_count files\n";
echo "\nDone! Please refresh your browser to see responsive design working.\n";
?>
