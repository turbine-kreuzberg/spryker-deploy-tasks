include makefiles/help.mk

setup: ##@setup install dependencies
	make install-git-hooks
	docker compose up -d
.PHONY: setup

cli: ##@development open shell in container
	docker compose exec php bash
.PHONY: test-coverage

tests: ##@development run tests
	docker compose exec php composer tests
.PHONY: tests

test-coverage: ##@development run tests
	docker compose exec php vendor/bin/phpunit --colors=always -c phpunit.xml --coverage-text --coverage-html=tests/_output
.PHONY: test-coverage

phpstan: ##@development run phpstan
	docker compose exec php vendor/bin/codecept build --quiet
	docker compose exec php vendor/bin/phpstan analyse
.PHONY: phpstan

sniff-project: ##@development run code sniffer
	docker compose exec php vendor/bin/phpcs src/ tests/ --standard=./config/codesniffer_ruleset.xml -ps
.PHONY: sniff-project

sniff-fix-project: ##@development run code sniffer
	docker compose exec php vendor/bin/phpcbf src/ tests/ --standard=./config/codesniffer_ruleset.xml -p
.PHONY: sniff-fix-project

install-git-hooks: ##@development install git hooks
	git config core.hooksPath .githooks
.PHONY: install-git-hooks-include

%:
	@:
