# =====================================================================
#  Produktionsnahes, mehrstufiges Abbild für Kapitel 68.
#  Stufe 1 (builder) baut die Abhängigkeiten mit Composer.
#  Stufe 2 (runtime) ist schlank: nur PHP-FPM, der fertige Code und
#  die Abhängigkeiten - kein Composer, keine dev-Pakete, kein Ballast.
# =====================================================================

# ---- Stufe 1: bauen -------------------------------------------------
FROM composer:2 AS builder

WORKDIR /app

# Zuerst nur die Manifeste kopieren. Solange sich composer.lock nicht
# ändert, bleibt die teure Paket-Ebene im Cache und wird nicht neu gebaut.
COPY composer.json composer.lock ./

# --no-dev lässt Test- und Analysewerkzeuge weg; --optimize-autoloader
# baut einen schnellen Klassenindex für den Betrieb. --no-scripts, weil
# in dieser Stufe der Quelltext noch fehlt.
RUN composer install \
        --no-dev \
        --optimize-autoloader \
        --no-interaction \
        --no-progress \
        --no-scripts

# Jetzt den eigenen Code dazu und den Autoloader endgültig optimieren.
COPY src/ ./src/
COPY public/ ./public/
COPY preload.php ./preload.php
# Das Migrations-Werkzeug (bin/) und die Migrationen (migrations/) reisen
# mit, damit der Migrationsschritt der Auslieferung im Abbild lauffähig ist.
COPY bin/ ./bin/
COPY migrations/ ./migrations/
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative

# ---- Stufe 2: Laufzeit ----------------------------------------------
FROM php:8.4-fpm AS runtime

# OPcache ist im Abbild enthalten, aber nicht aktiv - im Betrieb Pflicht.
RUN docker-php-ext-enable opcache

# Betriebs-Konfiguration: OPcache-Einstellungen (Kapitel 59) als eigene
# conf.d-Datei (der zz-Name lädt nach den Vorgaben des Abbilds und
# gewinnt) und der FPM-Pool, der den mitgelieferten Standardpool ersetzt.
COPY opcache.ini   /usr/local/etc/php/conf.d/zz-opcache.ini
COPY fpm-pool.conf /usr/local/etc/php-fpm.d/www.conf

WORKDIR /app

# Nur die Ergebnisse der Bau-Stufe übernehmen - fertig aufgelöste
# Abhängigkeiten und der eigene Code, sonst nichts.
COPY --from=builder /app/vendor/     ./vendor/
COPY --from=builder /app/src/        ./src/
COPY --from=builder /app/public/     ./public/
COPY --from=builder /app/preload.php ./preload.php
COPY --from=builder /app/bin/        ./bin/
COPY --from=builder /app/migrations/ ./migrations/

# FPM lauscht auf Port 9000; nur der Reverse-Proxy spricht ihn an.
EXPOSE 9000

CMD ["php-fpm"]
