.PHONY: help
help: ## Show this help
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_\-\.]+:.*?## / {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

test: ## Runs CS-Fixer, PHPUnit and PHPStan
	docker compose exec php composer test

csfixer: ## Run CS-Fixer
	docker compose exec php composer csfixer

phpunit: ## Run PHPUnit
	docker compose exec php composer phpunit

phpunit-application: ## Run PHPUnit with the Application testsuite
	docker compose exec php composer phpunit -- --testsuite Application

phpunit-integration: ## Run PHPUnit with the Integration testsuite
	docker compose exec php composer phpunit -- --testsuite Integration

phpunit-unit: ## Run PHPUnit with the Unit testsuite
	docker compose exec php composer phpunit -- --testsuite Unit

phpstan: ## Run PHPStan
	docker compose exec php composer phpstan

app-run:
	docker compose exec php php bin/console.php app:run

fixtures:
	docker compose exec php php bin/console.php app:database-fixtures
