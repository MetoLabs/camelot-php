<?php

namespace MetoLabs\CamelotPHP\Command;

use Spatie\TemporaryDirectory\Exceptions\PathAlreadyExists;

class Command
{
    /**
     * Configuration.
     *
     * @var Configuration
     */
    protected Configuration $configuration;

    /**
     * Constructor.
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Make.
     *
     * @param Configuration $configuration
     * @return self
     */
    public static function make(Configuration $configuration): self
    {
        return new self($configuration);
    }

    /**
     * Get version command.
     *
     * @return $this
     * @throws PathAlreadyExists
     */
    public static function version(): self
    {
        return Command::make(Configuration::make()->setMode(Configuration::MODE_VERSION));
    }

    /**
     * Convert command into a CLI string.
     *
     * @return string
     */
    public function toString(): string
    {
        $options = $this->configuration->toArray();

        $parts = [];

        foreach ($options as $key => $value) {

            if (! is_int($key)) {
                $value = escapeshellarg($value);
            }

            if ($key === Configuration::ARGUMENT_FILE_PATH) {
                $value = str_replace(' ', '\ ', $value);
            }

            if (is_int($key)) {
                $parts[] = $value;
            } else {
                $parts[] = "{$key} {$value}";
            }
        }

        $command = trim(implode(' ', $parts));

        return preg_replace('/\s+/', ' ', $command);
    }

    /**
     * Magic method to cast the command to string.
     *
     * @return string.
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}