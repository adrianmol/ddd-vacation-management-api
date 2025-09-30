# 📘 Vacation Management API

A **Domain-Driven Design (DDD)** inspired API built in pure PHP (no frameworks), following layered architecture principles.

---

## 🚀 Features

- **Domain-Driven Design** (Application, Business, Persistence layers).
- **Custom Dependency Injection Container**.
- **Router with Reflection-based Request Mapping**.
- **DTOs & Transfers with Validation**.
- **QueryBuilder & ActiveRecord-style Models (MySQLi)**.
- **Migrations & Seeders** (pure PHP).
- **API Key Authentication (Manager/Employee roles)**.

---

## 🛠 Requirements

- PHP **8.3+**
- MySQL **8+** (or MariaDB)
- Composer

---

## 📦 Installation

Clone repository:

```bash
git clone https://github.com/your-org/vacation-management-api.git
cd vacation-management-api

Install dependencies:

Option 1: Without Docker

composer install

Copy environment config:

cp .env.example .env


Update .env with your database credentials:

DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=vacation_management_db
DB_USERNAME=root
DB_PASSWORD=

🗄 Database

Run migrations:

php database/migrate.php


Run seeders (e.g., create initial manager):

php database/seed.php

▶️ Running the API

Start local PHP server:

php -S localhost:8000 index.php


API available at:
👉 http://localhost:8000/api

Option 2: With Docker

Build and start services:

docker-compose up --build


API runs on http://localhost:8000

MySQL runs on port 3306

Run migrations:

docker-compose exec app php database/migrate.php


Run seeders (optional):

docker-compose exec app php database/seed.php


Stop containers:

docker-compose down

--

## 📬 API Testing with Postman

To make testing easier, this project includes a **Postman collection** with ready-to-use requests.

### Import Collection

1. Open **Postman**.
2. Click **Import**.
3. Select the file [`collection.json`](collection.json) from the project root.
4. Postman will create a collection named **Vacation Management API**.


📂 Project Structure
src/
  Application/   # Application layer (Requests, Controllers, DTOs)
  Domain/ #
  Infrastructure/
    Core/        # AppKernel, Database, Router, Container
    Http/        # BaseRequest, Response
    Route/       # Router
database/
  migrate.php    # Run migrations
  seed.php       # Run seeders
index.php        # App entry point

🤝 Contributing

Fork the repo

Create feature branch: git checkout -b feature/my-feature

Commit changes: git commit -m "Add my feature"

Push branch: git push origin feature/my-feature

Create Pull Request

---

✅ That’s a clean, professional README.  

👉 Do you also want me to generate a **small ASCII architecture diagram** (showing `Request → Router → Controller → Business → Repository → DB`) to include in the README for quick onboarding?