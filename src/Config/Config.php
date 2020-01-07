<?php
declare(strict_types=1);

namespace UniGen\Config;

use UniGen\Config\Exception\InvalidConfigSchemaException;
use UniGen\Config\Exception\UnknownConfigKeyException;

class Config
{
    /** @var array<string, mixed> */
    private $parameters;

    /**
     * @param array<string, mixed> $parameters
     * @param Schema $schema
     *
     * @throws InvalidConfigSchemaException
     */
    public function __construct(array $parameters, Schema $schema)
    {
        $schema->validate($parameters);
        $this->parameters = $parameters;
    }

    /**
     * @param string $key
     *
     * @return mixed
     *
     * @throws UnknownConfigKeyException
     */
    public function get(string $key)
    {
        if (!$this->has($key)) {
            throw new UnknownConfigKeyException(sprintf('Config key "%s" does not exist.', $key));
        }

        return $this->parameters[$key];
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->parameters);
    }
}
