
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
- Une adaptation du terme HR (Human resource) en Malagasy.

## Requirements :
```
    - Docker desktop
    - Symfony/api-platform/Docker base knowledge
```

## Pre-install
```
    - Verify compose file, the container name etc, you can personalise yours
    - Verify all secret key (database, mercure ... ) in .env
    - Generate JWT Token by : bin/console lexik:jwt:generate-keypair
```

## Installation :
```
    - clone this repository
    - run : docker compose build
    - run : docker compose up --wait
```

## Post-install
```
    - Verify docker container health
    - Verify HTTP accessibility, go to: https://localhost
    - Try running unit test
```

## Before push
```
    - Make sure that your code follow the PSR rules by launching : ./vendor/bin/php-cs-fixer fix
```


*Code for fun :heart: !*