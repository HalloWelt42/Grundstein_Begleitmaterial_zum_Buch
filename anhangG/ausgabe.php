<?php

declare(strict_types=1);

// Sobald auch nur ein Zeichen ausgegeben wurde, sind die HTTP-Header bereits
// unterwegs - ein späteres header() kommt zu spät.
echo "Hallo\n";

header('Content-Type: text/plain; charset=utf-8');
