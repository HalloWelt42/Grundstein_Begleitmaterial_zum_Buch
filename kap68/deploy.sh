#!/usr/bin/env bash
# =====================================================================
#  deploy.sh - eine Auslieferung nach dem Zero-Downtime-Muster (Skizze).
#  Baut ein Image, zieht die neue Version PARALLEL zur alten hoch, prüft
#  ihre Gesundheit, schaltet erst dann um und räumt die alte Version ab.
#  Schlägt die Gesundheitsprüfung fehl, wird zurückgerollt.
#
#  Bewusst als Konzept gehalten: Ein echter Betrieb erledigt das über
#  einen Orchestrierer. Die Schritte und ihre Reihenfolge sind aber genau
#  die, die jede Auslieferung durchläuft.
# =====================================================================
set -euo pipefail

# --- Einstellungen ---------------------------------------------------
IMAGE="grundstein-kap68"
# Die Version kennzeichnet das Image eindeutig - hier der Git-Stand.
VERSION="$(git rev-parse --short HEAD 2>/dev/null || echo 'manuell')"
# Der Gesundheits-Endpunkt endet auf .php und deckt sich damit genau mit
# der Whitelist des Reverse-Proxy (webserver.conf). Fragte man /health ohne
# Endung ab, liefe die Anfrage über try_files auf index.php und ergäbe 404.
HEALTH_URL="http://localhost:8080/health.php"
MAX_VERSUCHE=30

# --- 1. Bauen und testen ---------------------------------------------
echo "==> Baue Image ${IMAGE}:${VERSION}"
docker build -t "${IMAGE}:${VERSION}" .

echo "==> Teste das Image, bevor irgendetwas ausgerollt wird"
docker run --rm "${IMAGE}:${VERSION}" php -v

# --- 2. Datenbank-Migrationen (Kapitel 33) ---------------------------
# Migrationen laufen VOR dem Umschalten und müssen so beschaffen sein,
# dass die alte UND die neue Version mit dem Schema zurechtkommen.
echo "==> Wende ausstehende Migrationen an"
docker run --rm --env-file .env "${IMAGE}:${VERSION}" php bin/migrate.php

# --- 3. Neue Version parallel hochziehen -----------------------------
echo "==> Starte die neue Version neben der alten"
docker run -d --name "${IMAGE}-neu" --env-file .env "${IMAGE}:${VERSION}"

# --- 4. Gesundheit prüfen, bevor umgeschaltet wird -------------------
echo "==> Warte auf eine gesunde neue Instanz"
versuch=1
while [ "${versuch}" -le "${MAX_VERSUCHE}" ]; do
    if curl --silent --fail "${HEALTH_URL}" >/dev/null; then
        echo "    gesund nach ${versuch} Versuch(en)"
        break
    fi

    if [ "${versuch}" -eq "${MAX_VERSUCHE}" ]; then
        echo "!! Neue Version wurde nicht gesund - rolle zurück" >&2
        docker rm -f "${IMAGE}-neu"
        exit 1
    fi

    versuch=$((versuch + 1))
    sleep 1
done

# --- 5. Umschalten und alte Version abräumen -------------------------
# Erst jetzt zeigt der Reverse-Proxy auf die neue Instanz. Laufende
# Anfragen der alten Instanz dürfen noch zu Ende laufen (Kapitel 61:
# sauberes Beenden), bevor sie gestoppt wird.
echo "==> Schalte den Verkehr auf die neue Version um"
docker rename "${IMAGE}-neu" "${IMAGE}"        # symbolisch: neu wird zur laufenden
docker stop "${IMAGE}-alt" 2>/dev/null || true # gibt es beim ersten Mal nicht

echo "==> Auslieferung ${VERSION} abgeschlossen"
