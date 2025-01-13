# ip21_akademija_projekt1

Application for accessing cryptocurrency price data, built step by step with php.

This app was created as part of mentoring program of Inštitut za priložnosti 21. stoletja (IP21). 

## Prerequisites

- Docker
- Docker Compose
- Ports 80, 443 and 8080 should be free

## Project Setup

1. Clone the project

Run `git clone <project_url>`

2. Navigate to project directory

Run `cd <project_directory_name>`

3. Build

Run `docker compose up`

4. Login to the environment

Run `docker compose exec php_app bash`

5. Install Composer dependencies

Run `composer install`

## Access

**Aplication:**

[https://localhost/]

**Database:**

[https://localhost:8080/]

## Usage

**CLI:**

Run `php console.php 'help'`
