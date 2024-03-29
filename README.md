[![GitHub Actions](https://github.com/a-sera-org/asera-api/workflows/a-sera-ci/badge.svg)](https://github.com/a-sera-org/asera-api/actions?workflow=a-sera-ci)

# ASERA (API)
`EN:`Asera is a recruitment platform dedicated to various targets, including students seeking internships or alternances, engineers looking for new opportunities, recruiters in search of talents, as well as companies seeking new collaborators. The word 'Asera' derives its roots from three Malagasy terms:
- 'Asa' (work) and 'Serasera' (diversity)
- 'Asa' and 'Sera'
- An adaptation of the term HR (Human Resources) in Malagasy."

`FR:`Asera est une plateforme de recrutement dédiée à diverses cibles, comprenant des étudiants à la recherche de stages ou d'alternances, des ingénieurs en quête de nouvelles opportunités, des recruteurs en quête de talents, ainsi que des entreprises à la recherche de nouveaux collaborateurs.
Le mot "Asera" puise ses racines dans trois termes malgaches : 
- "Asa" (travail), "Serasera" (diversité)
- "Asa" et "Sera"
- Une adaptation du terme HR (Human Resources) en Malagasy.

## Requirements :
```
    - Docker desktop
    - Symfony/api-platform/Docker base knowledge
```

## Installation :
```
    - clone this repository
    - Copy .env to .env.local, change variables in if needle
    - run : docker compose build
    - run : docker compose up --wait
```

## Post-install
```
    - Verify docker container health in terminal, if there was an error or container is unhealthy, run compose by using : docker compose up
    - Verify HTTP accessibility, go to: https://localhost
```

# Without Docker
## Requirements :

```
- php>8.2 with intl, mbstring, xml, pgsql, gd, zip enabled
- Postgresql
- Symfony CLI
- Node & Yarn CLI
```

## Installation
```
- Clone this repository
- Copy .env to .env.local, change variables / DB_URL in if needle
- Generate JWT key by : php bin/console lexik:jwt:generate-keypair
```

## Post install
```
- symfony serve
- yarn install
- yarn watch
- Open indicated server port in cli, ex : localhost:8000 
- Create admin user by running : php bin/console asera:user --create
```


## Before push
```
    - Make sure that your code follow the PSR and PHP8 coding styles and rules by launching : ./vendor/bin/php-cs-fixer fix
    - Make sure you write unit test and it's green by bin/phpunit.
    - Validate your DB schemas by running : php bin/console doctrine:schema:validate
```

*Code for fun :heart: !*