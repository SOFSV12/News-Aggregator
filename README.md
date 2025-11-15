# News Aggregator Project

A Laravel-based News Aggregator application that fetches news articles from multiple sources via their APIs, normalizes the data, and stores it in a PostgreSQL database. It supports background jobs and scheduling for periodic updates.

## Features

- Fetches articles from multiple news sources (e.g., The Guardian, NewsAPI, NY Times, AI-generated news endpoints).
- Normalizes API responses into a consistent format.
- Stores articles in PostgreSQL.
- Background processing using Laravel Horizon and Redis.
- Scheduler to fetch news periodically.
- Nginx web server with Dockerized setup.

## Prerequisites

- Docker Desktop
- git
- postman for testing frontend endpoint

## Getting Started

### 1. Clone the repository

### 2. Configure the .env file

```bash
cp .env.example .env
```

API Keys:

```
THE_GUARDIAN_API_KEY=your_guardian_key
NEWSAPI_ORG_API_KEY=your_newsapi_key
NEWSAPI_AI_API_KEY=your_newsai_key
THE_NEW_YORK_TIMES_API_KEY=your_nyt_key
```

https://developer.nytimes.com provides various uses of their endpoint i use Times Newswire API and my app i created works only with this enabled for my endpoint
Times Wire API - Real-time feed of NYT article publishes

PostgreSQL settings:

```
DB_CONNECTION=pgsql
DB_HOST=db
DB_PORT=5432
DB_DATABASE=news_db
DB_USERNAME=news_user
DB_PASSWORD=secretpassword
```

Redis settings:
```
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### 3. Important Volume Note

The project uses a Docker volume to map your local Laravel project into the container:
```
volumes:
  - "C:/laragon/www/News-Aggregator:/var/www/html"
  ```

⚠️ Make sure the path matches your actual local folder.

Incorrect volume paths will prevent the project from running properly.
Adjust the path according to your platform (Windows, Mac, Linux).

### 4. Start Docker containers
```
docker compose up -d --build
```
This starts:

    app → Laravel PHP-FPM
    db → PostgreSQL
    redis → Redis server
    horizon → Laravel Horizon
    nginx → Web server
    pgadmin → Optional PostgreSQL GUI

#### 5. Install Composer dependencies (if vendor folder is missing)

If you see artisan command not found or vendor/autoload.php missing:
bash

    docker compose exec app bash
    composer install
    cp .env.example .env
    php artisan key:generate

This will generate the vendor folder and Laravel application key.

### 6. Run migrations


    docker compose exec app bash
    php artisan migrate


### 7. Verify Redis extension

    Ensure Redis is installed and enabled in PHP:
    bash

    docker compose exec app bash
    php -m | grep redis

    Output should return:
    text

    redis

### 8. Access the application

    Main Application: http://127.0.0.1:8080

    Horizon dashboard (queues): http://127.0.0.1:8080/horizon

    PG admin4: http://127.0.0.1:5050

PGAdmin credentials:

    Email: admin@admin.com

    Password: admin

Create a new server:

    Input a name

    Move to connection tab

    Pass db as host (specified in docker-compose.yml)

    Pass in the DB_USER and DB_PASSWORD (specified in your .env)

    Click on save

### 9. Starting Scheduler
```
docker compose exec app php artisan schedule:work
```
logs can be found in storage/logs, logs are set to run on daily 

### 10. Stopping the project 
```
docker compose down
```

 




