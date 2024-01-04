
[![GitHub Actions](https://github.com/a-sera-org/asera-api/workflows/a-sera-ci/badge.svg)](https://github.com/a-sera-org/asera-api/actions?workflow=a-sera-ci)

# ASERA API

## Requirements :
```
    - Docker desktop
    - Symfony base knowledge
```

## Pre-install
```
    - Verify docker container name, you can personalise yours
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