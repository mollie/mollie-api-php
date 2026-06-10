<?php

declare(strict_types=1);

if ($argc < 3) {
    fwrite(STDERR, "Usage: php tools/audit-v3-v4-surface.php <v3-root> <v4-root> [output-json]\n");
    exit(1);
}

[$script, $v3Root, $v4Root] = $argv;
$outputPath = $argv[3] ?? '/tmp/mollie-v3-v4-surface-diff.json';

/**
 * @return list<string>
 */
function allPhpFiles(string $root): array
{
    $files = [];

    foreach (['src', 'tests'] as $base) {
        $directory = "{$root}/{$base}";

        if (! is_dir($directory)) {
            continue;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $files[] = $file->getPathname();
            }
        }
    }

    sort($files);

    return $files;
}

/**
 * @param mixed $token
 */
function tokenText($token): string
{
    return is_array($token) ? $token[1] : $token;
}

/**
 * @param mixed $token
 */
function tokenId($token): ?int
{
    return is_array($token) ? $token[0] : null;
}

/**
 * @param list<mixed> $tokens
 */
function collectName(array $tokens, int &$index): string
{
    $name = '';

    for ($count = count($tokens); $index < $count; $index++) {
        $id = tokenId($tokens[$index]);
        $text = tokenText($tokens[$index]);

        if (
            $id === T_STRING
            || $id === T_NAME_QUALIFIED
            || $id === T_NAME_FULLY_QUALIFIED
            || $id === T_NS_SEPARATOR
        ) {
            $name .= $text;

            continue;
        }

        if ($id === T_WHITESPACE) {
            continue;
        }

        break;
    }

    return $name;
}

/**
 * @param list<mixed> $tokens
 */
function skipWhitespace(array $tokens, int &$index): void
{
    for ($count = count($tokens); $index < $count && tokenId($tokens[$index]) === T_WHITESPACE; $index++) {
    }
}

/**
 * @param list<mixed> $tokens
 */
function parseParams(array $tokens, int $index): string
{
    $depth = 0;
    $signature = '';

    for ($count = count($tokens); $index < $count; $index++) {
        $text = tokenText($tokens[$index]);

        if ($text === '(') {
            $depth++;

            if ($depth === 1) {
                continue;
            }
        }

        if ($text === ')') {
            $depth--;

            if ($depth === 0) {
                break;
            }
        }

        if ($depth >= 1) {
            $signature .= $text;
        }
    }

    $signature = preg_replace('/\s+/', ' ', trim($signature)) ?? '';

    return preg_replace('/\s*,\s*/', ', ', $signature) ?? '';
}

/**
 * @param list<mixed> $tokens
 */
function nextNonWhitespace(array $tokens, int $index): ?string
{
    for ($count = count($tokens); $index < $count; $index++) {
        $id = tokenId($tokens[$index]);

        if ($id !== T_WHITESPACE && $id !== T_COMMENT && $id !== T_DOC_COMMENT) {
            return tokenText($tokens[$index]);
        }
    }

    return null;
}

/**
 * @param list<mixed> $tokens
 */
function previousNonWhitespace(array $tokens, int $index): ?string
{
    for (; $index >= 0; $index--) {
        $id = tokenId($tokens[$index]);

        if ($id !== T_WHITESPACE && $id !== T_COMMENT && $id !== T_DOC_COMMENT) {
            return tokenText($tokens[$index]);
        }
    }

    return null;
}

/**
 * @return array{classes: array<string, array{kind: string, file: string, methods: array<string, string>, constants: list<string>, properties: list<string>}>, files: list<string>}
 */
function inventory(string $root): array
{
    $classes = [];
    $files = [];

    foreach (allPhpFiles($root) as $path) {
        $relativePath = substr($path, strlen($root) + 1);
        $files[] = $relativePath;
        $tokens = token_get_all(file_get_contents($path) ?: '');
        $namespace = '';
        $currentClass = null;
        $classDepth = null;
        $depth = 0;
        $visibility = 'public';

        for ($index = 0, $count = count($tokens); $index < $count; $index++) {
            $id = tokenId($tokens[$index]);
            $text = tokenText($tokens[$index]);

            if ($text === '{') {
                $depth++;

                continue;
            }

            if ($text === '}') {
                if ($classDepth !== null && $depth === $classDepth) {
                    $currentClass = null;
                    $classDepth = null;
                }

                $depth--;

                continue;
            }

            if ($id === T_NAMESPACE) {
                $index++;
                $namespace = collectName($tokens, $index);

                continue;
            }

            if ($id === T_PUBLIC || $id === T_PROTECTED || $id === T_PRIVATE) {
                $visibility = strtolower($text);

                continue;
            }

            $classTokenMap = [
                T_CLASS => 'class',
                T_INTERFACE => 'interface',
                T_TRAIT => 'trait',
            ];

            if (defined('T_ENUM')) {
                $classTokenMap[T_ENUM] = 'enum';
            }

            if (isset($classTokenMap[$id])) {
                if (
                    nextNonWhitespace($tokens, $index + 1) === '('
                    || previousNonWhitespace($tokens, $index - 1) === '::'
                    || previousNonWhitespace($tokens, $index - 1) === 'new'
                ) {
                    continue;
                }

                $classNameIndex = $index + 1;
                skipWhitespace($tokens, $classNameIndex);

                $name = tokenText($tokens[$classNameIndex]);
                $fullyQualifiedName = $namespace !== '' ? "{$namespace}\\{$name}" : $name;

                $classes[$fullyQualifiedName] = [
                    'kind' => $classTokenMap[$id],
                    'file' => $relativePath,
                    'methods' => [],
                    'constants' => [],
                    'properties' => [],
                ];

                $currentClass = $fullyQualifiedName;

                while ($index < $count && tokenText($tokens[$index]) !== '{') {
                    $index++;
                }

                $depth++;
                $classDepth = $depth;
                $visibility = 'public';

                continue;
            }

            if ($currentClass !== null && $depth === $classDepth && $id === T_FUNCTION) {
                $methodNameIndex = $index + 1;
                skipWhitespace($tokens, $methodNameIndex);

                if (tokenText($tokens[$methodNameIndex]) === '&') {
                    $methodNameIndex++;
                    skipWhitespace($tokens, $methodNameIndex);
                }

                $name = tokenText($tokens[$methodNameIndex]);

                if ($visibility === 'public') {
                    $classes[$currentClass]['methods'][$name] = parseParams($tokens, $methodNameIndex + 1);
                }

                $visibility = 'public';

                continue;
            }

            if ($currentClass !== null && $depth === $classDepth && $id === T_CONST && $visibility === 'public') {
                $constantIndex = $index + 1;

                while ($constantIndex < $count && tokenText($tokens[$constantIndex]) !== ';') {
                    if (tokenId($tokens[$constantIndex]) === T_STRING) {
                        $classes[$currentClass]['constants'][] = tokenText($tokens[$constantIndex]);
                    }

                    $constantIndex++;
                }

                $visibility = 'public';

                continue;
            }

            if ($currentClass !== null && $depth === $classDepth && $id === T_VARIABLE && $visibility === 'public') {
                $classes[$currentClass]['properties'][] = substr($text, 1);
                $visibility = 'public';
            }
        }
    }

    ksort($classes);
    sort($files);

    foreach ($classes as &$class) {
        ksort($class['methods']);
        sort($class['constants']);
        sort($class['properties']);
    }

    return ['classes' => $classes, 'files' => $files];
}

/**
 * @param array{file: string} $class
 */
function category(array $class): string
{
    $file = $class['file'];

    return match (true) {
        str_starts_with($file, 'src/EndpointCollection/') => 'endpoint_collections',
        str_starts_with($file, 'src/Http/Requests/') => 'requests',
        str_starts_with($file, 'src/Resources/') => 'resources',
        str_starts_with($file, 'src/Factories/') => 'factories',
        str_starts_with($file, 'src/Fake/') => 'fakes',
        str_starts_with($file, 'src/Types/') => 'types',
        str_starts_with($file, 'src/Contracts/') => 'contracts',
        str_starts_with($file, 'src/Traits/') => 'traits',
        str_starts_with($file, 'tests/') => 'tests',
        default => 'other_src',
    };
}

$v3 = inventory($v3Root);
$v4 = inventory($v4Root);

$diff = [
    'baseline' => 'v3.13.1',
    'counts' => [],
    'missing_classes' => [],
    'new_classes' => [],
    'method_diffs' => [],
    'constant_diffs' => [],
    'property_diffs' => [],
    'file_diffs' => [],
];

foreach (['endpoint_collections', 'requests', 'resources', 'factories', 'fakes', 'types', 'contracts', 'traits', 'other_src', 'tests'] as $category) {
    $diff['counts'][$category] = ['v3' => 0, 'v4' => 0, 'missing' => 0, 'new' => 0];
}

foreach ($v3['classes'] as $class) {
    $diff['counts'][category($class)]['v3']++;
}

foreach ($v4['classes'] as $class) {
    $diff['counts'][category($class)]['v4']++;
}

foreach (array_diff_key($v3['classes'], $v4['classes']) as $name => $class) {
    $category = category($class);
    $diff['missing_classes'][$category][$name] = $class;
    $diff['counts'][$category]['missing']++;
}

foreach (array_diff_key($v4['classes'], $v3['classes']) as $name => $class) {
    $category = category($class);
    $diff['new_classes'][$category][$name] = $class;
    $diff['counts'][$category]['new']++;
}

foreach (array_intersect_key($v3['classes'], $v4['classes']) as $name => $v3Class) {
    $v4Class = $v4['classes'][$name];
    $missingMethods = array_diff_key($v3Class['methods'], $v4Class['methods']);
    $newMethods = array_diff_key($v4Class['methods'], $v3Class['methods']);
    $changedMethods = [];

    foreach (array_intersect_key($v3Class['methods'], $v4Class['methods']) as $methodName => $signature) {
        if ($signature !== $v4Class['methods'][$methodName]) {
            $changedMethods[$methodName] = ['v3' => $signature, 'v4' => $v4Class['methods'][$methodName]];
        }
    }

    if ($missingMethods !== [] || $newMethods !== [] || $changedMethods !== []) {
        $diff['method_diffs'][$name] = [
            'file_v3' => $v3Class['file'],
            'file_v4' => $v4Class['file'],
            'missing' => $missingMethods,
            'new' => $newMethods,
            'changed' => $changedMethods,
        ];
    }

    $missingConstants = array_values(array_diff($v3Class['constants'], $v4Class['constants']));
    $newConstants = array_values(array_diff($v4Class['constants'], $v3Class['constants']));

    if ($missingConstants !== [] || $newConstants !== []) {
        $diff['constant_diffs'][$name] = [
            'file_v3' => $v3Class['file'],
            'file_v4' => $v4Class['file'],
            'missing' => $missingConstants,
            'new' => $newConstants,
        ];
    }

    $missingProperties = array_values(array_diff($v3Class['properties'], $v4Class['properties']));
    $newProperties = array_values(array_diff($v4Class['properties'], $v3Class['properties']));

    if ($missingProperties !== [] || $newProperties !== []) {
        $diff['property_diffs'][$name] = [
            'file_v3' => $v3Class['file'],
            'file_v4' => $v4Class['file'],
            'missing' => $missingProperties,
            'new' => $newProperties,
        ];
    }
}

$diff['file_diffs'] = [
    'missing' => array_values(array_diff($v3['files'], $v4['files'])),
    'new' => array_values(array_diff($v4['files'], $v3['files'])),
];

file_put_contents($outputPath, json_encode($diff, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "Baseline: v3.13.1\n";
echo "Full JSON: {$outputPath}\n";

foreach ($diff['counts'] as $category => $counts) {
    printf(
        "%-22s v3=%3d v4=%3d missing=%3d new=%3d\n",
        $category,
        $counts['v3'],
        $counts['v4'],
        $counts['missing'],
        $counts['new']
    );
}

echo "\nMissing classes by category:\n";

foreach ($diff['missing_classes'] as $category => $classes) {
    echo "[{$category}]\n";

    foreach ($classes as $name => $class) {
        echo " - {$name} ({$class['file']})\n";
    }
}

echo "\nNew classes by category:\n";

foreach ($diff['new_classes'] as $category => $classes) {
    echo "[{$category}]\n";

    foreach ($classes as $name => $class) {
        echo " + {$name} ({$class['file']})\n";
    }
}

$sourceMethodDiffs = array_filter(
    $diff['method_diffs'],
    fn (array $methodDiff): bool => str_starts_with($methodDiff['file_v3'], 'src/')
);

echo "\nMethod diffs in src classes: ".count($sourceMethodDiffs)."\n";

foreach ($sourceMethodDiffs as $name => $methodDiff) {
    echo "* {$name}\n";

    foreach ($methodDiff['missing'] as $method => $signature) {
        echo "  - missing method {$method}({$signature})\n";
    }

    foreach ($methodDiff['new'] as $method => $signature) {
        echo "  + new method {$method}({$signature})\n";
    }

    foreach ($methodDiff['changed'] as $method => $signatures) {
        echo "  ~ changed method {$method}: ({$signatures['v3']}) => ({$signatures['v4']})\n";
    }
}
