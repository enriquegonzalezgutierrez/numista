# Numista-App: Numismatics & Collectibles Project

This is a web application for managing numismatic and other collectible collections. The project is built using the TALL stack on Docker.

- **Backend**: Laravel 12 (dev)
- **Frontend**: Livewire 3 & Blade
- **Admin Panel**: Filament 3, featuring dynamic forms, relation managers, advanced filters, and bulk actions.
- **Database**: PostgreSQL 16
- **PHP**: 8.2
- **Testing**: Pest (PHPUnit)
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

# 2. Run the automated setup script
make setup
```

The application will be available at **[http://localhost:8080](http://localhost:8080)**.

- **Default User:** `admin@numista.es`
- **Password:** `admin`
- **Test Images:** The database seeder assigns random images to the generated items. For these images to display correctly, you need to place your own sample images in `storage/app/tenants/tenant-1/item-images/`. The seeder expects files like `Moneda-Antigua.png`, `Cómic-Clásico.png`, etc.

---

## ✅ Running Tests

The project uses Pest for testing. The test environment is configured to use an in-memory SQLite database to ensure tests are fast and do not interfere with your development data.

- `make test`: Run the entire test suite.
- `make test-feature`: Run only the Feature tests.
- `make test-unit`: Run only the Unit tests.
- `make test-coverage`: Run tests and generate a code coverage report in the terminal.

---

## 🛠️ Useful Make Commands

- `make up`: Build and start all containers.
- `make setup`: **Run this after `make up` on a fresh install.** Installs dependencies, generates key, and runs migrations with seeders.
- `make test`: Run all automated tests.
- `make down`: Stop and remove all containers, networks, and volumes.
- `make stop`: Stop containers without removing them.
- `make logs`: View real-time logs for all services.
- `make bash`: Get a shell (`sh`) inside the `app` container.
- `make artisan a="<command>"`: Run any `php artisan` command (e.g., `make artisan a="migrate:status"`).
- `make composer a="<command>"`: Run any `composer` command (e.g., `make composer update`).
- `make npm a="<command>"`: Run any `npm` command (e.g., `make npm a="install"`).
- `make db-shell`: Connect to the PostgreSQL database shell.
- `make clear-all`: Clear all Laravel application caches.
- `make fix-permissions`: Fix file permissions for `storage` and `bootstrap/cache`.

---

## 🏛️ Project Architecture

The project follows a Domain-Driven Design (DDD) inspired structure and is tested to ensure code quality and stability.

### Automated Testing

- **Strategy:** We focus on a combination of Unit and Feature tests to cover our core business logic.
- **Unit Tests:** Located in `tests/Unit`, they verify individual classes in isolation (e.g., Managers).
- **Feature Tests:** Located in `tests/Feature`, they test the integration of different parts of the application, including model relationships and observer logic, by interacting with an in-memory test database.

### Image Management

- **Storage:** Images are stored in a private, tenant-specific directory (`storage/app/tenants/tenant-{id}`).
- **Database:** An `Image` model with a polymorphic relationship allows images to be attached to any other model (currently `Item`). This relationship is managed via the `images` table.
- **Access:** A dedicated route (`/tenant-files/{path}`) and controller (`TenantFileController`) are used to serve the private files securely.
- **Background Processing:** Image uploads are processed asynchronously using Laravel Queues and a Supervisor-managed worker for a better user experience.
- **UI:** The `ImagesRelationManager` on the `ItemResource` edit page handles the full image CRUD lifecycle.

### Extensible Systems: Item `Type`, `Status`, and `Grade`

The application uses a manager-based, extensible pattern for key item attributes. This follows SOLID principles, especially Open/Closed. To add a new option (e.g., a new "Type"):

1.  **Translation:** Add the new key and its Spanish translation to the appropriate language file (e.g., `lang/es/item.php`).
2.  **Handler Class (Optional):** If the new option requires special logic or fields, create a new handler class in the corresponding directory (e.g., `src/Collection/UI/Filament/ItemTypes/`).
3.  **Manager Registration:** Add the new key and its handler class (or `null`) to the `types` array in the corresponding manager (e.g., `ItemTypeManager.php`).

### Category Management

- **Category Management:** Categories are managed via the `CategoryResource`. This resource allows creating, editing, and deleting categories, including setting up parent-child relationships for a hierarchical structure.
- **Assigning to Items:** From an item's edit page, categories can be attached or detached using the "Categorías" Relation Manager.
