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
*Note: The `.env` file is already configured for the Docker setup. Remember to set `APP_LOCALE=es` and `APP_URL=http://localhost:8080` for a correct setup.*

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

---

## 🛠️ Useful Make Commands

- `make up`: Build and start all containers.
- `make setup`: **Run this after `make up` on a fresh install.** Installs dependencies, generates key, and runs migrations with seeders.
- `make down`: Stop and remove all containers, networks, and volumes.
- `make stop`: Stop containers without removing them.
- `make logs`: View real-time logs for all services.
- `make bash`: Get a shell (`sh`) inside the `app` container.
- `make artisan a="<command>"`: Run any `php artisan` command (e.g., `make artisan a="migrate:status"`).
- `make composer a="<command>"`: Run any `composer` command (e.g., `make composer update`).
- `make npm a="<command>"`: Run any `npm` command (e.g., `make npm a="install"`).
- `make db-shell`: Connect to the PostgreSQL database shell.
- `make clear-all`: Clear all Laravel application caches.
- `make fix-permissions`: Fix file permissions for `storage` and `bootstrap/cache` directories.

---

## 🏛️ Project Architecture: Extensible Systems

The application uses a SOLID-based architecture to manage different entities like Item Types, Statuses, and Grades. The `...Manager` classes act as the single source of truth for each system.

### How to Add a New Item Type

1.  **Translation:** Add the new type's key (e.g., `type_wine`) and its Spanish translation to `lang/es/item.php`.
2.  **Register Type:** Open `src/Collection/UI/Filament/ItemTypeManager.php` and add the new type key (e.g., `'wine'`) to the `$types` array. Set its value to `null` if it has no custom fields, or to a handler class name.
3.  **(Optional) Custom Fields:** If the type requires specific fields:
    -   Add `nullable` columns to the `items` table via a new migration.
    -   Add the new columns to the `$fillable` array in the `Item` model.
    -   Add translations for the new field labels in `lang/es/item.php`.
    -   Create a new handler class in `src/Collection/UI/Filament/ItemTypes/` (e.g., `WineType.php`) that implements `ItemType`.
    -   Update the `ItemTypeManager` to point the type key to your new handler class.

### How to Add a New Item Status

The process is managed by the `ItemStatusManager`.

1.  **Translation:** Add the new status key (e.g., `status_reserved`) and its Spanish translation to `lang/es/item.php`.
2.  **Create Status Class:** Create a new empty class in `src/Collection/UI/Filament/ItemStatuses/` (e.g., `ReservedStatus.php`) that implements the `ItemStatus` interface.
3.  **Register Status:** Open `src/Collection/UI/Filament/ItemStatusManager.php`, import your new class, and add it to the `$statuses` array (e.g., `'reserved' => ReservedStatus::class`).

The `Select` dropdown in the form will update automatically.

### How to Add a New Item Grade

The process is managed by the `ItemGradeManager` and is identical to adding a new status.

1.  **Translation:** Add the new grade key (e.g., `grade_pr`) and its Spanish translation (e.g., "PR (Proof)") to `lang/es/item.php`.
2.  **Create Grade Class:** Create a new empty class in `src/Collection/UI/Filament/ItemGrades/` (e.g., `PrGrade.php`) that implements `ItemGrade`.
3.  **Register Grade:** Open `src/Collection/UI/Filament/ItemGradeManager.php`, import your new class, and add it to the `$grades` array (e.g., `'pr' => PrGrade::class`).

After making changes, run `make clear-all` to ensure caches are updated.
