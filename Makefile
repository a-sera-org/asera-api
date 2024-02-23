# Executables (local)
# Misc
.DEFAULT_GOAL = help
.PHONY        : help to build the asera project

REQUIRED_BINS := symfony composer openssl node yarn php
ENVFILE = .env
LOCAL_ENVFILE = .env.bogosy

include .env.lobe

ifdef DATABASE_URL
$(info DATABASE_URL $(DATABASE_URL))
else
$(error DATABASE_URL undefined)
endif

## —— 🎵 🐳 Asera project Makefile 🐳 🎵 ————————————————
help: ## Outputs this help screen
	@grep -E '(^[a-zA-Z0-9\./_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'

check: ## Check requirements command
    $(foreach bin,$(REQUIRED_BINS),\
        $(if $(shell command -v $(bin) 2> /dev/null),,$(error Please install `$(bin)`)))

## —— Install packages 🐳 —————————————————
install: ## Install required dependencies
	composer install
	php bin/console c:c
	yarn install
	yarn encore build


## —— Configure project 🐳 ——————————————————
config: ## Config/MAJ DB and keys
	php bin/console doctrine:database:create --if-not-exists
	php bin/console doctrine:schema:update -f
	php bin/console lexik:jwt:generate-keypair --overwrite