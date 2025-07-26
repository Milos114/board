# Ticket Board

A modern ticket management system built with Laravel 11. This application provides a comprehensive solution for managing tickets with lanes, priorities, and user management, complete with activity logging.

## Features

- **User Authentication**: Secure JWT-based authentication system
- **Role-Based Access Control**: Manage user permissions effectively
- **Ticket Management**: Create, edit, view, and delete tickets
- **Lane System**: Organize tickets in customizable lanes (e.g., To Do, In Progress, Done)
- **Priority Levels**: Assign priorities to tickets
- **Activity Logging**: Track changes made to tickets
- **File Attachments**: Attach files to tickets
- **API Endpoints**: Full RESTful API for integration with other systems
- **Filament Admin Panel**: Advanced admin interface

## Tech Stack

- **Backend**: Laravel 11, PHP 8.2+
- **Authentication**: JWT Auth
- **Database**: MySQL (configurable)
- **Testing**: PHPUnit with Paratest for parallel testing
- **Documentation**: Scribe API Documentation
- **Error Tracking**: Sentry integration
- **Admin Panel**: Filament

## Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- MySQL or compatible database
- Redis (optional, for caching)

### Setup Instructions

1. Clone the repository:
   ```bash
   git clone https://github.com/Milos114/board.git
   cd board
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set up environment:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure database in `.env`:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. Run migrations and seeders:
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. Generate JWT secret:
   ```bash
   php artisan jwt:secret
   ```

7. Start the development server:
   ```bash
   php artisan serve
   ```

## Docker Setup

The project includes Docker configuration for easy setup:

```bash
docker-compose up -d
```

This will set up containers for PHP, Nginx, and MySQL.

## API Documentation

After installation, you can generate API documentation using Scribe:

```bash
php artisan scribe:generate
```

The documentation will be available at `/docs` endpoint.

## Testing

Run the test suite with:

```bash
php artisan test
```

Or use parallel testing for faster results:

```bash
php artisan test --parallel
```

## License

This project is licensed under the MIT License.

---

Developed and maintained by [Milos114](https://github.com/Milos114)