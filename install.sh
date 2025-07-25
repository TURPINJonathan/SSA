#!/bin/bash

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}        SSA API INSTALLATION            ${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

if [ ! -d "./api" ]; then
    echo -e "${RED}❌ The './api' directory does not exist${NC}"
    echo -e "${YELLOW}💡 Make sure you are in the SSA project root${NC}"
    exit 1
fi

cd ./api || { echo -e "${RED}❌ Cannot change directory to ./api${NC}"; exit 1; }

echo -e "${GREEN}📁 Working directory: $(pwd)${NC}"
echo ""

if ! command -v composer &> /dev/null; then
    echo -e "${RED}❌ Composer is not installed. Please install it first.${NC}"
    exit 1
fi

if ! command -v php &> /dev/null; then
    echo -e "${RED}❌ PHP is not installed. Please install it first.${NC}"
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
echo -e "${GREEN}✅ PHP version: $PHP_VERSION${NC}"

echo -e "${BLUE}🔧 Pre-configuration setup${NC}"
echo ""

APP_SECRET=$(openssl rand -hex 16)
echo -e "${YELLOW}🔐 Generating APP_SECRET...${NC}"

echo -e "${YELLOW}📊 Database configuration${NC}"
echo "Choose your database type:"
echo "1) MySQL/MariaDB"
echo "2) PostgreSQL"
echo "3) SQLite"
read -p "Your choice (1-3) [1]: " db_choice
db_choice=${db_choice:-1}

case $db_choice in
    1)
        read -p "Database host [127.0.0.1]: " db_host
        db_host=${db_host:-127.0.0.1}
        
        read -p "Port [3306]: " db_port
        db_port=${db_port:-3306}
        
        read -p "Database name [SSA]: " db_name
        db_name=${db_name:-SSA}
        
        read -p "Username: " db_user
        read -s -p "Password: " db_password
        echo ""
        
        read -p "Server version [8.0.32]: " db_version
        db_version=${db_version:-8.0.32}
        
        DATABASE_URL="mysql://${db_user}:${db_password}@${db_host}:${db_port}/${db_name}?serverVersion=${db_version}&charset=utf8mb4"
        ;;
    2)
        read -p "Database host [127.0.0.1]: " db_host
        db_host=${db_host:-127.0.0.1}
        
        read -p "Port [5432]: " db_port
        db_port=${db_port:-5432}
        
        read -p "Database name [SSA]: " db_name
        db_name=${db_name:-SSA}
        
        read -p "Username: " db_user
        read -s -p "Password: " db_password
        echo ""
        
        read -p "Server version [16]: " db_version
        db_version=${db_version:-16}
        
        DATABASE_URL="postgresql://${db_user}:${db_password}@${db_host}:${db_port}/${db_name}?serverVersion=${db_version}&charset=utf8"
        ;;
    3)
        read -p "SQLite filename [data.db]: " db_file
        db_file=${db_file:-data.db}
        
        DATABASE_URL="sqlite:///%kernel.project_dir%/var/${db_file}"
        ;;
    *)
        echo -e "${RED}❌ Invalid choice${NC}"
        exit 1
        ;;
esac

echo -e "${GREEN}✅ Database configuration completed${NC}"
echo ""

echo -e "${YELLOW}🔑 Generating JWT keys...${NC}"

mkdir -p config/jwt

JWT_PASSPHRASE=$(openssl rand -hex 32)

echo -e "${YELLOW}📝 Writing configuration...${NC}"

cat > .env.local << EOF
###> symfony/framework-bundle ###
APP_SECRET=$APP_SECRET
###< symfony/framework-bundle ###

###> doctrine/doctrine-bundle ###
DATABASE_URL="$DATABASE_URL"
###< doctrine/doctrine-bundle ###

###> lexik/jwt-authentication-bundle ###
JWT_PASSPHRASE=$JWT_PASSPHRASE
###< lexik/jwt-authentication-bundle ###
EOF

echo -e "${GREEN}✅ Configuration saved in .env.local${NC}"
echo ""

echo -e "${YELLOW}📦 Installing Composer dependencies...${NC}"
COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --optimize-autoloader --quiet 2>/dev/null

if [ $? -ne 0 ]; then
    echo -e "${YELLOW}⚠️  Trying with dev dependencies...${NC}"
    COMPOSER_ALLOW_SUPERUSER=1 composer install --quiet 2>/dev/null
    
    if [ $? -ne 0 ]; then
        echo -e "${RED}❌ Error during dependencies installation${NC}"
        exit 1
    fi
fi

echo -e "${GREEN}✅ Dependencies installed successfully${NC}"
echo ""

echo "$JWT_PASSPHRASE" | php bin/console lexik:jwt:generate-keypair --overwrite --quiet 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ JWT keys generated successfully${NC}"
else
    echo -e "${RED}❌ Error during JWT keys generation${NC}"
    exit 1
fi

echo -e "${YELLOW}🔍 Testing database connection...${NC}"

php bin/console doctrine:database:create --if-not-exists --quiet 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Database created/verified successfully${NC}"
else
    echo -e "${YELLOW}⚠️  Database already exists or connection error${NC}"
fi

echo -e "${YELLOW}🔄 Running migrations...${NC}"
php bin/console doctrine:migrations:migrate --no-interaction --quiet 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ Migrations executed successfully${NC}"
else
    echo -e "${YELLOW}⚠️  No migrations to execute or error${NC}"
fi

echo ""
echo -e "${YELLOW}👤 Creating an agent${NC}"
read -p "Do you want to create an agent user now? (Y/n): " create_agent

if [[ $create_agent =~ ^[Nn]$ ]]; then
	echo -e "${BLUE}💼 Skipping agent creation${NC}"
else
	php bin/console cr:ag

	while true; do
		echo ""
		read -p "Do you want to create another agent? (y/N): " create_another
		
		if [[ $create_another =~ ^[Yy]$ ]]; then
			php bin/console cr:ag
		else
			echo -e "${BLUE}💼 Agent creation completed${NC}"
			break
		fi
	done
fi

echo -e "${YELLOW}🔒 Setting permissions...${NC}"
chmod 600 config/jwt/private.pem 2>/dev/null
chmod 644 config/jwt/public.pem 2>/dev/null

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}    ✅ Installation completed!          ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo -e "${BLUE}📋 Installation summary:${NC}"
echo -e "   • Composer dependencies installed"
echo -e "   • Environment configuration (.env.local)"
echo -e "   • JWT keys generated"
echo -e "   • Database configured"
echo -e "   • Migrations executed"
echo -e "   • Agent user created (if selected)"
echo ""
echo -e "${BLUE}🚀 To start the server:${NC}"
echo -e "   cd api && symfony server:start"
echo ""
echo -e "${BLUE}📮 Postman Collection:${NC}"
echo -e "   • Import 'postman_collection.json' into Postman"
echo -e "   • Use http://localhost:8000 as base URL"
echo -e "   • Run LOGIN request first to authenticate"
echo ""
echo -e "${BLUE}📖 Useful URLs:${NC}"
echo -e "   • API Documentation: http://localhost:8000/api"
echo -e "   • Authentication: POST http://localhost:8000/auth"
echo ""
echo -e "${YELLOW}⚠️  Note: The .env.local file contains sensitive data${NC}"
echo -e "   and should NOT be committed to Git."
echo ""