# Worker-Abbild für Kapitel 61.
# Das schlanke Standard-Abbild bringt pcntl nicht mit; die Erweiterung
# liefert die Signalfunktionen (pcntl_async_signals, pcntl_signal), die
# der Worker für das saubere Beenden braucht.
FROM php:8.4-cli

RUN docker-php-ext-install pcntl

WORKDIR /app
