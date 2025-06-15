# Makefile

# Use bash as the default shell
SHELL := /bin/bash

# Define a default goal, which will be 'help'
.DEFAULT_GOAL := help

# Get variables from Laravel's .env file to use in commands
# This ensures we use the correct DB credentials for db-shell
include .env.example
export $(shell sed 's/=.*//' .env.example)

# Docker commands
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

# Application Setup
setup: ## Run initial setup: install deps, generate key, migrate DB
	@echo "--- Starting project setup ---"
	@make composer-install
	@make key-generate
	@make migrate-fresh
	@echo "--- ✅ Setup complete! The application is ready. ---"

# Artisan commands
artisan: ## Run any artisan command. Ex: make artisan list
	@docker-compose exec app php artisan $(filter-out $@,$(MAKECMDGOALS))

migrate: ## Run database migrations
	@docker-compose exec app php artisan migrate

migrate-fresh: ## Drop all tables and re-run all migrations
	@docker-compose exec app php artisan migrate:fresh

key-generate: ## Generate a new application key
	@docker-compose exec app php artisan key:generate

# Composer commands
composer: ## Run any composer command. Ex: make composer update
	@docker-compose exec app composer $(filter-out $@,$(MAKECMDGOALS))

composer-install: ## Install composer dependencies
	@docker-compose exec app composer install

# Helper commands
bash: ## Get a shell ('sh') inside the app container
	@docker-compose exec app /bin/sh

db-shell: ## Connect to the PostgreSQL database shell
	@docker-compose exec -u postgres db psql -d ${DB_DATABASE} -U ${DB_USERNAME}

# Self-documenting 'help' command
help: ## Show this help message
	@echo "Usage: make [target]"
	@echo ""
	@echo "Available targets:"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-18s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

# Mark targets as not being actual files
.PHONY: up down stop restart logs setup artisan migrate migrate-fresh key-generate composer composer-install bash db-shell help