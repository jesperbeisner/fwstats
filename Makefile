.PHONY: help
help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

test: ## Run whole testsuite
	-docker compose exec php php -d xdebug.mode=off -d memory_limit=-1 vendor/bin/php-cs-fixer fix --diff --verbose
	-docker compose exec php php -d xdebug.mode=off -d memory_limit=-1 vendor/bin/phpstan analyse
	-docker compose exec php php -d xdebug.mode=off -d memory_limit=-1 vendor/bin/phpunit

csfixer: ## Run CS-Fixer
	docker compose exec php php -d xdebug.mode=off -d memory_limit=-1 vendor/bin/php-cs-fixer fix --diff --verbose

phpstan: ## Run PHPStan
	docker compose exec php php -d xdebug.mode=off -d memory_limit=-1 vendor/bin/phpstan analyse

phpstan-baseline: ## Generate PHPStan Baseline
	docker compose exec php php -d xdebug.mode=off -d memory_limit=-1 vendor/bin/phpstan --allow-empty-baseline --generate-baseline=.phpstan/phpstan-baseline.php

phpunit: ## Run PHPUnit
	docker compose exec php php -d xdebug.mode=off -d memory_limit=-1 vendor/bin/phpunit

app-run:
	docker compose exec php php -d xdebug.mode=off -d memory_limit=-1 bin/console.php app:run

fixtures:
	docker compose exec php php -d xdebug.mode=off -d memory_limit=-1 bin/console.php app:database-fixtures
