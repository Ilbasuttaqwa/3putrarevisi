<?php
/**
 * ULTIMATE CACHE CLEARER
 * Clear semua cache yang mungkin ada
 */

echo "🧹 CLEARING ALL CACHES...\n\n";

// 1. Clear OPcache (PHP bytecode cache)
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "✅ OPcache cleared (PHP bytecode cache)\n";
} else {
    echo "⚠️  OPcache not available\n";
}

// 2. Clear APCu cache (if available)
if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    echo "✅ APCu cache cleared\n";
}

// 3. Clear Laravel view cache
$viewPath = __DIR__ . '/storage/framework/views';
if (is_dir($viewPath)) {
    $files = glob($viewPath . '/*.php');
    $count = 0;
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $count++;
        }
    }
    echo "✅ Laravel view cache cleared ($count files)\n";
}

// 4. Clear Laravel cache
$cachePath = __DIR__ . '/storage/framework/cache/data';
if (is_dir($cachePath)) {
    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($cachePath, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );
    $count = 0;
    foreach ($files as $fileinfo) {
        if ($fileinfo->isFile() && $fileinfo->getFilename() !== '.gitignore') {
            unlink($fileinfo->getRealPath());
            $count++;
        }
    }
    echo "✅ Laravel cache cleared ($count files)\n";
}

// 5. Clear config cache
$configCache = __DIR__ . '/bootstrap/cache/config.php';
if (file_exists($configCache)) {
    unlink($configCache);
    echo "✅ Config cache cleared\n";
}

// 6. Clear routes cache
$routesCache = __DIR__ . '/bootstrap/cache/routes-v7.php';
if (file_exists($routesCache)) {
    unlink($routesCache);
    echo "✅ Routes cache cleared\n";
}

// 7. Clear events cache
$eventsCache = __DIR__ . '/bootstrap/cache/events.php';
if (file_exists($eventsCache)) {
    unlink($eventsCache);
    echo "✅ Events cache cleared\n";
}

echo "\n✅ ALL CACHES CLEARED!\n";
echo "⚠️  NEXT STEP: RESTART PHP SERVER (php artisan serve)\n";
echo "⚠️  THEN: Hard refresh browser (Ctrl + Shift + R)\n";
