# Watchtower Server

This repository contains the backend server for the Watchtower application. This is built with Laravel 8 and common tools found in the ecosystem. This application is designed to work together with the Watchtower Client built with Vue.js 3.

## Requirements
- Pusher Account
- Redis Server

## Installation

### 1. Install & Compile Dependencies.

We have two sets of dependencies to install, one is for PHP/Composer packages and the other one is JS/Node dependencies. Run the commands shown below to install the required dependencies.

```
composer install
npm install
npm run production
```

### 2. Run the migrations.

The application seeds initial data right in the migrations, there is no need to do anything other than the command below:

```
php artisan migrate
```

### 3. Install Channels.

Watchtower has a channels system and each one is installed via an artisan command. Run the following command below and follow the instructions in the CLI to install channels.

```
php artisan channels:install
```

## Deployment

```
composer install
npm install
npm run production
php artisan migrate --force
```

### Notes

- Run Queue Worker (php artisan queue:work)
- Run Horizon (php artisan horizon)
