# Abbild für Kapitel 67: Internationalisierung.
# Das schlanke Standard-Abbild bringt die intl-Erweiterung nicht mit. Sie
# liefert NumberFormatter, IntlDateFormatter, MessageFormatter und Collator -
# die vier Bausteine dieses Kapitels. intl setzt auf der ICU-Bibliothek auf,
# darum wird zuerst deren Entwicklungspaket installiert.
FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y libicu-dev \
    && docker-php-ext-install intl \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
