FROM php:7.4-cli
COPY . /usr/src/myapp
WORKDIR /usr/src/myapp

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer


