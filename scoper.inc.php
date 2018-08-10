<?php declare(strict_types=1);

// scoper.inc.php

use Isolated\Symfony\Component\Finder\Finder;

return [
    'finders' => [],                        // Finder[]
    'patchers' => [],                       // callable[]
    'whitelist' => [
        'Mollie\\Api\\*',
    ],
];