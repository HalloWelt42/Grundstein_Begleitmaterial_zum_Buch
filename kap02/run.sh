#!/usr/bin/env sh
#
# Grundstein - Kapitel 2: der bequeme Dauer-Arbeitsplatz.
#
# Dieses Skript fährt unser selbst gebautes Arbeits-Image hoch, hängt den
# aktuellen Ordner als /app in den Container und reicht alle Argumente an
# PHP weiter. Statt den langen docker-Befehl immer wieder zu tippen, rufst
# du nur noch auf:
#
#     ./run.sh hallo.php
#     ./run.sh info.php
#     ./run.sh -v
#
# Ohne Argumente zeigt es die installierte PHP-Version.

# set -e bricht bei einem Fehler ab, set -u meckert bei unbekannten Variablen.
set -eu

# Name des Images aus unserem Dockerfile. An einer Stelle definiert, überall
# genutzt - so lässt er sich später mit einem Handgriff ändern.
IMAGE="grundstein-php"

# Ohne Argumente behandeln wir den Aufruf wie "run.sh -v".
if [ "$#" -eq 0 ]; then
    set -- -v
fi

# --rm  räumt den Container nach dem Lauf wieder auf (kein Müll bleibt liegen).
# -v    hängt das aktuelle Verzeichnis als /app in den Container ein.
# -w    macht /app dort zum Arbeitsverzeichnis.
docker run --rm -v "$PWD":/app -w /app "$IMAGE" php "$@"
