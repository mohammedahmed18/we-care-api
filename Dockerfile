FROM ubuntu:20.04

WORKDIR /app


ARG DEBIAN_FRONTEND=noninteractive

# Update APT packages - Base Layer
RUN apt-get update \
    && apt-get upgrade --yes \
    && DEBIAN_FRONTEND=noninteractive apt-get install --no-install-recommends --yes wget curl


# install the required packages
RUN apt update && apt install -y software-properties-common \ 
    && add-apt-repository ppa:ondrej/php \ 
    && apt update \
    && apt-get install -y --no-install-recommends php8.1 \
    && apt-get install -y php8.1-cli php8.1-common php8.1-mysql php8.1-pgsql php8.1-zip php8.1-gd php8.1-mbstring php8.1-curl php8.1-xml php8.1-sqlite3 php8.1-bcmath \
    # php libapache2-mod-php php-mbstring php-cli php-bcmath php-json php-xml php-zip php-pdo php-common php-tokenizer php-mysql \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer



# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*


COPY . .

# install project packages
RUN composer install

RUN php artisan config:clear
RUN php artisan cache:clear


RUN php artisan migrate -n --force \
    && php artisan db:seed --class TestExamSeeder --force \
    && php artisan db:seed --class UserSeeder --force


# for render to map the port
ENV PORT=8000

EXPOSE 8000

CMD [ "php" , "artisan" , "ser", "--host=0.0.0.0" , "--port=8000" ]
