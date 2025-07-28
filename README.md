# Numista-App: Numismatics & Collectibles Project

Numista-App is a modern, multi-tenant web application for managing numismatic and other collectible collections. It is built on the TALL stack and containerized with Docker, featuring a powerful admin panel and a fully responsive public marketplace.

---

## ✨ Visual Showcase

| Marketplace (Desktop) | Item Details (Desktop) |
| :---: | :---: |
| ![Marketplace Screenshot](docs/screenshots/marketplace.png) | ![Item Details Screenshot](docs/screenshots/item-details.png) |

| Cart Page (Desktop) | Mobile View |
| :---: | :---: |
| ![Cart Screenshot](docs/screenshots/cart.png) | ![Mobile View Screenshot](docs/screenshots/mobile-view.png) |

---

## 🚀 Key Features

### Admin Panel (Filament)
- **Multi-Tenant Architecture:** Each user manages their own isolated collection, with the ability to be part of multiple tenants.
- **Dynamic EAV System:** A flexible Entity-Attribute-Value model allows admins to define custom attributes (e.g., "Year", "Grade", "Composition") for different item types without code changes.
- **Dynamic Forms:** Item forms in Filament are generated dynamically based on the selected "Item Type," displaying only relevant attributes.
- **Complete CRUD Management:** Full create, read, update, and delete functionality for Items, Categories, Collections, Orders, and Attributes.
- **Interactive Dashboard:** A real-time dashboard with widgets for key statistics, charts, and recently added items.
- **Secure Image Management:** Easy image uploads with reordering, stored securely in tenant-specific private directories.

### Public Marketplace
- **Fully Responsive Design:** A modern interface built with Tailwind CSS that looks great on all devices, from mobile phones to desktops.
- **Advanced Filtering:** Users can filter items by search term, category, and any custom, filterable attribute.
- **Interactive Shopping Cart:** A seamless, session-based shopping cart with "quick add" functionality directly from the item grid.
- **Secure Checkout:** A clean checkout process for authenticated users to purchase items.
- **User Accounts & Order History:** Customers can register, log in, and view their complete order history.
- **Contact Seller Functionality:** An integrated contact form on the item page that sends a queued email to the item's owner.

---

## 🛠️ Tech Stack

- **Backend**: Laravel 12
- **Frontend**: Livewire 3 & Alpine.js
- **UI/Styling**: Tailwind CSS
- **Admin Panel**: Filament 3
- **Database**: PostgreSQL 16
- **PHP**: 8.2
- **Testing**: Pest (PHPUnit) with automated CI via GitHub Actions.
- **Environment**: Docker & Docker Compose

---

## 🐳 Local Development Setup

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

## 🧰 Useful Make Commands

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

The project follows a Domain-Driven Design (DDD) inspired structure. The core feature is a flexible **Entity-Attribute-Value (EAV)** model for managing collectible items.

-   **Items (`items` table):** Store only common data (name, description, price, etc.).
-   **Attributes (`attributes` table):** A tenant can define custom attributes for their collection (e.g., "Year", "Publisher"). This is managed via the **Settings > Attributes** section in the admin panel.
-   **Dynamic Forms:** The "Create/Edit Item" form in Filament is fully dynamic. When a user selects an "Item Type", the form automatically displays the attributes that have been linked to that type.

This architecture allows tenants to customize the data they collect for each type of item without requiring changes to the database schema.
