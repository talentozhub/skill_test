#!/bin/bash

echo "Starting Simple PHP Employee Management System with Docker..."

# Build and start the containers
docker-compose up --build -d

echo "Containers started successfully!"
echo ""
echo "Application URLs:"
echo "   Web Interface: http://localhost:8000/web_app/new.php"
echo "   API Endpoint:  http://localhost:8000/api/new.php"
echo "   Dashboard:     http://localhost:8000/web_app/dashboard.php"
echo ""
echo "Database:"
echo "   Host: localhost"
echo "   Port: 3306"
echo "   Database: test"
echo "   Username: root"
echo "   Password: root"
echo ""
echo "Useful Commands:"
echo "   View logs:     docker-compose logs -f"
echo "   Stop:          docker-compose down"
echo "   Restart:       docker-compose restart"
echo "   Shell access:  docker-compose exec web bash"
echo ""
echo "🎉 Happy coding with Docker!"
