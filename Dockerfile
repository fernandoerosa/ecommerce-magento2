FROM php:8.2-cli

WORKDIR /usr/src/myapp

COPY . /usr/src/myapp

RUN apt-get update && apt-get install -y \
    libxslt-dev \
    libicu-dev \
    libxml2-dev \
    libzip-dev \
    libcurl4-openssl-dev \
    libpng-dev \
    libxrender1 \
    libxext6 \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libonig-dev \
    && docker-php-ext-install -j$(nproc) bcmath curl dom gd intl mysqli pdo_mysql simplexml soap xsl zip sockets \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

CMD [ "php", "./your-script.php" ]