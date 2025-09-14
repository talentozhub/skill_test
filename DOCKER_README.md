# 🐳 Docker Setup for Simple PHP Employee Management System

This project has been dockerized to make it easy to run and develop with Docker!

## 🚀 Quick Start

### Option 1: Using the startup script (Recommended)
```bash
./start.sh
```

### Option 2: Manual Docker Compose
```bash
# Build and start containers
docker-compose up --build -d

# View logs
docker-compose logs -f

# Stop containers
docker-compose down
```

## Access Points

Once running, you can access:

- **Web Interface**: http://localhost:8000/web_app/new.php
- **API Endpoint**: http://localhost:8000/api/new.php  
- **Dashboard**: http://localhost:8000/web_app/dashboard.php

## Database Information

- **Host**: localhost (or `mysql` from within containers)
- **Port**: 3306
- **Database**: test
- **Username**: root
- **Password**: root

## Docker Commands

```bash
# View running containers
docker-compose ps

# View logs
docker-compose logs -f

# Stop all services
docker-compose down

# Restart services
docker-compose restart

# Access web container shell
docker-compose exec web bash

# Access database
docker-compose exec mysql mysql -u root -p test

# Rebuild containers
docker-compose up --build
```

## Architecture

The Docker setup includes:

1. **Web Container** (`simple-php-web`):
   - PHP 7.4 with Apache
   - MySQL extensions installed
   - Application code mounted as volume

2. **Database Container** (`simple-php-mysql`):
   - MySQL 8.0
   - Database initialized with schema from `assets/db.sql`
   - Persistent data storage

## Development

### Making Changes
- Code changes are automatically reflected (volume mounted)
- Database changes persist between container restarts
- Use `docker-compose restart web` to restart just the web service

### Environment Variables
The application uses these environment variables:
- `DB_HOST`: Database host (default: localhost)
- `DB_NAME`: Database name (default: test)
- `DB_USER`: Database user (default: root)
- `DB_PASSWORD`: Database password (default: root)

## Troubleshooting

### Container won't start
```bash
# Check logs
docker-compose logs

# Rebuild from scratch
docker-compose down
docker-compose up --build
```

### Database connection issues
```bash
# Check if database is running
docker-compose ps

# Check database logs
docker-compose logs mysql
```

### Permission issues
```bash
# Fix file permissions
sudo chown -R $USER:$USER .
```

## Learning Docker

This setup demonstrates several Docker concepts:

1. **Multi-container applications** with docker-compose
2. **Service dependencies** (web depends on mysql)
3. **Volume mounting** for development
4. **Environment variables** for configuration
5. **Custom Dockerfile** with PHP extensions
6. **Database initialization** with SQL scripts

## Next Steps

Try these Docker learning exercises:

1. **Modify the Dockerfile** to add more PHP extensions
2. **Add a Redis container** for caching
3. **Create a production docker-compose.yml** with different settings
4. **Add health checks** to the containers
5. **Use Docker secrets** for sensitive data
6. **Create a CI/CD pipeline** with Docker

Happy Docker learning! 🐳
