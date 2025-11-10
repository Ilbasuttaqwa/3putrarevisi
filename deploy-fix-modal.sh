#!/bin/bash
# ===================================================================
# DEPLOYMENT SCRIPT - Fix Old Modal Display Issue
# ===================================================================
#
# MASALAH: Tampilan lama masih muncul di bawah index
# PENYEBAB: JavaScript files (absensi-bulk.js & app.js) inject modal lama
# SOLUSI: Disable JavaScript files + rebuild Vite assets
#
# ===================================================================

echo "🔧 DEPLOYMENT - Fix Old Modal Display"
echo "======================================"
echo ""

# Step 1: Pull latest code
echo "📥 Step 1: Pull latest code from branch..."
git pull origin claude/ai-intelligence-task-011CUxbnPftVGdhUP8CTfkhT

if [ $? -ne 0 ]; then
    echo "❌ Git pull failed! Please resolve conflicts manually."
    exit 1
fi
echo "✅ Code updated"
echo ""

# Step 2: Install/update npm dependencies (if needed)
echo "📦 Step 2: Check npm dependencies..."
if [ -f "package-lock.json" ]; then
    npm install
    echo "✅ Dependencies updated"
else
    echo "⚠️  No package-lock.json found, skipping npm install"
fi
echo ""

# Step 3: Rebuild Vite assets (CRITICAL!)
echo "🔨 Step 3: Rebuild Vite assets (CRITICAL STEP!)..."
echo "   This compiles app.js with disabled modal code"
npm run build

if [ $? -ne 0 ]; then
    echo "❌ Vite build failed!"
    echo "   Try manually: npm run build"
    exit 1
fi
echo "✅ Vite assets rebuilt"
echo ""

# Step 4: Clear all caches
echo "🧹 Step 4: Clear all Laravel caches..."
php artisan cache:clear
php artisan view:clear
php artisan config:clear
php artisan route:clear
echo "✅ Laravel caches cleared"
echo ""

# Step 5: Clear OPcache (if available)
echo "🗑️  Step 5: Clear OPcache..."
if command -v php >/dev/null 2>&1; then
    php -r "if(function_exists('opcache_reset')){opcache_reset(); echo '✅ OPcache cleared\n';} else {echo '⚠️  OPcache not available\n';}"
else
    echo "⚠️  PHP CLI not available"
fi
echo ""

# Step 6: Manual cache cleanup
echo "🗂️  Step 6: Manual cache file cleanup..."
rm -rf storage/framework/views/*.php
rm -rf storage/framework/cache/data/*
echo "✅ Cache files removed"
echo ""

# Step 7: Verify files
echo "📋 Step 7: Verify changes..."
echo ""
echo "Checking if absensi-bulk.js is disabled:"
if [ -f "resources/js/absensi-bulk.js.disabled" ]; then
    echo "   ✅ absensi-bulk.js.disabled EXISTS (correct!)"
else
    echo "   ❌ absensi-bulk.js.disabled NOT FOUND"
fi

if [ -f "resources/js/absensi-bulk.js" ]; then
    echo "   ❌ WARNING: absensi-bulk.js still exists (should be renamed!)"
else
    echo "   ✅ absensi-bulk.js does NOT exist (correct!)"
fi

echo ""
echo "Checking if Vite built assets exist:"
if [ -d "public/build" ]; then
    echo "   ✅ public/build/ directory exists"
    ASSET_COUNT=$(find public/build -type f -name "*.js" | wc -l)
    echo "   ✅ Found $ASSET_COUNT JavaScript files"
else
    echo "   ❌ WARNING: public/build/ directory not found!"
    echo "      Run: npm run build"
fi
echo ""

# Summary
echo "=========================================="
echo "✅ DEPLOYMENT COMPLETE!"
echo "=========================================="
echo ""
echo "⚠️  IMPORTANT NEXT STEPS:"
echo "   1. Restart PHP-FPM:"
echo "      sudo systemctl restart php8.2-fpm"
echo ""
echo "   2. Restart Web Server:"
echo "      sudo systemctl restart nginx"
echo "      # OR for Apache:"
echo "      # sudo systemctl restart apache2"
echo ""
echo "   3. Open browser and HARD REFRESH:"
echo "      - Windows/Linux: Ctrl + Shift + R"
echo "      - Mac: Cmd + Shift + R"
echo ""
echo "   4. Open Developer Console (F12) and verify:"
echo "      - No 'Loading absensi-bulk.js...' message"
echo "      - No 'bulkAttendanceModal' errors"
echo "      - Pembibitan dropdown appears in form"
echo ""
echo "=========================================="
