# Grundstein - Begleitmaterial (Quellcode)

Der vollständige, lauffähige Quellcode zum Buch **Grundstein - Modernes PHP von der
Installation bis zur sauberen Architektur**.

Jedes Beispiel im Buch, das mehr als ein paar Zeilen umfasst, liegt hier als eigene,
getestete Datei. Im Buch steht meist nur ein Auszug; die vollständige Fassung findest du
unter dem jeweils angegebenen Pfad (Fußnote "Begleitmaterial:" unter dem Listing).

## Aufbau

Die Ordner folgen den Kapiteln des Buches:

- `kap01/` - Was modernes PHP ausmacht (Ausblick-Beispiele)
- `kap02/` - Die ersten Programme
- (weitere Kapitel folgen mit dem Buch)

## Ausführen mit Docker

Alle Beispiele sind mit **PHP 8.4** getestet. Du brauchst kein PHP auf dem Rechner zu
installieren - ein Container genügt:

    docker run --rm -v "$PWD":/app -w /app php:8.4-cli php kap02/hallo.php

Wie du dir daraus einen bequemen Dauer-Arbeitsplatz baust, zeigt Kapitel 2 im Buch.

## Anforderungen

- PHP 8.4 oder neuer (einige Beispiele nutzen Property Hooks und asymmetrische
  Sichtbarkeit aus 8.4; Ausblick-Beispiele zu 8.5 sind entsprechend markiert)
- Für die Projekt-Beispiele ab dem Composer-Kapitel: Composer 2

---

## Unterstützen

Grundstein ist ein privates Hobby-Projekt. Kein Tracking, keine Werbung, keine Kompromisse.

Wenn dir das Buch gefällt, kannst du es weitergeben und mitverbessern -- oder direkt hier:

[![Ko-fi](https://ko-fi.com/img/githubbutton_sm.svg)](https://ko-fi.com/HalloWelt42)

**Crypto:**

| Coin | Adresse |
|------|---------|
| BTC | `bc1qnd599khdkv3v3npmj9ufxzf6h4fzanny2acwqr` |
| DOGE | `DL7tuiYCqm3xQjMDXChdxeQxqUGMACn1ZV` |
| ETH | `0x8A28fc47bFFFA03C8f685fa0836E2dBe1CA14F27` |

Copyright (c) 2025-2026 HalloWelt42
