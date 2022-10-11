# OpenClassrooms - Développeur d'application PHP Symfony - Projet 7

![GitHub Workflow Status (branch)](https://img.shields.io/github/workflow/status/TBoileau/oc-symfony-7/Continuous%20integration/develop?style=for-the-badge)
![GitHub](https://img.shields.io/github/license/TBoileau/oc-symfony-7?style=for-the-badge)
![GitHub pull requests](https://img.shields.io/github/issues-pr-raw/TBoileau/oc-symfony-7?style=for-the-badge)
![GitHub issues](https://img.shields.io/github/issues-raw/TBoileau/oc-symfony-7?style=for-the-badge)

## Installation
```
git clone git@github.com:TBoileau/oc-symfony-7.git
cd oc-symfony-7
make install
```

Options à la commande `make install`:
* `db-user`
* `db-password`
* `db-name`
* `db-host`
* `db-port`
* `db-version`
* `db-charset`

Exemple :
```
make install db-driver=postgresql db-user=root db-password=root db-name=oc-symfony-7 db-host=127.0.0.1 db-port=5432 db-version=14 db-charset=utf8
```

Options par default :
* `db-driver=mysql`
* `db-user=root`
* `db-password=password`
* `db-name=oc-symfony-7`
* `db-host=127.0.0.1`
* `db-port=3306`
* `db-version=8.0`
* `db-charset=utf8mb4`

## Base de données

### Création du schéma de la base de données
*Attention, il faut au préalable avoir créé un fichier contenant les variables d'environnement de la base de données.*
```
make db-schema
```

Option à la commande `make db-schema`:
* `env`

Exemple :
```
make db-schema env=test
```

Option par default :
* `env=dev`

### Chargement des fixtures
*Attention, il faut au préalable que le schéma de la base de données soit créé.*
```
make db-fixtures
```

Option à la commande `make db-fixtures`:
* `env`

Exemple :
```
make db-fixtures env=test
```

Option par default :
* `env=dev`

### Création d'une migration
```
make db-migration
```

## Tests
```
make test
```

## Qualité du code
*Assurez vous d'avoir installer le [binaire de Symfony](https://symfony.com/download).*

```
make qa
```

Cette commande va lancer un ensemble de vérifications sur le code, mais vous pouvez tout à fait les éxecuter indivuellement :
* `make qa-composer` : Analyse du fichier composer.json
* `make qa-doctrine` : Analyse du mapping Doctrine
* `make qa-yaml` : Analyse des fichiers YAML
* `make qa-container` : Analyse du container Symfony
* `make qa-security-check` : Analyse des vulnérabilités de sécurité
* `make qa-phpstan` : Analyse du code avec PHPStan
* `make qa-phpcpd` : Analyse du code avec PHPCPD
* `make qa-phpmd` : Analyse du code avec PHPMD
* `make qa-phpcs-fixer` : Analyse du code avec PHP-CS-Fixer

### Corriger automatiquement des erreurs
```
make fix
```

Cette commande utilise un ensemble d'outils pour corriger le code, mais vous pouvez tout à fait les éxecuter indivuellement :
* `make fix-cs-fixer` : Corrige le code avec PHP-CS-Fixer
* `make fix-eslint` : Corrige le code avec ESLint
* `make fix-stylelint` : Corrige le code avec Stylelint

## Documentation
La documentation est disponible [ici](https://tboileau.github.io/oc-symfony-7/).

## Changelog
[CHANGELOG.md](/CHANGELOG.md) liste tous les changements effectués lors de chaque release.

## À propos
Projet initialement conçu dans un but pédagogique par [Thomas Boileau](https://github.com/TBoileau). Si vous avez la moindre question, contactez [Thomas Boileau](mailto:t-boileau@email.com?subject=[Github]%20oc-symfony-7)