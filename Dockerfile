# Multi-stage build for Laravel + Statamic with Vite
FROM node:20-alpine AS node-builder

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install node dependencies
RUN npm ci

# Copy source files needed for build
COPY vite.config.js ./
COPY resources ./resources
COPY public ./public

# Build assets
RUN npm run build

# PHP Stage
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    libxml2-dev \
    mysql-client \
    postgresql-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    intl \
    opcache \
    && pecl install redis \
    && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# Copy application files
COPY . .

# Copy built assets from node-builder
COPY --from=node-builder /app/public/build ./public/build

# Set permissions
RUN chown -R www-data:www-data /app \
    && chmod -R 755 /app/storage \
    && chmod -R 755 /app/bootstrap/cache

# Copy Nginx config
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf || echo "worker_processes auto;\nerror_log /var/log/nginx/error.log warn;\npid /var/run/nginx.pid;\n\nevents {\n    worker_connections 1024;\n}\n\nhttp {\n    include /etc/nginx/mime.types;\n    default_type application/octet-stream;\n    sendfile on;\n    keepalive_timeout 65;\n\n    server {\n        listen 8000;\n        server_name _;\n        root /app/public;\n        index index.php;\n\n        location / {\n            try_files \$uri \$uri/ /index.php?\$query_string;\n        }\n\n        location ~ \\.php$ {\n            fastcgi_pass 127.0.0.1:9000;\n            fastcgi_index index.php;\n            fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;\n            include fastcgi_params;\n        }\n\n        location ~ /\\.(?!well-known).* {\n            deny all;\n        }\n    }\n}" > /etc/nginx/nginx.conf

# Create supervisor config
RUN echo "[supervisord]\nnodaemon=true\nuser=root\n\n[program:php-fpm]\ncommand=php-fpm\nautostart=true\nautorestart=true\n\n[program:nginx]\ncommand=nginx -g 'daemon off;'\nautostart=true\nautorestart=true\n\n[program:horizon]\ncommand=php /app/artisan horizon\nautostart=true\nautorestart=true\nstdout_logfile=/dev/stdout\nstdout_logfile_maxbytes=0\nstderr_logfile=/dev/stderr\nstderr_logfile_maxbytes=0" > /etc/supervisor/conf.d/supervisord.conf

# Optimize Laravel
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

EXPOSE 8000

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
