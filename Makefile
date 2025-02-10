.PHONY: help

help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

run: ## Run application
	docker compose up

bash: ## Open bash in container
	docker compose exec php bash

reset-db: ## Reset database
	docker compose exec php php -d memory_limit=-1 artisan migrate:fresh --seed

migrate: ## Migrate the database
	docker compose exec php php artisan migrate

watch: ## Watch assets
	docker compose exec php npm run dev

test: ## Run whole testsuite
	-docker compose exec php php -d memory_limit=-1 vendor/bin/php-cs-fixer fix --diff --verbose
	-docker compose exec php php -d memory_limit=-1 vendor/bin/phpstan analyse
	-docker compose exec php php -d memory_limit=-1 vendor/bin/phpunit --testsuite Unit,Feature

csfixer: ## Run PHP-CS-Fixer
	docker compose exec php php -d memory_limit=-1 vendor/bin/php-cs-fixer fix --diff --verbose

phpstan: ## Run PHPStan
	docker compose exec php php -d memory_limit=-1 vendor/bin/phpstan

phpstan-debug: ## Run PHPStan in debug mode
	docker compose exec php php -d memory_limit=-1 vendor/bin/phpstan --debug

phpstan-baseline: ## Generate PHPStan Baseline
	docker compose exec php php -d memory_limit=-1 vendor/bin/phpstan --allow-empty-baseline --generate-baseline=.phpstan/phpstan-baseline.php

phpunit: ## Run PHPUnit
	docker compose exec php php -d memory_limit=-1 vendor/bin/phpunit --testsuite Unit,Feature

composer-install: ## Install composer packages
	docker compose run --rm --entrypoint="composer install" --no-deps php

npm-install: ## Install npm packages
	docker compose run --rm --entrypoint="npm install" --no-deps php
