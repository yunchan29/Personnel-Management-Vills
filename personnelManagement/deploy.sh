#!/bin/bash

# ==============================================
# Personnel Management System - Deployment Script
# For use on Hostinger server via SSH
# ==============================================

echo "🚀 Starting deployment process..."

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${RED}❌ Error: .env file not found!${NC}"
    echo "Please create .env file from .env.hostinger template"
    exit 1
fi

# Step 1: Put application in maintenance mode
echo -e "${YELLOW}⏸️  Putting application in maintenance mode...${NC}"
php artisan down --retry=60

# Step 2: Pull latest changes (if using Git on server)
if [ -d .git ]; then
    echo -e "${YELLOW}📥 Pulling latest changes from Git...${NC}"
    git pull origin main
fi

# Step 3: Install/Update Composer dependencies
echo -e "${YELLOW}📦 Installing Composer dependencies...${NC}"
composer install --optimize-autoloader --no-dev --no-interaction

# Step 4: Install/Update NPM dependencies
echo -e "${YELLOW}📦 Installing NPM dependencies...${NC}"
npm ci

# Step 5: Build frontend assets
echo -e "${YELLOW}🔨 Building frontend assets...${NC}"
npm run build

# Step 6: Clear all caches
echo -e "${YELLOW}🧹 Clearing caches...${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Step 7: Run database migrations
echo -e "${YELLOW}🗄️  Running database migrations...${NC}"
php artisan migrate --force

# Step 8: Create storage symlink if not exists
echo -e "${YELLOW}🔗 Creating storage symlink...${NC}"
php artisan storage:link

# Step 9: Optimize for production
echo -e "${YELLOW}⚡ Optimizing for production...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Step 10: Set proper permissions
echo -e "${YELLOW}🔐 Setting file permissions...${NC}"
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chmod 600 .env

# Step 11: Bring application back up
echo -e "${YELLOW}▶️  Bringing application back online...${NC}"
php artisan up

echo -e "${GREEN}✅ Deployment completed successfully!${NC}"
echo ""
echo "📊 Application Status:"
php artisan --version
echo ""
echo "🔍 To view logs: tail -f storage/logs/laravel.log"
echo "🧪 To test queue: php artisan queue:work --once"
