#!/bin/bash
# Production Deployment Script - Clear All Caches
# Run this on production server after git pull

echo "🚀 Starting Production Deployment..."
echo ""

# 1. Clear Laravel Caches
echo "📦 Clearing Laravel caches..."
php artisan cache:clear 2>/dev/null || echo "⚠️  Cache clear failed (maybe not available)"
php artisan view:clear 2>/dev/null || echo "⚠️  View clear failed"
php artisan config:clear 2>/dev/null || echo "⚠️  Config clear failed"
php artisan route:clear 2>/dev/null || echo "⚠️  Route clear failed"
php artisan optimize:clear 2>/dev/null || echo "⚠️  Optimize clear failed"

# 2. Clear OPcache (CRITICAL!)
echo ""
echo "🔥 Clearing OPcache (PHP bytecode cache)..."
php -r "if(function_exists('opcache_reset')){opcache_reset();echo 'OPcache cleared!\n';}else{echo 'OPcache not available\n';}"

# 3. Manual cache clear (as backup)
echo ""
echo "🧹 Manual cache clearing..."
rm -rf storage/framework/views/*.php 2>/dev/null
rm -rf storage/framework/cache/data/* 2>/dev/null
rm -f bootstrap/cache/config.php 2>/dev/null
rm -f bootstrap/cache/routes*.php 2>/dev/null

# 4. Set proper permissions
echo ""
echo "🔐 Setting permissions..."
chmod -R 775 storage bootstrap/cache 2>/dev/null || echo "⚠️  Permission setting failed (maybe no permission)"

# 5. Verify files exist
echo ""
echo "✅ Verifying critical files..."
if [ -f "resources/views/absensis/create.blade.php" ]; then
    echo "✅ create.blade.php exists"
    FIRST_LINE=$(head -1 resources/views/absensis/create.blade.php)
    if [[ "$FIRST_LINE" == *"layouts.tailwind"* ]]; then
        echo "✅ File uses layouts.tailwind (CORRECT!)"
    else
        echo "❌ File still uses old layout (WRONG!)"
    fi
else
    echo "❌ create.blade.php NOT FOUND!"
fi

if [ -f "resources/views/layouts/tailwind.blade.php" ]; then
    echo "✅ tailwind.blade.php exists"
else
    echo "❌ tailwind.blade.php NOT FOUND!"
fi

echo ""
echo "🎉 Deployment completed!"
echo ""
echo "⚠️  NEXT STEPS:"
echo "1. Restart PHP-FPM: sudo systemctl restart php-fpm (atau php8.x-fpm)"
echo "2. Restart Web Server: sudo systemctl restart nginx (atau apache2)"
echo "3. Hard refresh browser: Ctrl + Shift + R"
echo ""
