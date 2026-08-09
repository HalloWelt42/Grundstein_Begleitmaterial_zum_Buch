<?php

declare(strict_types=1);

function pruefeAlter(int $alter): void
{
    if ($alter < 0) {
        throw new InvalidArgumentException('Alter darf nicht negativ sein.');
    }
}

pruefeAlter(-5);
