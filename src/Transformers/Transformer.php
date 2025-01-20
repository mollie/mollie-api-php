<?php

namespace Mollie\Api\Transformers;

use Mollie\Api\Utils\Arr;

abstract class Transformer
{
    protected array $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    abstract public function create(): mixed;

    public static function new($data): self
    {
        /** @phpstan-ignore-next-line */
        return new static($data);
    }

    /**
     * Get a value from the data array or the backup key.
     *
     * @param  string|array<string>  $key
     * @param  mixed  $default
     */
    protected function get($key, $default = null, $backupKey = 'filters.')
    {
        $keys = (array) $key;

        if ($backupKey !== null) {
            $keys[] = $backupKey . $key;
        }

        foreach ($keys as $key) {
            if ($value = Arr::get($this->data, $key, $default)) {
                return $value;
            }
        }

        return $default;
    }

    /**
     * @param  string|array<string>  $key
     * @param  mixed  $value
     */
    protected function includes($key, $value, $backupKey = 'filters.'): bool
    {
        return Arr::includes($this->data, [$backupKey . $key, $key], $value);
    }
}
