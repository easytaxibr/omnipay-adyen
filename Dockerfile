FROM php:7.2-alpine

RUN set -xe \
	&& apk add --no-cache --virtual .run-deps \
		git

COPY composer.json /tmp/

RUN set -xe \
	&& curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
	&& composer install \
		--no-dev \
		--no-interaction \
		--no-progress \
		--prefer-dist \
		--no-autoloader \
		--no-plugins \
		--no-scripts \
		--working-dir=/tmp \
	&& rm -r /tmp/composer.json /tmp/composer.lock /tmp/vendor

COPY . /var/www/omnipay-adyen

RUN set -xe \
	&& composer install \
		--no-interaction \
		--no-progress \
		--prefer-dist \
		--optimize-autoloader \
		--working-dir=/var/www/omnipay-adyen \
	&& rm -r ~/.composer

WORKDIR /var/www/omnipay-adyen
