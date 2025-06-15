# Numista-App: Numismatics & Collectibles Project

This is a web application for managing numismatic and other collectible collections. The project is built using the TALL stack on Docker.

- **Backend**: Laravel 12 (dev)
- **Frontend**: Livewire 3 & Blade
- **Admin Panel**: Filament 3
- **Database**: PostgreSQL 16
- **PHP**: 8.2
- **Web Server**: Nginx
- **Environment**: Docker

---

## 🚀 Local Development Setup

### Prerequisites

- [Docker](https://www.docker.com/products/docker-desktop)
- [Make](https://www.gnu.org/software/make/) (usually pre-installed on Linux/macOS, available for Windows via Chocolatey or WSL)

### 1. Clone the Repository

```bash
git clone <your-repository-url>
cd numista-app
```

### 2. Configure Environment Files

First, copy the Laravel example environment file:
```bash
cp .env.example .env
```
*Note: The `.env` file is already configured for the Docker setup. You don't need to change `DB_HOST`.*

Second, create the `.env` file for Docker Compose to set your user ID. This avoids file permission issues.
*On Linux/macOS:*
```bash
echo "UID=$(id -u)" > .env
```
*On Windows, you may need to create the `.env` file manually and set `UID=1000`.*


### 3. Build and Run the Application

The whole setup process is automated with `make`.

```bash
# 1. Build and start all services in detached mode
make up

# 2. Run the automated setup script
make setup
```

The application will be available at **[http://localhost:8080](http://localhost:8080)**.

---

## 🛠️ Useful Make Commands

- `make up`: Build and start all containers.
- `make setup`: **Run this after `make up` on a fresh install.** Installs dependencies, generates key, and runs migrations.
- `make down`: Stop and remove all containers, networks, and volumes.
- `make stop`: Stop containers without removing them.
- `make logs`: View real-time logs for all services.
- `make bash`: Get a shell (`sh`) inside the `app` container.
- `make artisan <command>`: Run any `php artisan` command (e.g., `make artisan migrate:status`).
- `make composer <command>`: Run any `composer` command (e.g., `make composer update`).
- `make db-shell`: Connect to the PostgreSQL database shell.
