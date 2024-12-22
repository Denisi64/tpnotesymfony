# Utiliser une image légère PHP avec support CLI
FROM php:8.2-cli

# Installer les extensions nécessaires et les outils
RUN apt-get update && apt-get install -y \
    unzip \
    git \
    curl \
    libpq-dev \
    supervisor \
    && docker-php-ext-install pdo pdo_pgsql

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Définir le répertoire de travail
WORKDIR /app

# Copier les fichiers du projet
COPY . /app

# Installer les dépendances Symfony
RUN composer install --no-interaction --optimize-autoloader

# Copier le fichier de configuration supervisord
COPY ./docker/supervisord.conf /etc/supervisor/supervisord.conf

# Exposer les ports nécessaires
EXPOSE 8000
EXPOSE 9000

# Démarrer supervisord par défaut
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
