<?php

declare(strict_types=1);

// Mit ERRMODE_EXCEPTION meldet PDO jeden Fehler als PDOException. getCode()
// liefert den SQLSTATE - 23000 steht für eine verletzte Integritätsbedingung.

$pdo = new PDO('sqlite::memory:', null, null, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);
$pdo->exec('CREATE TABLE nutzer (email TEXT UNIQUE)');

$einfuegen = $pdo->prepare('INSERT INTO nutzer (email) VALUES (?)');
$einfuegen->execute(['ada@example.org']);

try {
    // Dieselbe E-Mail ein zweites Mal verletzt die UNIQUE-Bedingung.
    $einfuegen->execute(['ada@example.org']);
} catch (PDOException $fehler) {
    echo 'SQLSTATE ' . $fehler->getCode() . ': verletzte Eindeutigkeit.' . "\n";
}
