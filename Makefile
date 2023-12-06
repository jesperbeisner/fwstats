.PHONY: help
help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

run: ## Run the application
	docker compose up -d

build: ## Build the application
	docker compose up -d --build

reset: ## Reset the whole application
	docker compose down -v --remove-orphans
	docker compose up -d
	docker compose exec php rm -rf vendor
	docker compose exec php composer install
	docker compose exec php rm -f ./storage/logs/laravel.log
	docker compose exec php php -d memory_limit=-1 -d xdebug.mode=off artisan optimize:clear
	docker compose exec php php -d memory_limit=-1 -d xdebug.mode=off artisan clear-compiled
	docker compose exec php php -d memory_limit=-1 -d xdebug.mode=off artisan migrate:fresh --seed

test: ## Runs Pint, PHPStan and PHPUnit
	-docker compose exec php php -d memory_limit=-1 -d xdebug.mode=off vendor/bin/pint --verbose
	-docker compose exec php php -d memory_limit=-1 -d xdebug.mode=off vendor/bin/phpstan analyze
	-docker compose exec php php -d memory_limit=-1 -d xdebug.mode=off vendor/bin/phpunit

pint: ## Run Pint
	docker compose exec php php -d memory_limit=-1 -d xdebug.mode=off vendor/bin/pint --verbose

phpstan: ## Run PHPStan
	docker compose exec php php -d memory_limit=-1 -d xdebug.mode=off vendor/bin/phpstan analyze

phpunit: ## Run PHPUnit
	docker compose exec php php -d memory_limit=-1 -d xdebug.mode=off vendor/bin/phpunit

phpstan-baseline: ## Generate PHPStan Baseline
	docker compose exec php php -d memory_limit=-1 -d xdebug.mode=off vendor/bin/phpstan --allow-empty-baseline --generate-baseline=.phpstan/phpstan-baseline.php

reset-db: ## Reset the database
	docker compose exec php php -d memory_limit=-1 -d xdebug.mode=off artisan migrate:fresh --seed

import: ## Import real freewar data
	docker compose exec php php -d memory_limit=-1 -d xdebug.mode=off artisan fwstats:import
