Random Cat Browser
==================

Overview
--------

The purpose of this repository is to check skills of candidates applying to Orba for a position of PHP Developer. If you are here, you probably want to work with us :-).

Random Cat Browser is a simple application built using Symfony Microkernel. The only feature of the application is showing random photo of a cat.
In current state it shows only one hardcoded photo. Your task will be to unhardcode photo getter by applying connection to **Random Cat API**.

### Random Cat API

It's a simple JSON API with only one endpoint: http://randomcatapi.orbalab.com/

It is protected by API key. To be authorized you must pass `api_key` GET parameter to the endpoint. The key that you may use is `5up3rc0nf1d3n714llp455w0rdf0rc47s`.

As a response for request to this API you will get simple JSON object with just one attribute called "url", eg. `{"url": "http://supercats.com/randomkitty123.jpg"}`.

The API is heavily loaded, so from time to time (approximately 25% of all requests) it will not respond with HTTP status 200.

Collection of cats photos used by the API is a little bit outdated, so from time to time (approximately 30% of successfull requests) it will return URL pointing to a 404 page.

Recruitment task description
----------------------------

1. Fork this repository.
2. Install application locally.
3. Apply connection to Random Cat API in `\App\Service\RandomCatUrlGetter::getUrl` method.
4. Frontend should always show correct image. In case of any problems with API, internal `public/images/404.jpg` photo must be shown.
5. Log all failed API responses and invalid images to a text log file.
6. Write unit tests to get more points.
7. Make your code SOLID to get even more points.
8. When you're done, create a Pull Request with your changes.

Installation and running the application
----------------------------------------

You can install application on your local environment or using Docker.

### Using Docker

You need to have Docker and Docker-Compose installed on your local machine.

1. Clone your forked repository (master branch) locally.
2. CD to project root and run `docker-compose up`.
3. Application will be accessible by the following address: `http://localhost:8000/`.
4. Unit tests can be executed by running the following command: `docker exec -i -t php_cats sh -c "/app/vendor/bin/simple-phpunit /app/src"`.
5. Composer can be executed by running the following command: `docker exec -i -t php_cats sh -c "cd /app && composer"`.

### Using PHP and Composer from your local machine

You need to have PHP 7.1 and Composer installed on your local machine.

1. Clone your forked repository (master branch) locally.
2. CD to project root and run `composer install`.
3. Set-up PHP built-in server by running the following command: `php -S localhost:8000 -t public`.
4. Application will be accessible by the following address: `http://localhost:8000/`.
5. Unit tests can be executed by running the following command: `vendor/bin/simple-phpunit src`.