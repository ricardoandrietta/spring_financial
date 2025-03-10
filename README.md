# Spring Financial - Leaderboard Application

## Project Overview

Spring Financial is a real-time leaderboard application built with Laravel 12 following Clean Architecture principles.  
The application allows you to create users, manage their scores, and view a real-time leaderboard with updates.

## Key Features

- Real-time leaderboard updates using Laravel Echo and Pusher
- Complete CRUD operations for users
- RESTful API for programmatic access
- QR code generation for user addresses
- Clean Architecture implementation
- Comprehensive test suite using PEST

## Architecture

The application follows Clean Architecture principles with distinct layers:

- **Domain Layer**: Core business logic, entities, and repository interfaces
- **Application Layer**: Use cases and DTOs
- **Infrastructure Layer**: Repository implementations and external services
- **Presentation Layer**: Controllers and views

You can find more details in the [project_structure](readme_files/project_structure.md) file

## Prerequisites

- Docker and Docker Compose
- Git

## Requirements

- PHP 8.3+
- Composer
- Node.js and NPM
- MySQL or another database supported by Laravel

## Getting Started with Docker

### 1. Clone the Repository

```bash
git clone https://github.com/ricardoandrietta/spring_financial.git
cd spring_financial
```

### 2. Create Data Directory
The database volume folder (assuming that you'll be using MySQL as your database)
```bash
mkdir -p data
```

### 3. Start Docker Containers

```bash
docker compose up -d
```

This will start the following services:
- **php-fpm**: PHP 8.3 FPM container (spring_php)
- **webserver**: Nginx web server (spring_web)
- **mysql**: MySQL 8.0 database (spring_db)

### 4. Install Dependencies

```bash
docker compose exec -it spring_php composer install
```

### 5. DB Configuration
The MySQL database is configured with the following credentials:
- **Host**: localhost (from host) or spring_db (from containers)
- **Port**: 3306
- **Database**: leaderboard
- **Username**: leaderboard
- **Password**: financial_202503

You can change these default values in your docker-compose.yml

Create your `.env` file
```bash
cp application/.env.example application/.env
```

Update the `.env` file with your database credentials details:
```
DB_CONNECTION=mysql
DB_HOST=spring_db
DB_PORT=3306
DB_DATABASE=leaderboard
DB_USERNAME=leaderboard
DB_PASSWORD=financial_202503
```

### 6. Set Up the Application

```bash
# Enter the PHP container
docker compose exec php spring_php bash

# Inside the container, run:
php artisan key:generate
php artisan migrate
php artisan db:seed
npm install && npm run build

# Exit the container
exit
```
### 7. Access the Application

The application will be available at:
- **Web Interface**: [http://localhost:8000](http://localhost:8000)
- **API Endpoints**: http://localhost:8000/api/v1/users

## Available API Endpoints

- `POST /api/v1/users` - Create a new user
- `GET /api/v1/users` - List all users
- `GET /api/v1/users/{id}` - Get a specific user
- `PATCH /api/v1/users/{id}/add-points` - Add points to a user
- `PATCH /api/v1/users/{id}/subtract-points` - Subtract points from a user
- `DELETE /api/v1/users/{id}` - Delete a user
- `GET /api/v1/users/by_score` - Get users grouped by score, showing their names and average age

Here is a Postman Collection for you to get started with these APIs:
[Spring Financial.Collection](readme_files/SpringFinancial.postman_collection.json)

## Resetting Scores

To reset all user scores:

```bash
docker exec -it spring_php php artisan app:reset-score
```

## User's Address QR Code Generation

### Implementation Details

- Leverages Laravel Queues/Jobs for asynchronous processing
- QR codes are generated using the goQR.me API
- QR images are saved in PNG format in `/application/storage/app/private/qr_codes` folder
- The file path is stored in the `users.qr_code_path` column
- Filename pattern: `qrcode_{user_id}.png` (e.g., `qrcode_1.png`)

By design, when a user is created, a job is created to generate the QR code asynchronously.

### Queue Setup Instructions
1. Configure your queue driver (currently set to use database)  
   1.1. In the `.env` file: `QUEUE_CONNECTION=database`
2. Ensure storage is properly configured
3. Run a queue worker to process the jobs:

### Start queue worker
```bash
docker exec -it spring_php php artisan queue:work
```

### List Scheduled Jobs
```bash
docker exec -it spring_php php artisan schedule:list
```

### Start Schedule worker
```bash
docker exec -it spring_php php artisan schedule:work
```

## Running Tests

To run the test suite:

```bash
# Enter the PHP container
docker compose exec spring_php bash

# Run all tests
php artisan test

# Run specific test files
php artisan test --filter=UserEntityTest

# Run tests with coverage report (requires Xdebug)
# Change the phpdocker/php-fpm/php-ini-overrides.ini file: add coverage to xdebug.mode
php artisan test --coverage

# Exit the container
exit
```

## Docker Commands

- Start containers: `docker compose up -d`
- Stop containers: `docker compose stop`
- View logs: `docker compose logs` or `docker compose logs SERVICE_NAME`
- Shell into PHP container: `docker compose exec -it spring_php bash`
- Run artisan commands: `docker compose exec -it spring_php php artisan COMMAND`
- MySQL shell: `docker compose exec -it mysql mysql -uleaderboard -pfinancial_202503 leaderboard`

## Debugging

The Docker setup includes Xdebug configuration for debugging with IDEs like PHPStorm.

### For PHPStorm:
1. In PHPStorm, go to Preferences | Languages & Frameworks | PHP | Servers
2. Add a new server named "SpringFinancial"
3. Set the port to 8000
4. Enable "Use path mappings" and map your local project path to `/application` in the container

## Troubleshooting

### File Permissions
If you encounter file permission issues, run:
```bash
docker compose exec spring_php chown -R www-data:www-data /application/storage
```

### Database Connection Issues
If the application can't connect to the database, ensure:
1. The containers are running: `docker compose ps`
2. The database credentials in `.env` match those in `docker compose.yml`
3. The database has been created: `docker compose exec spring_db mysql -u root -pspring_032025 -e "SHOW DATABASES;"`

## License

[License information]