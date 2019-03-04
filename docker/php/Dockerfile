FROM php:7.1

RUN apt-get update && apt-get install -y zlib1g-dev
RUN docker-php-ext-install zip

RUN php -r "copy('https://getcomposer.org/download/1.8.4/composer.phar', 'composer.phar');"
RUN php -r "if (hash_file('SHA256', 'composer.phar') === '1722826c8fbeaf2d6cdd31c9c9af38694d6383a0f2bf476fe6bbd30939de058a') { echo 'Composer.phar verified'; } else { echo 'Composer.phar corrupted'; unlink('composer.phar'); } echo PHP_EOL;"
RUN chmod a+x composer.phar
RUN mv composer.phar /usr/local/bin/composer