# =============================================================================
# FAN READ - SYMFONY 8 PROJECT - MAKEFILE
# =============================================================================
# Description: Makefile pour simplifier les commandes du projet Symfony 8
# Auteur: Papoel
# Date: 02/05/2026
# =============================================================================

# Variables de couleurs pour l'affichage
GREEN := \033[0;32m
YELLOW := \033[0;33m
CYAN := \033[0;36m
BLUE := \033[0;34m
MAGENTA := \033[0;35m
RED := \033[0;31m
BOLD := \033[1m
NC := \033[0m

# Variable du projet
PROJECT_NAME := FAN_READ

# Variables Symfony
SYMFONY_BIN := symfony
CONSOLE := php bin/console
CONSOLE_MEM := php -d memory_limit=256M bin/console
PHPUNIT := php bin/phpunit
COMPOSER := composer

# Variables Docker
DOCKER_COMPOSE := docker compose
DOCKER_EXEC := docker compose exec
DOCKER_RUN := docker run

# Variables jakzal/phpqa pour l'analyse de qualité
PHP_VERSION := 8.5
PHPQA := jakzal/phpqa:php$(PHP_VERSION)
PHPQA_RUN := $(DOCKER_RUN) --init --rm -v $(PWD):/project -w /project $(PHPQA)

# Variables de base de données
DB_NAME := db_$(PROJECT_NAME)
DB_HOST := database
DB_PORT := 3306
DB_USER := root

# Variables du serveur
SERVER_HOST := localhost
SERVER_PORT := 8000

# Déclaration des targets comme PHONY
.PHONY: help about check-updates install start stop reset \
        git-stats show-wip \
        composer-install composer-update composer-validate composer-audit composer-require composer-remove \
        controller security form crud twig-component entity \
        docker-start docker-up docker-down docker-restart docker-logs docker-ps docker-env docker-config \
        docker-build docker-clean docker-shell \
        db-create db-test db-drop db-migration db-migrate db-diff db-rollback db-validate db-fixtures db-reset db-backup db-restore \
        cache-clear cache-warmup cache-clear-prod cc clean-cache-all \
        assets-install assets-compile assets-watch \
        test test-coverage coverage update-coverage-badge \
        lint lint-yaml lint-twig lint-php lint-container fix-php \
        phpstan phpstan-level phpstan-file phpstan-baseline  phpmd phpcpd phpcs phpcbf phpmetrics phpinsights qa qa-full \
        serve console routes router-match debug-container debug-events \
        jwt-install jwt-generate-keys jwt-keys-permissions jwt-check-config jwt-test-token jwt-decode jwt-setup jwt-clean \
        security-check security-audit before-commit cs-fix cs-check ci ci-full

# Target par défaut
.DEFAULT_GOAL := help

# =============================================================================
# AIDE ET DOCUMENTATION
# =============================================================================

help: ## 🚀 Affiche cette aide
	@echo ""
	@echo "$(BOLD)$(CYAN)╔═══════════════════════════════════════════════════════════╗$(NC)"
	@echo "$(BOLD)$(CYAN)║                                                           ║$(NC)"
	@echo "$(BOLD)$(CYAN)║           SYMFONY 8 - $(PROJECT_NAME) MAKEFILE      ║$(NC)"
	@echo "$(BOLD)$(CYAN)║                                                           ║$(NC)"
	@echo "$(BOLD)$(CYAN)╚═══════════════════════════════════════════════════════════╝$(NC)"
	@echo ""
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | \
	awk 'BEGIN {FS = ":.*?## "}; \
	/^##/ {printf "\n$(YELLOW)%s$(NC)\n", substr($$0, 4)}; \
	!/^##/ {printf "  $(GREEN)%-25s$(NC) %s\n", $$1, $$2}'
	@echo ""
	@echo "$(CYAN)💡 Astuce:$(NC) Utilisez $(BOLD)make <target>$(NC) pour exécuter une commande"
	@echo ""

# =============================================================================
# GESTION DU PROJET
# =============================================================================

## —— 🎯 Projet ————————————————————————————————————————————————————————————————
about:
	@$(CONSOLE) about
	@echo ""
	@$(MAKE) check-updates

check-updates:
	@echo "=== Mises à jour disponibles ==="
	@composer outdated "symfony/*"

install: composer-install db-create db-migrate assets-install ## 📦 Installation complète du projet
	@echo "$(GREEN)✅ Installation terminée !$(NC)"

start: docker-start serve ## 🚀 Démarre le projet (Docker + SGBD + serveur Symfony)
	@echo ""
	@echo "$(BOLD)$(GREEN)╔═══════════════════════════════════════════════════════════╗$(NC)"
	@echo "$(BOLD)$(GREEN)║                  🚀 PROJET DÉMARRÉ !                      ║$(NC)"
	@echo "$(BOLD)$(GREEN)╚═══════════════════════════════════════════════════════════╝$(NC)"
	@echo ""
	@echo "$(CYAN)✅ Docker démarré$(NC)"
	@echo "$(CYAN)✅ Serveur Symfony démarré$(NC)"
	@echo ""
	@echo "$(YELLOW)💡 Utilisez 'make stop' pour tout arrêter$(NC)"
	@echo ""

stop: docker-down ## 🛑 Arrête tous les services (Docker + Symfony)
	@echo ""
	@echo "$(YELLOW)🛑 Arrêt du serveur Symfony...$(NC)"
	@$(SYMFONY_BIN) server:stop 2>/dev/null || true
	@echo "$(GREEN)✅ Tous les services sont arrêtés$(NC)"
	@echo ""

reset: stop cache-clear ## 🔄 Reset du projet (cache, arrêt services)
	@echo "$(YELLOW)🔄 Projet réinitialisé$(NC)"

git-stats: ## 📊 Affiche les statistiques Git du projet
	@echo ""; \
	echo "$(BOLD)$(CYAN)╔═══════════════════════════════════════════════════════════╗$(NC)"; \
	echo "$(BOLD)$(CYAN)║              📊 STATISTIQUES GIT DU PROJET                ║$(NC)"; \
	echo "$(BOLD)$(CYAN)╚═══════════════════════════════════════════════════════════╝$(NC)"; \
	echo ""; \
	FIRST_COMMIT=$$(git log --reverse --format='%ai' 2>/dev/null | head -n1 | cut -d' ' -f1); \
	LAST_COMMIT=$$(git log -1 --format='%ai' 2>/dev/null | cut -d' ' -f1); \
	COMMIT_COUNT=$$(git rev-list --count HEAD 2>/dev/null); \
	CONTRIBUTORS=$$(git shortlog -sn --all 2>/dev/null | wc -l | tr -d ' '); \
	BRANCHES=$$(git branch -a 2>/dev/null | wc -l | tr -d ' '); \
	FILES=$$(git ls-files 2>/dev/null | wc -l | tr -d ' '); \
	echo "$(YELLOW)📅 Premier commit:$(NC)      $$FIRST_COMMIT"; \
	echo "$(YELLOW)📅 Dernier commit:$(NC)      $$LAST_COMMIT"; \
	echo "$(YELLOW)📝 Nombre de commits:$(NC)   $$COMMIT_COUNT"; \
	echo "$(YELLOW)👥 Contributeurs:$(NC)       $$CONTRIBUTORS"; \
	echo "$(YELLOW)🌿 Branches:$(NC)            $$BRANCHES"; \
	echo "$(YELLOW)📄 Fichiers suivis:$(NC)     $$FILES"; \
	echo ""; \
	echo "$(CYAN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(NC)"; \
	echo "$(BOLD)$(CYAN)👤 Top 5 des contributeurs:$(NC)"; \
	echo "$(CYAN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(NC)"; \
	echo ""; \
	git shortlog -sn --all 2>/dev/null | head -n 5 | while read count author; do \
		echo "  $(GREEN)$$count commits$(NC) - $$author"; \
	done; \
	echo ""; \
	echo "$(CYAN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(NC)"; \
	echo "$(BOLD)$(CYAN)📈 Activité récente (7 derniers jours):$(NC)"; \
	echo "$(CYAN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(NC)"; \
	echo ""; \
	git log --since="7 days ago" --format="%ad - %s" --date=short 2>/dev/null | head -n 10 || echo "  $(YELLOW)Aucun commit récent$(NC)"; \
	echo ""

show-wip: ## 📝 Affiche les commits de la branche courante (utile pour les PR)
	@echo ""; \
	echo "$(BOLD)$(CYAN)╔═══════════════════════════════════════════════════════════╗$(NC)"; \
	echo "$(BOLD)$(CYAN)║              📝 TRAVAIL EN COURS (WIP)                   ║$(NC)"; \
	echo "$(BOLD)$(CYAN)╚═══════════════════════════════════════════════════════════╝$(NC)"; \
	echo ""; \
	BRANCH_NAME=$$(git rev-parse --abbrev-ref HEAD 2>/dev/null); \
	if [ "$$BRANCH_NAME" = "main" ] || [ "$$BRANCH_NAME" = "master" ]; then \
		echo "$(YELLOW)⚠️  Vous êtes sur la branche principale ($$BRANCH_NAME)$(NC)"; \
		echo "$(CYAN)💡 Cette commande est utile pour voir les changements sur une branche de feature$(NC)"; \
		echo ""; \
		exit 0; \
	fi; \
	echo "$(GREEN)🌿 Branche actuelle:$(NC) $$BRANCH_NAME"; \
	echo ""; \
	COMMIT_COUNT=$$(git rev-list --count main..$$BRANCH_NAME 2>/dev/null || echo 0); \
	if [ "$$COMMIT_COUNT" = "0" ]; then \
		echo "$(YELLOW)⚠️  Aucun commit sur cette branche par rapport à main$(NC)"; \
		echo ""; \
		exit 0; \
	fi; \
	echo "$(CYAN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(NC)"; \
	echo "$(BOLD)$(CYAN)📊 Commits ($$COMMIT_COUNT) et fichiers modifiés:$(NC)"; \
	echo "$(CYAN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(NC)"; \
	echo ""; \
	git log main..$$BRANCH_NAME --name-status --pretty=format:"$(YELLOW)Commit %h$(NC) - %s%n$(GREEN)Author:$(NC) %an%n$(GREEN)Date:$(NC) %ad%n" --date=short 2>/dev/null; \
	echo ""; \
	echo "$(CYAN)━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━$(NC)"; \
	echo "$(BOLD)$(GREEN)💡 Astuce:$(NC) Copiez ces informations pour votre Pull Request"; \
	echo ""

# =============================================================================
# GESTION COMPOSER
# =============================================================================

## —— 📦 Composer ——————————————————————————————————————————————————————————————

composer-install: ## 📥 Installation des dépendances PHP
	$(COMPOSER) install --no-interaction --prefer-dist --optimize-autoloader
	@echo "$(GREEN)✅ Dépendances installées$(NC)"

composer-update: ## 🔄 Mise à jour des dépendances PHP
	$(COMPOSER) update --no-interaction
	@echo "$(GREEN)✅ Dépendances mises à jour$(NC)"

composer-validate: ## ✅ Valide le fichier composer.json
	$(COMPOSER) validate --strict

composer-audit: ## 🔒 Vérifie les vulnérabilités de sécurité
	$(COMPOSER) audit

composer-require: ## ➕ Ajoute une dépendance (ex: make composer-require package=vendor/package)
	$(COMPOSER) require $(package)

composer-remove: ## ➖ Supprime une dépendance (ex: make composer-remove package=vendor/package)
	$(COMPOSER) remove $(package)

# =============================================================================
# COMMANDE SYMFONY
# =============================================================================

## —— 🎸 Symfony ———————————————————————————————————————————————————————————————

controller: ## 🎯 Crée un nouveau contrôleur (ex: make controller name=HomeController)
	@echo "$(BOLD)$(CYAN)🔧 Création du contrôleur...$(NC)"
	@echo "$(CYAN)💡 Commande Symfony: bin/console make:controller $(name)$(NC)"
	@echo ""
	$(CONSOLE) make:controller $(name)

security: ## 🎯 Crée un système d'authentification (ex: make auth)
	@echo "$(BOLD)$(CYAN)🔐 Création du système d'authentification avec formulaire...$(NC)"
	@echo "$(CYAN)💡 Commande Symfony: bin/console make:security:form-login$(NC)"
	@echo ""
	$(CONSOLE) make:security:form-login

form: ## 🎯 Crée un formulaire (ex: make form name=MyForm)
	@echo "$(BOLD)$(CYAN)📝 Création du formulaire...$(NC)"
	@echo "$(CYAN)💡 Commande Symfony: bin/console make:form $(name)$(NC)"
	@echo ""
	$(CONSOLE) make:form $(name)

crud: ## 🎯 Crée le CRUD d'une entité (ex: make crud entity=Article)
	@echo "$(BOLD)$(CYAN)📝 Création du CRUD...$(NC)"
	@echo "$(CYAN)💡 Commande Symfony: bin/console make:crud $(entity)$(NC)"
	@echo ""
	$(CONSOLE) make:crud $(entity)

twig-component: ## 🎯 Crée un nouveau composant Twig (ex: make twig-component name=Navbar)
	@echo "$(BOLD)$(CYAN)🔧 Création du composant Twig...$(NC)"
	@echo "$(CYAN)💡 Commande Symfony: bin/console make:twig-component $(name)$(NC)"
	@echo ""
	$(CONSOLE) make:twig-component $(name)

entity: ## 🎯 Crée une entité (ex: make entity name=Article)
	@echo "$(BOLD)$(CYAN)📝 Création de l'entité...$(NC)"
	@echo "$(CYAN)💡 Commande Symfony: bin/console make:entity $(name)$(NC)"
	@echo ""
	$(CONSOLE) make:entity $(name)

# =============================================================================
# GESTION DOCKER
# =============================================================================

## —— 🐳 Docker ————————————————————————————————————————————————————————————————

docker-start: ## 🚀 Démarre les conteneurs (MariaDB + phpMyAdmin + Maildev)
	@if [ -f compose.yaml ]; then \
		$(DOCKER_COMPOSE) up -d; \
		echo "$(GREEN)✅ Services démarrés$(NC)"; \
		echo "$(CYAN)📊 phpMyAdmin : http://localhost:8085$(NC)"; \
		echo "$(CYAN)📧 Maildev    : http://localhost:8081 (SMTP localhost:1025)$(NC)"; \
	else \
		echo "$(YELLOW)⚠ Aucun compose.yaml trouvé : mode sans Docker activé.$(NC)"; \
		echo "$(YELLOW)⚠ SQLite sera utilisé automatiquement.$(NC)"; \
	fi

docker-up: docker-start ## ⬆️  Alias de docker-start

docker-down: ## ⬇️  Arrête tous les conteneurs Docker
	$(DOCKER_COMPOSE) down --remove-orphans
	@echo "$(RED)🛑 Tous les conteneurs Docker arrêtés$(NC)"

docker-restart: docker-down docker-start ## 🔄 Redémarre les conteneurs Docker

docker-logs: ## 📋 Affiche les logs Docker
	$(DOCKER_COMPOSE) logs -f

docker-ps: ## 📊 Liste les conteneurs actifs
	$(DOCKER_COMPOSE) ps

docker-env: ## 🔍 Affiche les variables d'environnement d'un service (ex: make docker-env service=database)
	@if [ -z "$(service)" ]; then \
		echo "$(RED)❌ Spécifiez un service avec service=nom_du_service$(NC)"; \
		echo "$(YELLOW)Exemple: make docker-env service=database$(NC)"; \
		exit 1; \
	fi
	@echo "$(CYAN)Variables d'environnement du service $(BOLD)$(service)$(NC)$(CYAN):$(NC)"
	@echo ""
	@$(DOCKER_COMPOSE) exec $(service) env | sort || \
	echo "$(RED)❌ Le service $(service) n'est pas en cours d'exécution$(NC)"

docker-config: ## 📋 Affiche la configuration Docker Compose finale avec les valeurs substituées
	@echo "$(CYAN)Configuration finale de Docker Compose :$(NC)"
	@echo ""
	@$(DOCKER_COMPOSE) config

docker-build: ## 🔨 Reconstruit les images Docker
	$(DOCKER_COMPOSE) build --no-cache
	@echo "$(GREEN)✅ Images Docker reconstruites$(NC)"

docker-clean: ## 🧹 Supprime tous les conteneurs, volumes et images
	$(DOCKER_COMPOSE) down -v --remove-orphans
	@echo "$(GREEN)✅ Nettoyage Docker complet terminé$(NC)"

docker-shell: ## 🐚 Ouvre un shell dans le conteneur de base de données
	$(DOCKER_EXEC) database /bin/sh

# =============================================================================
# GESTION BASE DE DONNÉES
# =============================================================================

## —— 💾 Base de données ——————————————————————————————————————————————————————

db-create: ## ➕ Crée la base de données
	@mkdir -p var/database
	@$(CONSOLE) doctrine:database:create --if-not-exists 2>/dev/null || true
	@echo "$(GREEN)✅ Base de données créée$(NC)"
	@$(CONSOLE) doctrine:schema:update --force
	@echo "$(GREEN)✅ Schéma de la base de données mis à jour$(NC)"

db-test: ## ➕ Crée la base de données test
	@$(CONSOLE) doctrine:database:create --if-not-exists --env=test 2>/dev/null || true
	@echo "$(GREEN)✅ Base de données test créée$(NC)"
	@$(CONSOLE) doctrine:schema:update --force --env=test
	@echo "$(GREEN)✅ Schéma de la base de données test mis à jour$(NC)"

db-drop: ## ➖ Supprime la base de données
	$(CONSOLE) doctrine:database:drop --force --if-exists
	@echo "$(RED)🗑️  Base de données supprimée$(NC)"

db-migration: ## 📝 Génère une nouvelle migration en demandant l'env (dev/test/prod)
	$(CONSOLE) make:migration
	@echo "$(GREEN)✅ Migration générée$(NC)"

db-migrate: ## 🔄 Exécute les migrations
	$(CONSOLE) doctrine:migrations:migrate --no-interaction
	@echo "$(GREEN)✅ Migrations exécutées$(NC)"

db-diff: ## 📝 Génère une nouvelle migration
	$(CONSOLE) doctrine:migrations:diff
	@echo "$(GREEN)✅ Migration générée$(NC)"

db-rollback: ## ⏪ Annule la dernière migration
	$(CONSOLE) doctrine:migrations:migrate prev --no-interaction
	@echo "$(YELLOW)⏪ Migration annulée$(NC)"

db-validate: ## ✅ Valide le mapping Doctrine
	$(CONSOLE) doctrine:schema:validate

db-fixtures: ## 🌱 Charge les fixtures
	$(CONSOLE) doctrine:fixtures:load --no-interaction
	@echo "$(GREEN)✅ Fixtures chargées$(NC)"

db-reset: db-drop db-create db-migrate ## 🔄 Reset complet de la base de données
	@echo "$(GREEN)💾 Base de données réinitialisée$(NC)"

db-backup: ## 💾 Sauvegarde la base de données
	@mkdir -p var/backups
	@echo "$(CYAN)💾 Sauvegarde de la base de données...$(NC)"
	$(DOCKER_COMPOSE) exec -T database mysqldump -u $(DB_USER) $(DB_NAME) > var/backups/db-backup-$$(date +%Y%m%d-%H%M%S).sql
	@echo "$(GREEN)✅ Sauvegarde terminée$(NC)"

db-restore: ## 🔄 Restaure la base de données (ex: make db-restore file=var/backups/db-backup.sql)
	@if [ -z "$(file)" ]; then \
		echo "$(RED)❌ Veuillez spécifier un fichier : make db-restore file=var/backups/db-backup.sql$(NC)"; \
		exit 1; \
	fi
	@echo "$(CYAN)🔄 Restauration de la base de données...$(NC)"
	$(DOCKER_COMPOSE) exec -T database mysql -u $(DB_USER) $(DB_NAME) < $(file)
	@echo "$(GREEN)✅ Restauration terminée$(NC)"

# =============================================================================
# GESTION DU CACHE
# =============================================================================

## —— 🗄️  Cache ————————————————————————————————————————————————————————————————

cache-clear: ## 🧹 Vide le cache
	$(CONSOLE_MEM) cache:clear
	@echo "$(GREEN)✅ Cache vidé$(NC)"

cache-warmup: ## 🔥 Réchauffe le cache
	$(CONSOLE_MEM) cache:warmup
	@echo "$(GREEN)✅ Cache réchauffé$(NC)"

cache-clear-prod: ## 🧹 Vide le cache de production
	$(CONSOLE) cache:clear --env=prod
	$(CONSOLE) cache:warmup --env=prod
	@echo "$(GREEN)✅ Cache de production vidé$(NC)"

cc: cache-clear cache-warmup ## 🧹 Alias pour vider et réchauffer le cache
	@echo "$(GREEN)✅ Cache vidé et réchauffé$(NC)"

clean-cache-all: ## 🧹 Supprime tous les fichiers de cache
	rm -rf var/cache/*
	@echo "$(GREEN)✅ Tous les fichiers de cache supprimés$(NC)"

# =============================================================================
# GESTION DES ASSETS
# =============================================================================

## —— 🎨 Assets ————————————————————————————————————————————————————————————————

assets-install: ## 📥 Installe les assets
	$(CONSOLE) importmap:install
	$(CONSOLE) asset:install
	@echo "$(GREEN)✅ Assets installés$(NC)"

assets-compile: ## 🔨 Compile les assets
	$(CONSOLE) asset:install
	@echo "$(GREEN)✅ Assets compilés$(NC)"

assets-watch: ## 👀 Compile les assets en continu (AssetMapper n'a pas de watch natif, relance importmap:install)
	@echo "$(CYAN)💡 Lance l'installation des assets :$(NC)"
	$(CONSOLE) importmap:install

# =============================================================================
# TESTS
# =============================================================================

## —— 🧪 Tests ——————————————————————————————————————————————————————————————————

test: ## 🧪 Lance tous les tests
	$(PHPUNIT)

test-coverage: ## 📊 Lance les tests avec couverture de code
	XDEBUG_MODE=coverage $(PHPUNIT) --coverage-html var/coverage
	@echo "$(GREEN)✅ Couverture générée dans var/coverage$(NC)"

## —— 📊 Couverture de code ——————————————————————————————————————————————————
coverage: ## 📊 Génère le rapport de couverture et met à jour le badge
	@echo "$(BOLD)$(CYAN)📊 Génération du rapport de couverture...$(NC)"
	@if php -m | grep -q xdebug; then \
        XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-clover=clover.xml --coverage-text | tee coverage.txt; \
        $(MAKE) update-coverage-badge; \
    else \
        echo "$(RED)❌ Xdebug n'est pas installé. Installez-le avec : brew install php@8.4-xdebug$(NC)"; \
        exit 1; \
    fi

update-coverage-badge: ## 🏅 Met à jour le badge de couverture dans le README
	@echo "$(BOLD)$(YELLOW)🏅 Mise à jour du badge de couverture...$(NC)"
	@if [ -f clover.xml ]; then \
        COVERAGE=$$(php -r '\
            $$xml = simplexml_load_file("clover.xml"); \
            $$metrics = $$xml->project->metrics; \
            $$covered = (int)$$metrics["coveredstatements"]; \
            $$total = (int)$$metrics["statements"]; \
            if ($$total > 0) { \
                echo round(($$covered / $$total) * 100); \
            } else { \
                echo 0; \
            }'); \
        if [ $$COVERAGE -ge 80 ]; then COLOR="brightgreen"; \
        elif [ $$COVERAGE -ge 60 ]; then COLOR="yellow"; \
        else COLOR="red"; fi; \
        sed -i.bak "s|coverage-[0-9]*%25-[a-z]*|coverage-$${COVERAGE}%25-$${COLOR}|" README.md && rm README.md.bak; \
        echo "$(GREEN)✅ Badge mis à jour : $${COVERAGE}% (couleur: $${COLOR})$(NC)"; \
    else \
        echo "$(RED)❌ Fichier clover.xml introuvable$(NC)"; \
    fi

# =============================================================================
# QUALITÉ DE CODE
# =============================================================================

## —— 🔍 Qualité de code ——————————————————————————————————————————————————————

lint: lint-yaml lint-twig lint-container ## ✅ Vérifie la syntaxe de tous les fichiers
	@echo "$(GREEN)✅ Vérification de syntaxe terminée$(NC)"

lint-yaml: ## ✅ Vérifie la syntaxe YAML
	$(CONSOLE) lint:yaml config

lint-twig: ## ✅ Vérifie la syntaxe Twig
	$(CONSOLE) lint:twig templates

lint-php: ## ✅ Vérifie la syntaxe PHP
	@if [ -f vendor/bin/php-cs-fixer ]; then \
		vendor/bin/php-cs-fixer fix --dry-run --diff --verbose; \
	else \
		echo "$(YELLOW)⚠️  php-cs-fixer n'est pas installé$(NC)"; \
	fi

lint-container: ## ✅ Vérifie le container
	$(CONSOLE) lint:container

fix-php: ## 🔧 Corrige automatiquement le code PHP
	@if [ -f vendor/bin/php-cs-fixer ]; then \
		vendor/bin/php-cs-fixer fix; \
		echo "$(GREEN)✅ Code PHP corrigé$(NC)"; \
	else \
		echo "$(YELLOW)⚠️  php-cs-fixer n'est pas installé$(NC)"; \
		echo "$(CYAN)💡 Installation : composer require --dev friendsofphp/php-cs-fixer$(NC)"; \
	fi

cs-fix: fix-php ## 🔧 Alias pour fix-php (compatibilité CI)

cs-check: lint-php ## ✅ Alias pour lint-php (compatibilité CI)

ci: composer-validate cs-check lint phpstan test ## 🚀 Lance toute la CI localement (rapide)
	@echo ""
	@echo "$(BOLD)$(GREEN)✅ CI LOCALE RÉUSSIE !$(NC)"
	@echo ""

ci-full: before-commit test ## 🚀 Lance toute la CI complète (identique à GitHub Actions)
	@echo ""
	@echo "$(BOLD)$(GREEN)✅ CI complète terminée avec succès !$(NC)"
	@$(MAKE) update-coverage-badge

# =============================================================================
# ANALYSE DE QUALITÉ (PHPQA)
# =============================================================================

## —— 🔬 Analyse de qualité (jakzal/phpqa) ————————————————————————————————————

phpstan: ## 🔍 Analyse statique du code avec PHPStan (niveau max)
	@echo "$(CYAN)🔍 Analyse statique du code avec PHPStan (niveau max)...$(NC)"
	@$(PHPQA_RUN) phpstan analyse -l max src
	@echo "$(GREEN)✅ Analyse PHPStan terminée$(NC)"

phpstan-level: ## 🔍 Analyse statique du code avec PHPStan (niveau personnalisé) : make phpstan-level level=7
	@if [ -z "$(level)" ]; then \
		echo "$(RED)❌ Veuillez spécifier un niveau : make phpstan-level level=7$(NC)"; \
		echo "$(YELLOW)Exemple: make phpstan-level level=7$(NC)"; \
		exit 1; \
	fi
	@echo "$(CYAN)🔍 Analyse statique du code avec PHPStan (niveau $(level))...$(NC)"
	@$(PHPQA_RUN) phpstan analyse -l $(level) src
	@echo "$(GREEN)✅ Analyse PHPStan terminée$(NC)"

phpstan-file: ## 🔍 Analyse PHPStan sur un fichier : make phpstan-file file=src/Classe/Cart.php
	@if [ -z "$(file)" ]; then \
		echo "$(RED)❌ Veuillez spécifier un fichier : make phpstan-file file=chemin/vers/fichier.php$(NC)"; \
		echo "$(YELLOW)Exemple: make phpstan-file file=src/Classe/Cart.php$(NC)"; \
		exit 1; \
	fi
	@echo "$(CYAN)🔍 Analyse statique du fichier $(file) avec PHPStan (niveau max)...$(NC)"
	@if [ -f phpstan.neon ]; then \
		$(PHPQA_RUN) phpstan analyse -l max -c phpstan.neon $(file); \
	else \
		echo "parameters:" > var/.phpstan-temp.neon; \
		$(PHPQA_RUN) phpstan analyse -l max -c var/.phpstan-temp.neon $(file); \
		rm -f var/.phpstan-temp.neon; \
	fi
	@echo "$(GREEN)✅ Analyse PHPStan terminée$(NC)"

phpstan-baseline: ## 📊 Génère la baseline PHPStan
	@echo "$(CYAN)📊 Génération de la baseline PHPStan...$(NC)"
	@$(PHPQA_RUN) phpstan analyse -l max src --generate-baseline
	@echo "$(GREEN)✅ Baseline générée dans phpstan-baseline.neon$(NC)"

phpmd: ## 🔎 Détection de code smell avec PHP Mess Detector
	@echo "$(CYAN)🔎 Détection de code smell avec PHPMD...$(NC)"
	@$(PHPQA_RUN) phpmd src text phpmd.xml 2>&1 | grep -v "Deprecated:" || true
	@echo "$(GREEN)✅ Analyse PHPMD terminée$(NC)"

phpcpd: ## 📋 Détection de code dupliqué avec PHP Copy/Paste Detector
	@echo "$(CYAN)📋 Détection de code dupliqué...$(NC)"
	@$(PHPQA_RUN) phpcpd src 2>&1 | grep -v "Deprecated:"
	@echo "$(GREEN)✅ Analyse PHPCPD terminée$(NC)"

phpcs: ## 📐 Vérification des standards de code avec PHP CodeSniffer
	@echo "$(CYAN)📐 Vérification des standards de code...$(NC)"
	@$(PHPQA_RUN) phpcs --standard=PSR12 src
	@echo "$(GREEN)✅ Analyse PHPCS terminée$(NC)"

phpcbf: ## 🔧 Correction automatique des standards de code avec PHP Code Beautifier
	@echo "$(CYAN)🔧 Correction automatique des standards de code...$(NC)"
	@$(PHPQA_RUN) phpcbf --standard=PSR12 src || true
	@echo "$(GREEN)✅ Corrections PHPCBF appliquées$(NC)"

phpmetrics: ## 📊 Génère un rapport de métriques avec PhpMetrics
	@echo "$(CYAN)📊 Génération du rapport PhpMetrics...$(NC)"
	@mkdir -p var/phpmetrics
	@$(PHPQA_RUN) phpmetrics --report-html=var/phpmetrics src 2>&1 | grep -v "Deprecated:" | grep -v "Warning: copy"
	@echo "$(GREEN)✅ Rapport généré dans var/phpmetrics/index.html$(NC)"

phpinsights: ## 💡 Analyse complète avec PHP Insights
	@echo "$(CYAN)💡 Analyse complète avec PHP Insights...$(NC)"
	@$(PHPQA_RUN) phpinsights analyse src --no-interaction --min-quality=80 --min-complexity=80 --min-architecture=80 --min-style=80
	@echo "$(GREEN)✅ Analyse PHP Insights terminée$(NC)"

qa: phpstan phpmd phpcpd ## 🎯 Analyse de qualité rapide (PHPStan + PHPMD + PHPCPD)
	@echo "$(GREEN)✅ Analyse de qualité rapide terminée$(NC)"

qa-full: phpstan phpmd phpcpd phpcbf phpmetrics ## 🚀 Analyse de qualité complète
	@echo ""
	@echo "$(BOLD)$(CYAN)╔═══════════════════════════════════════════════════════════╗$(NC)"
	@echo "$(BOLD)$(CYAN)║                                                           ║$(NC)"
	@echo "$(BOLD)$(CYAN)║          ✅ ANALYSE DE QUALITÉ COMPLÈTE TERMINÉE          ║$(NC)"
	@echo "$(BOLD)$(CYAN)║                                                           ║$(NC)"
	@echo "$(BOLD)$(CYAN)╚═══════════════════════════════════════════════════════════╝$(NC)"
	@echo ""
	@echo "$(GREEN)📊 Rapport PhpMetrics disponible :$(NC) var/phpmetrics/index.html"
	@echo ""

# =============================================================================
# DÉVELOPPEMENT
# =============================================================================

## —— 💻 Développement ————————————————————————————————————————————————————————

serve: ## 🌐 Lance le serveur de développement Symfony
	$(SYMFONY_BIN) server:start -d
	@echo "$(CYAN)🌐 Serveur démarré sur http://$(SERVER_HOST):$(SERVER_PORT)$(NC)"

console: ## 🖥️  Ouvre la console Symfony
	$(CONSOLE)

routes: ## 🗺️  Affiche toutes les routes
	$(CONSOLE) debug:router

router-match: ## 🎯 Teste une route spécifique (ex: make router-match path=/api/users)
	@if [ -z "$(path)" ]; then \
		echo "$(RED)❌ Veuillez spécifier un path : make router-match path=/votre/route$(NC)"; \
		echo "$(YELLOW)Exemples :$(NC)"; \
		echo "  make router-match path=/"; \
		echo "  make router-match path=/api/users"; \
		echo "  make router-match path=/login"; \
		exit 1; \
	fi
	@echo "$(CYAN)🎯 Test de la route: $(path)$(NC)"
	@$(CONSOLE) router:match $(path)

debug-container: ## 🔍 Liste tous les services du container
	$(CONSOLE) debug:container

debug-events: ## 🔍 Liste tous les events disponibles
	$(CONSOLE) debug:event-dispatcher

before-commit: ## 🔍 Lance toutes les vérifications avant commit (lint, QA, sécurité)
	@echo ""
	@echo "$(BOLD)$(CYAN)╔═══════════════════════════════════════════════════════════╗$(NC)"
	@echo "$(BOLD)$(CYAN)║                                                           ║$(NC)"
	@echo "$(BOLD)$(CYAN)║           🔍 VÉRIFICATIONS AVANT COMMIT                   ║$(NC)"
	@echo "$(BOLD)$(CYAN)║                                                           ║$(NC)"
	@echo "$(BOLD)$(CYAN)╚═══════════════════════════════════════════════════════════╝$(NC)"
	@echo ""
	@echo "$(BOLD)$(YELLOW)📝 1/11 - Validation composer.json...$(NC)"
	@$(MAKE) composer-validate
	@echo ""
	@echo "$(BOLD)$(YELLOW)🔒 2/11 - Audit de sécurité Composer...$(NC)"
	@$(MAKE) composer-audit
	@echo ""
	@echo "$(BOLD)$(YELLOW)📄 3/11 - Validation YAML...$(NC)"
	@$(MAKE) lint-yaml
	@echo ""
	@echo "$(BOLD)$(YELLOW)🎨 4/11 - Validation Twig...$(NC)"
	@$(MAKE) lint-twig
	@echo ""
	@echo "$(BOLD)$(YELLOW)🔧 5/11 - Correction automatique du style PHP (CS Fixer)...$(NC)"
	@$(MAKE) cs-fix
	@echo ""
	@echo "$(BOLD)$(YELLOW)📦 6/11 - Validation Container Symfony...$(NC)"
	@$(MAKE) lint-container
	@echo ""
	@echo "$(BOLD)$(YELLOW)📊 7/11 - Analyse statique PHPStan (niveau max)...$(NC)"
	@$(MAKE) phpstan
	@echo ""
	@echo "$(BOLD)$(YELLOW)🔍 8/11 - Détection de code complexe (PHPMD)...$(NC)"
	@$(MAKE) phpmd
	@echo ""
	@echo "$(BOLD)$(YELLOW)📋 9/11 - Détection de code dupliqué (PHPCPD)...$(NC)"
	@$(MAKE) phpcpd
	@echo ""
	@echo "$(BOLD)$(YELLOW)🔐 10/11 - Vérification de sécurité Symfony...$(NC)"
	@$(MAKE) security-check
	@echo ""
	@echo "$(BOLD)$(MAGENTA)🔐 11/11 - Mise à jour du badge dans le README...$(NC)"
	@$(MAKE) update-coverage-badge
	@echo ""
	@echo "$(BOLD)$(GREEN)╔═══════════════════════════════════════════════════════════╗$(NC)"
	@echo "$(BOLD)$(GREEN)║                                                           ║$(NC)"
	@echo "$(BOLD)$(GREEN)║   ✅ Toutes les vérifications sont passées avec succès !  ║$(NC)"
	@echo "$(BOLD)$(GREEN)║   🚀 Vous pouvez commiter en toute confiance !            ║$(NC)"
	@echo "$(BOLD)$(GREEN)║                                                           ║$(NC)"
	@echo "$(BOLD)$(GREEN)╚═══════════════════════════════════════════════════════════╝$(NC)"
	@echo ""

# =============================================================================
# SÉCURITÉ
# =============================================================================

## —— 🔒 Sécurité ——————————————————————————————————————————————————————————————

security-check: ## 🔍 Vérifie les vulnérabilités de sécurité
	@echo "$(CYAN)🔍 Vérification des vulnérabilités de sécurité...$(NC)"
	symfony security:check
	@echo "$(GREEN)✅ Vérification terminée$(NC)"

security-audit: composer-audit ## 🔒 Audit de sécurité complet
	@echo "$(GREEN)✅ Audit de sécurité terminé$(NC)"

