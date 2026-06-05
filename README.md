<div align="center">

# 📚 FanRead

**Ton compagnon de lecture personnel.**

*Suis tes lectures, rédige tes impressions, attribue des notes et partage tes coups de cœur — tout ce qu'il faut pour ne jamais perdre le fil d'un bon livre.*

<br/>

[![Security Audit](https://github.com/Papoel/fanread/actions/workflows/audit.yml/badge.svg)](https://github.com/Papoel/fanread/actions/workflows/audit.yml)
[![Quality Analysis](https://github.com/Papoel/fanread/actions/workflows/quality.yaml/badge.svg)](https://github.com/Papoel/fanread/actions/workflows/quality.yaml)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![Symfony](https://img.shields.io/badge/Symfony-8.1-000000?style=flat-square&logo=symfony&logoColor=white)](https://symfony.com/)
[![MariaDB](https://img.shields.io/badge/MariaDB-12.2-003545?style=flat-square&logo=mariadb&logoColor=white)](https://mariadb.org/)
[![PHPStan](https://img.shields.io/badge/PHPStan-level%20max-8892BF?style=flat-square)](https://phpstan.org/)
[![License](https://img.shields.io/badge/license-Proprietary-red?style=flat-square)](#-licence)
[![Last Commit](https://img.shields.io/github/last-commit/Papoel/fanread?style=flat-square)](https://github.com/Papoel/fanread/commits/main)

<br/>

> 🚧 **Projet en cours de développement — WIP**

</div>

---

## 📋 Table des matières

- [À propos](#-à-propos)
- [Fonctionnalités](#-fonctionnalités)
- [Stack technique](#️-stack-technique)
- [Prérequis](#-prérequis)
- [Installation](#-installation)
- [Commandes disponibles](#-commandes-disponibles)
- [CI / Qualité](#-ci--qualité)
- [Roadmap](#️-roadmap)
- [Licence](#-licence)

---

## 📖 À propos

**FanRead** est une application web personnelle dédiée aux passionnés de lecture.
Elle centralise l'ensemble de ton univers littéraire : livres en cours, terminés, abandonnés, envies de lecture... avec pour chaque titre la possibilité de rédiger une fiche complète.

Conçue pour un usage **strictement personnel** (accès par compte), elle offre tout de même une dimension sociale légère : **recommander un titre à un(e) ami(e)** en un clic.

---

## ✨ Fonctionnalités

- 📚 **Bibliothèque personnelle** — catalogue et organise tous tes livres
- 🔖 **Statut de lecture** — `À lire` · `En cours` · `Terminé` · `Abandonné`
- 📝 **Fiches de lecture** — résumé, thèmes, personnages, points forts / faibles
- ⭐ **Notes & impressions** — attribue une note et rédige ton ressenti
- 💛 **Coups de cœur** — marque tes lectures inoubliables
- 👥 **Recommandations** — suggère un livre à un(e) ami(e) directement depuis l'app
- 🔍 **Recherche** — retrouve n'importe quel titre instantanément *(Elasticsearch — à venir)*
- 🔐 **Espace privé** — accès sécurisé par compte utilisateur

---

## 🛠️ Stack technique

| Couche | Technologie |
| --- | --- |
| Langage | PHP 8.4 |
| Framework | Symfony 8.1 |
| Base de données | MariaDB 12.2 |
| Recherche *(à venir)* | Elasticsearch |
| Frontend | Twig · Stimulus · Symfony UX Live Component |
| Assets | Symfony AssetMapper |
| Qualité | PHPStan (max) · PHP-CS-Fixer · PHPMD · PHPCPD |
| CI/CD | GitHub Actions |

---

## 🔧 Prérequis

- PHP **≥ 8.4** avec les extensions `ctype`, `iconv`
- Composer **v2**
- Symfony CLI
- Docker & Docker Compose *(pour la base de données)*
- Make

---

## 🚀 Installation

```bash
# 1. Cloner le dépôt
git clone https://github.com/Papoel/fanread.git
cd fanread

# 2. Installer les dépendances
composer install

# 3. Copier et configurer les variables d'environnement
cp .env .env.local
# → éditer .env.local avec tes paramètres (DATABASE_URL, APP_SECRET...)

# 4. Démarrer les services Docker
make docker-start

# 5. Créer la base de données et appliquer les migrations
make db-create
make db-migrate

# 6. Installer les assets
make assets-install

# 7. Lancer le serveur de développement
make serve
```

L'application est accessible sur **<http://localhost:8000>**

---

## 🧰 Commandes disponibles

Lance `make` ou `make help` pour afficher la liste complète. Voici les essentielles :

<details>
<summary><strong>🎯 Projet</strong></summary>

| Commande | Description |
| --- | --- |
| `make install` | Installation complète (deps + BDD + assets) |
| `make start` | Démarre Docker + serveur Symfony |
| `make stop` | Arrête tous les services |
| `make reset` | Reset cache + arrêt des services |

</details>

<details>
<summary><strong>🗄️ Base de données</strong></summary>

| Commande | Description |
| --- | --- |
| `make db-create` | Crée la base de données |
| `make db-migrate` | Applique les migrations |
| `make db-fixtures` | Charge les fixtures |
| `make db-reset` | Recrée la BDD complète |
| `make db-backup` | Sauvegarde la BDD |

</details>

<details>
<summary><strong>🔬 Qualité du code</strong></summary>

| Commande | Description |
| --- | --- |
| `make phpstan` | Analyse statique (niveau max) |
| `make cs-fix` | Corrige le style de code |
| `make cs-check` | Vérifie le style sans corriger |
| `make phpmd` | Détecte les code smells |
| `make phpcpd` | Détecte le code dupliqué |
| `make qa` | PHPStan + PHPMD + PHPCPD |
| `make qa-full` | Analyse complète |

</details>

<details>
<summary><strong>🧪 Tests & CI locale</strong></summary>

| Commande | Description |
| --- | --- |
| `make test` | Lance les tests |
| `make test-coverage` | Tests avec couverture de code |
| `make ci` | CI rapide (cs-check + lint + phpstan + tests) |
| `make ci-full` | CI complète identique à GitHub Actions |
| `make before-commit` | Vérifications avant commit |

</details>

<details>
<summary><strong>🔎 Lint & Divers</strong></summary>

| Commande | Description |
| --- | --- |
| `make lint` | Lint YAML + Twig + PHP + container |
| `make security-check` | Audit des vulnérabilités |
| `make git-stats` | Statistiques Git du projet |

</details>

---

## 🤖 CI / Qualité

Deux pipelines GitHub Actions s'exécutent à chaque push et pull request :

### 🔒 Security Audit

[![Security Audit](https://github.com/Papoel/fanread/actions/workflows/audit.yml/badge.svg)](https://github.com/Papoel/fanread/actions/workflows/audit.yml)

- Audit `composer audit` des dépendances (CVE high/critical)
- Rapport d'audit uploadé comme artefact

### 🔬 Quality Analysis

[![Quality Analysis](https://github.com/Papoel/fanread/actions/workflows/quality.yaml/badge.svg)](https://github.com/Papoel/fanread/actions/workflows/quality.yaml)

Exécutée dans le container `jakzal/phpqa:php8.4` :

| Étape | Outil |
| --- | --- |
| Validation | `composer validate --strict` |
| Analyse statique | PHPStan niveau max |
| Code dupliqué | PHPCPD |
| Lint YAML | `bin/console lint:yaml` |
| Lint Twig | `bin/console lint:twig` |
| Lint Container | `bin/console lint:container` |

---

## 🗺️ Roadmap

- [x] Structure Symfony 8.1 initialisée
- [x] Pipeline CI (Security + Quality)
- [x] PHPStan niveau max configuré
- [ ] Authentification utilisateur
- [ ] Entités Book / ReadingLog / Review
- [ ] Tableau de bord de lecture
- [ ] Fiches de lecture complètes
- [ ] Système de recommandation entre amis
- [ ] Intégration Elasticsearch
- [ ] Application mobile *(PWA ?)*

---

## 📄 Licence

Ce projet est sous licence **Propriétaire**.
Tous droits réservés — © 2026 [Papoel](https://github.com/Papoel).

---

<div align="center">

Fait avec ❤️ et beaucoup de café ☕ par [Papoel](https://github.com/Papoel)

</div>
