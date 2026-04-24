# Task Manager API

Lightweight Task Manager API built with pure PHP, Composer autoloading, and a simple clean architecture split into `Controller -> Service -> Repository`.

## Features

- Minimal REST API for tasks.
- MySQL persistence via PDO.
- Basic migration runner (`migrate.php`).
- Layered structure for easier maintenance and testing.

## Tech Stack

- PHP (native, no framework)
- MySQL
- Composer (PSR-4 autoload)

## Project Structure

```text
.
├── config/
│   └── database.php
├── database/
│   └── migrations/
├── public/
│   └── index.php
├── src/
│   ├── Controllers/
│   ├── Services/
│   ├── Repositories/
│   └── Core/
└── migrate.php
```

## Requirements

- PHP 8.1+ (recommended)
- MySQL 8+ (or compatible)
- Composer

## Getting Started

1. Install dependencies:

```bash
composer install
```

2. Create a MySQL database (default expected name is `task_manager`).

3. Configure database credentials in `config/database.php`:

```php
return [
    'host' => '127.0.0.1',
    'port' => 3306,
    'database' => 'task_manager',
    'username' => 'root',
    'password' => '',
];
```

4. Run migrations:

```bash
php migrate.php
```

5. Start the local PHP server:

```bash
php -S localhost:8000 -t public
```

## API Endpoints

### Health Check

- `GET /health`

Example response:

```json
{
  "status": "ok",
  "message": "API is running"
}
```

### List Tasks

- `GET /tasks`

Returns all tasks ordered by latest first.

### Create Task

- `POST /tasks`
- `Content-Type: application/json`

Request body:

```json
{
  "title": "Buy groceries"
}
```

Example response (`201 Created`):

```json
{
  "id": 1,
  "title": "Buy groceries",
  "status": "pending"
}
```

## Notes

- Migration files run once and are tracked in the `migrations` table.
- Current task persistence is handled through `TaskRepository` and PDO.
