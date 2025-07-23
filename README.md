# Numista-App: Numismatics & Collectibles Project

This is a web application for managing numismatic and other collectible collections. The project is built using the TALL stack on Docker.

- **Backend**: Laravel 12 (dev)
- **Frontend**: Livewire 3 & Blade
- **Admin Panel**: Filament 3, featuring a dynamic, attribute-based form system.
- **Database**: PostgreSQL 16
- **PHP**: 8.2
- **Testing**: Pest (PHPUnit) with automated CI via GitHub Actions.
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
*Note: The `.env` file is already configured for the Docker setup. Remember to set `APP_LOCALE=es` and `APP_URL=http://localhost:8080`.*

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

# 2. Run the automated setup script (installs dependencies, migrates & seeds DB)
make setup
```

The application will be available at **[http://localhost:8080](http://localhost:8080)**.

-   **Admin User:** `admin@numista.es`
-   **Password:** `admin`

---

## ✅ Running Tests & CI

The project uses Pest for testing and includes a GitHub Actions workflow for Continuous Integration.

-   `make test`: Run the entire test suite (Unit & Feature).
-   `make fix`: Automatically fix code style issues using Pint.

The CI pipeline automatically runs `pint --test` and `php artisan test` on every push to the `main` and `development` branches.

---

## 🛠️ Useful Make Commands

-   `make up`: Build and start all containers.
-   `make setup`: **Run this after `make up` on a fresh install.**
-   `make down`: Stop and remove all containers, networks, and volumes.
-   `make artisan a="<command>"`: Run any `php artisan` command (e.g., `make artisan a="list"`).
-   `make composer a="<command>"`: Run any `composer` command.
-   `make db-shell`: Connect to the PostgreSQL database shell.
-   `make clear-all`: Clear all Laravel application caches.
-   `make fix-permissions`: Fix file permissions for `storage` and `bootstrap/cache`.
-   `make help`: Show all available commands.

---

## 🏛️ Project Architecture

The project follows a Domain-Driven Design (DDD) inspired structure.

### Extensible Attribute System

The application's core feature is a flexible **Entity-Attribute-Value (EAV)** model for managing collectible items.

-   **Items (`items` table):** Store only common data (name, description, price, etc.).
-   **Item Types (`type` column):** A fixed list of types (e.g., 'coin', 'stamp', 'comic') defined in `ItemTypeManager.php`.
-   **Attributes (`attributes` table):** A tenant can define custom attributes for their collection (e.g., "Year", "Publisher", "Composition"). This is managed via the **Settings > Attributes** section in the admin panel.
-   **Dynamic Forms:** The "Create/Edit Item" form in Filament is fully dynamic. When a user selects an "Item Type", the form automatically displays the attributes that have been linked to that type via the `AttributeSeeder`.

This architecture allows tenants to customize the data they collect for each type of item without requiring any changes to the database schema or application code by the developer.

### Image Management

-   **Storage:** Images are stored in a private, tenant-specific directory (`storage/app/tenants/tenant-{id}`).
-   **Database:** An `Image` model with a polymorphic relationship allows images to be attached to `Items`.
-   **Access:** A dedicated controller (`TenantFileController`) serves private files securely, while a `PublicImageController` serves images for items that are for sale.
