# Grundstein - Kapitel 2: Ein Arbeits-Image für das ganze Buch.
#
# Basis ist das offizielle PHP-8.4-Kommandozeilen-Image. Wir ergänzen nur
# eine einzige Sache: Composer. Statt ihn mühsam zu installieren, kopieren
# wir die fertige Binärdatei aus dem offiziellen Composer-Image herüber.
# So bleibt unsere PHP-Version die einzige Wahrheit, und wir handeln uns
# keine zweite, konkurrierende Installation ein.

FROM php:8.4-cli

# Nur die eine Datei aus dem Composer-Image übernehmen (Mehrstufen-Build).
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Alle Projektdateien leben zur Laufzeit unter /app. Genau dorthin hängen
# wir beim Start unseren Projektordner ein (siehe run.sh).
WORKDIR /app

# Ohne weitere Argumente zeigt der Container einfach die PHP-Version -
# ein harmloser Standard, der bei jedem echten Aufruf ohnehin ersetzt wird.
CMD ["php", "-v"]
