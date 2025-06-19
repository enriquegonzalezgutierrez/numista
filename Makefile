# Makefile

# Use bash as the default shell
SHELL := /bin/bash

# Define a default goal, which will be 'help'
.DEFAULT_GOAL := help

# Get variables from Laravel's .env file to use in commands
# This ensures we use the correct DB credentials for db-shell
# The '-' prefix prevents 'make' from failing if the file doesn't exist.
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
	@make key-generate
	@make migrate-fresh
	@echo "--- ✅ Setup complete! The application is ready. ---"

cache-clear: ## Clear all application caches (config, route, view)
	@echo "--- Clearing all application caches ---"
	@make artisan a="config:clear"
	@make artisan a="route:clear"
	@make artisan a="view:clear"
	@echo "--- ✅ Caches cleared successfully! ---"

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
.PHONY: up down stop restart logs setup cache-clear artisan migrate migrate-fresh key-generate composer composer-install bash db-shell help