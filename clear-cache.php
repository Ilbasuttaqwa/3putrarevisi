<?php
/**
 * Manual Cache Clearer
 * Jalankan: php clear-cache.php
 */

echo "🧹 Clearing Laravel Cache...\n\n";

// Clear view cache
$viewPath = __DIR__ . '/storage/framework/views';
if (is_dir($viewPath)) {
    $files = glob($viewPath . '/*.php');
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }
    echo "✅ View cache cleared (" . count($files) . " files)\n";
} else {
    echo "⚠️  View cache directory not found\n";
}

// Clear cache data
$cachePath = __DIR__ . '/storage/framework/cache/data';
if (is_dir($cachePath)) {
    $count = 0;
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cachePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            unlink($file->getPathname());
            $count++;
        }
    }
    echo "✅ Cache data cleared (" . $count . " files)\n";
} else {
    echo "⚠️  Cache directory not found\n";
}

// Clear config cache
$configCache = __DIR__ . '/bootstrap/cache/config.php';
if (file_exists($configCache)) {
    unlink($configCache);
    echo "✅ Config cache cleared\n";
} else {
    echo "ℹ️  No config cache to clear\n";
}

// Clear route cache
$routeCache = __DIR__ . '/bootstrap/cache/routes-v7.php';
if (file_exists($routeCache)) {
    unlink($routeCache);
    echo "✅ Route cache cleared\n";
} else {
    echo "ℹ️  No route cache to clear\n";
}

echo "\n✨ Cache clearing complete!\n";
echo "📝 Next steps:\n";
echo "   1. Restart your Laravel server (Ctrl+C, then php artisan serve)\n";
echo "   2. Hard refresh browser (Ctrl+Shift+R or Cmd+Shift+R)\n";
echo "   3. Access: /manager/absensis/create\n";
