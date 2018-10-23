Random Cat Browser
==================

Overview
--------

The purpose of this repository is to check skills of candidates applying to Orba for a position of PHP Developer. If you are here, you probably want to work with us :-).

Random Cat Browser is a simple application built using Symfony Microkernel. The only feature of the application is showing random photo of a cat.
In current state it shows only one hardcoded photo. Your task will be to unhardcode photo getter by applying connection to **Random Cat API**.

### Random Cat API

It's a simple JSON API with only one endpoint: http://randomcatapi.orbalab.com/

As a response for request to this API you will get simple JSON object with just one attribute called "url", eg. `{"url": "http://supercats.com/randomkitty123.jpg}`.

The API is heavily loaded, so from time to time (approximately 25% of all requests) it will not respond with HTTP status 200.

Collection of cats photos used by the API is a little bit outdated, so from time to time (approximately 30% of successfull requests) it will return URL pointing to a 404 page.

Recruitment task description
----------------------------

1. Fork this repository.
2. Install application locally.
3. Apply connection to Random Cat API in `\App\Service\RandomCatUrlGetter::getUrl` method.
4. Frontend should always show correct image. In case of any problems with API, internal `public/images/404.jpg` photo must be shown.
5. You will get additional points for writing unit tests.
6. When you're done, create a Pull Request with your changes.

Installation and running the application
------------

1. Clone your forked repository (master branch) locally.
2. Run `composer install`.
3. Set-up PHP built-in server by running the following command from project root: `php -S localhost:8000 -t public`
4. Application will be visible on the following address: `http://localhost:8000/`
5. Unit tests can be run by running the following command from project root: `vendor/bin/simple-phpunit src`