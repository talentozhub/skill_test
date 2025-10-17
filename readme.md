# AwesomeCorp Employee Management System

This project is a simplified Employee Management System for AwesomeCorp. It allows employees to register via a web interface or via an API (used by RecruiterForce). It demonstrates service layers, repository pattern, CSV reporting, audit logging, and email notifications.

---

## Requirements

* PHP 5.3 and above (compatible with PHP 7.1)
* MySQL / MariaDB
* Composer
* Docker & Docker Compose (optional for local development)

---

## Features

* Register new employees via `web_app/new.php`
* Register new employees via API `api/new.php`
* Audit logs for employee creation
* Email notification to employees with password and login info
* CSV report of new employees (`/tmp/employee_report.csv`)
* Supports new `gender` field for employees
* Repository pattern and service layers implemented for maintainable code

---

## Installation

### Using Docker (recommended)

1. Build and start containers:

```bash
docker-compose up -d --build
```

2. Run this once containers are up

```bash
docker exec -it skill_test_web composer install

docker exec -it skill_test_web composer dump-autoload

docker exec -i skill_test_db mysql -u root -proot test < assets/db.sql
```

3. Access on the web:

```
http://localhost:8000/web_app/new.php
```

4. Use this API for post request or Test Data:

```
http://localhost:8000/api/new.php
```

---

## How to Use

### Web Registration

Visit:

```
http://localhost:8000/web_app/new.php
```

Fill in the form and submit. After registration, you will be redirected to the dashboard:

```
http://localhost:8000/web_app/dashboard.php
```

---

### API Registration

Send a POST request to:

```
http://localhost:8000/api/new.php
```

---

### CSV Report

Each time a new employee is added, their data is appended to:

```
/tmp/employee_report.csv
```

---

## Design

* **Service Layer**: `EmployeeService` handles business logic.
* **Repository Pattern**: `EmployeeRepository` handles database interactions.
* **Utility Classes**: `Validator` for input validation, `Database` for DB connection.
* **Separation of Concerns**: Email, audit, and CSV reporting are handled by dedicated services.
