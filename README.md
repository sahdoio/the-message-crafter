# Message Crafter

This project integrates WhatsApp with BMG Bank to streamline credit applications and client interactions through a modern web application.

## 🚀 stack & technologies
- **backend:** laravel 12, php 8.3
- **frontend:** inertia.js, vue 3, tailwindcss
- **database:** mysql 8
- **messaging:** whatsapp cloud api
- **banking:** bmg bank api
- **queue system:** redis + horizon
- **authentication:** sanctum
- **deployment:** docker & sail

## 🔧 setup instructions
### prerequisites
ensure you have the following installed:
- php 8.3+
- composer
- node.js 18+
- docker & docker-compose
- mysql 8+

### installation
```sh
# clone the repository
git clone https://github.com/your-repo/whatsapp-bmg-integration.git
cd whatsapp-bmg-integration

# install backend dependencies
composer install

# install frontend dependencies
npm install

# copy environment file
cp .env.example .env

# generate app key
php artisan key:generate

# setup database
docker-compose up -d mysql
php artisan migrate --seed

# start the project
docker-compose up -d
docker-compose exec app php artisan serve
npm run dev
```

## ⚡ features
- authenticate users via sanctum
- fetch client data from bmg bank
- send & receive messages via whatsapp api
- process credit applications through chat
- manage message queues with redis + horizon
- dashboard for tracking sales & interactions

## 🔌 api integrations
### whatsapp cloud api
- **sending messages**
- **receiving client responses**
- **handling interactive buttons**

### bmg bank api
- **fetch client credit eligibility**
- **process loan applications**
- **track transaction statuses**

## 📌 environment variables
configure your `.env` file with:
```env
APP_NAME="whatsapp-bmg"
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=whatsapp_bmg
DB_USERNAME=root
DB_PASSWORD=root

WHATSAPP_API_TOKEN=your-token
BMG_API_KEY=your-key
```

## 🛠️ development
```sh
# run backend tests
php artisan test

# run frontend tests
npm run test
```

## 📢 contributing
1. fork the repo
2. create a feature branch (`git checkout -b feature-name`)
3. commit your changes (`git commit -m 'add feature'`)
4. push to the branch (`git push origin feature-name`)
5. open a pull request

## 📝 license
this project is open-source and available under the **mit license**.
