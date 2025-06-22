# Makefile

# Use bash as the default shell
SHELL := /bin/bash

# Define a default goal, which will be 'help'
.DEFAULT_GOAL := help

# Get variables from Laravel's .env file to use in commands
-include .env.example
export $(shell sed 's/=.*//' .env.example)

# ==============================================================================
# Docker Commands
# ==============================================================================
up: ## Build and start all services
	@docker-compose up -d --build

down: ## Stop and remove all containers, networks, and volumes
	@docker-compose down

stop: ## Stop all containers without removing them
	@docker-compose stop

restart: ## Restart all containers
	@docker-compose restart

logs: ## View real-time logs for all services
	@docker-compose logs -f

# ==============================================================================
# Application Setup & Maintenance
# ==============================================================================
setup: ## Run initial setup: install deps, generate key, migrate DB
	@echo "--- Starting project setup ---"
	@make composer-install
	@make npm a="install"
	@make npm a="run build"
	@make key-generate
	@make migrate-fresh
	@echo "--- ✅ Setup complete! The application is ready. ---"

cache-clear: ## Clear all application caches (config, route, view)
	@echo "--- Clearing all application caches ---"
	@make artisan a="config:clear"
	@make artisan a="route:clear"
	@make artisan a="view:clear"
	@echo "--- ✅ Caches cleared successfully! ---"

clear-all: ## Clear ALL Laravel caches (config, route, view, compiled classes)
	@echo "--- Clearing all Laravel application caches ---"
	@make artisan a="optimize:clear"
	@echo "--- ✅ All caches cleared successfully! ---"

fix-permissions: ## Fix storage and bootstrap/cache permissions
	@echo "--- Fixing file permissions ---"
	@docker-compose exec -u root app chown -R laravel:www-data storage bootstrap/cache
	@docker-compose exec -u root app find storage -type d -exec chmod 775 {} \;
	@docker-compose exec -u root app find storage -type f -exec chmod 664 {} \;
	@docker-compose exec -u root app find bootstrap/cache -type d -exec chmod 775 {} \;
	@docker-compose exec -u root app find bootstrap/cache -type f -exec chmod 664 {} \;
	@echo "--- ✅ Permissions fixed! ---"

# ==============================================================================
# Artisan Commands (use 'a' variable for arguments)
# ==============================================================================
artisan: ## Run any artisan command. Ex: make artisan a="list"
	@docker-compose exec app php artisan $(a)

migrate: ## Run database migrations
	@make artisan a="migrate"

migrate-fresh: ## Drop all tables and re-run all migrations with seeders
	@make artisan a="migrate:fresh --seed"

key-generate: ## Generate a new application key
	@make artisan a="key:generate"

# ==============================================================================
# Composer Commands (use 'a' variable for arguments)
# ==============================================================================
composer: ## Run any composer command. Ex: make composer a="update"
	@docker-compose exec app composer $(a)

composer-install: ## Install composer dependencies
	@docker-compose exec app composer install

# ==============================================================================
# NPM Commands (use 'a' variable for arguments)
# ==============================================================================
npm: ## Run any npm command. Ex: make npm a="install some-package"
	@docker-compose exec app npm $(a)

# ==============================================================================
# Testing Commands
# ==============================================================================
test: ## Run the entire test suite (Unit & Feature)
	@echo "--- Running all tests ---"
	@make artisan a="test"

test-feature: ## Run only Feature tests
	@make artisan a="test --testsuite=Feature"

test-unit: ## Run only Unit tests
	@make artisan a="test --testsuite=Unit"

test-coverage: ## Run tests and generate a code coverage report
	@make artisan a="test --coverage"

# ==============================================================================
# Helper Commands
# ==============================================================================
bash: ## Get a shell ('sh') inside the app container
	@docker-compose exec app /bin/sh

db-shell: ## Connect to the PostgreSQL database shell
	@docker-compose exec -u postgres db psql -d ${DB_DATABASE} -U ${DB_USERNAME}

# ==============================================================================
# Help Command
# ==============================================================================
help: ## Show this help message
	@echo "Usage: make [target] [a=\"arguments\"]"
	@echo ""
	@echo "Available targets:"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-18s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

# Mark targets as not being actual files
.PHONY: up down stop restart logs setup cache-clear fix-permissions artisan migrate migrate-fresh key-generate composer composer-install npm test test-feature test-unit test-coverage bash db-shell help