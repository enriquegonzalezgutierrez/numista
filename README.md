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
*Note: The `.env` file is already configured for the Docker setup. Remember to set `APP_LOCALE=es` for Spanish language support.*

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

---

## 🏛️ Project Architecture: Managing Item Types

The application uses an extensible, SOLID-based architecture to manage different types of collectible items. To add a new item type (e.g., "Wine"), follow these steps. The `ItemTypeManager` acts as the single source of truth.

### Step 1: Add Translation

Add the new type's key and its Spanish translation to the `lang/es/item.php` file.

```php
// lang/es/item.php
'type_wine' => 'Vino',
```

### Step 2: Register the New Type

Open `app/Filament/ItemTypeManager.php` and add the new type key to the `$types` array.

- If the new type only uses a generic form (no specific fields), set its value to `null`.
- If it requires custom fields, you will point it to a new handler class (see Step 3).

```php
// app/Filament/ItemTypeManager.php
protected array $types = [
    // ... existing types
    'wine' => null, // or WineType::class if it has custom fields
];
```

### Step 3 (Optional): Create a Custom Form for the New Type

If the new item type requires specific fields, create a new handler class.

1.  **Add new columns to the database:** Create a new migration to add the required `nullable` columns to the `items` table.
    ```bash
    make artisan a="make:migration add_wine_fields_to_items_table --table=items"
    ```
2.  **Make fields fillable:** Add the new column names to the `$fillable` array in the `app/Models/Item.php` model.
3.  **Add field translations:** Add the Spanish labels for the new fields in `lang/es/item.php`.
4.  **Create the handler class:** Create a new class in `app/Filament/ItemTypes/` (e.g., `WineType.php`) that implements the `ItemType` interface and defines the new form components.
    ```php
    <?php
    namespace App\Filament\ItemTypes;
    use Filament\Forms\Components\{Section, TextInput};

    class WineType implements ItemType {
        public static function getFormComponents(): array {
            return [
                Section::make('Detalles del Vino')
                    ->schema([
                        TextInput::make('winery')->label(__('item.field_winery')),
                        TextInput::make('region')->label(__('item.field_region')),
                    ])
            ];
        }
    }
    ```
5.  **Update the `ItemTypeManager`:** Change the value for your new type from `null` to the new class name: `'wine' => WineType::class,`.

After following these steps, run `make artisan a="migrate"` and `make clear-all`. The new item type will be fully integrated into the system.
