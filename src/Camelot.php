<?php

namespace MetoLabs\CamelotPHP;

use MetoLabs\CamelotPHP\Command\Command;
use MetoLabs\CamelotPHP\Command\Configuration;
use MetoLabs\CamelotPHP\Exceptions\CamelotExecutionException;
use MetoLabs\CamelotPHP\Exceptions\CamelotNotInstalledException;
use MetoLabs\CamelotPHP\Exceptions\DependencyErrorException;
use MetoLabs\CamelotPHP\Exceptions\FileNotFoundException;
use Spatie\TemporaryDirectory\Exceptions\PathAlreadyExists;
use Symfony\Component\Process\Process;

class Camelot
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
     * @param string $filePath
     * @param string|null $mode
     * @param string|null $output
     * @param string|null $binPath
     * @param string|null $env
     * @param bool $debug
     * @throws PathAlreadyExists
     */
    public function __construct(string $filePath, ?string $mode = null, ?string $output = null, ?string $binPath = null, ?string $env = null, bool $debug = false)
    {
        $this->configuration = Configuration::make()
            ->setBinPath($binPath)
            ->setFilePath($filePath)
            ->setOutput($output)
            ->setMode($mode)
            ->setEnv($env)
            ->setDebug($debug);
    }

    /**
     * Make.
     *
     * @param string $path
     * @param string|null $mode
     * @param string|null $output
     * @param string|null $binPath
     * @param string|null $env
     * @param bool $debug
     * @return self
     * @throws PathAlreadyExists
     */
    public static function make(string $path, ?string $mode = null, ?string $output = null, ?string $binPath = null, ?string $env = null, bool $debug = false): self
    {
        return new self($path, $mode, $output, $binPath, $env, $debug);
    }

    /**
     * Get configuration.
     *
     * @return Configuration
     */
    public function configuration(): Configuration
    {
        return $this->configuration;
    }

    /**
     * Get Camelot version.
     *
     * @return ?string
     * @throws PathAlreadyExists
     */
    public function version(): ?string
    {
        $output = $this->run(Command::version());

        if (preg_match('/version\s+([\d.]+)/i', $output, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Run command.
     *
     * @param string $command
     * @return string
     */
    protected function run(string $command): string
    {
        $output = '';
        $configuration = $this->configuration();
        $process = Process::fromShellCommandline(
            $command,
            $configuration->getCwd(),
            $configuration->getEnv()
        );

        $process->run();

        if ($configuration->debug()) {
            print('command: ' . $command . PHP_EOL);
            print('stdout: ' . $process->getOutput() . PHP_EOL);
            print('errout: ' . $process->getErrorOutput());
        }

        if (! $process->isSuccessful()) {
            $this->handleProcessError($command, $process->getErrorOutput());
        }

        if (str_contains($command, '--version')) {
            return $process->getOutput();
        }

        if (! is_null($this->configuration()->getTmpDir())) {
            $output = $this->getFilesContents($this->configuration()->getOutput());
            $this->configuration()->getTmpDir()->delete();
        }

        return json_encode($output, JSON_PRETTY_PRINT);
    }

    /**
     * Extract PDF.
     *
     * @param string $to
     * @return string|array|object
     */
    public function extract(string $to = 'array'): string|array|object
    {
        $extracted = $this->run(Command::make($this->configuration));

        if ($to === 'string') {
            return $extracted;
        }

        if ($to === 'array') {
            return json_decode($extracted, true);
        }

        if ($to === 'object') {
            return json_decode($extracted, false);
        }

        return $extracted;
    }

    /**
     * Get extracted data content.
     *
     * @param string $output
     * @return array
     */
    protected function getFilesContents(string $output): array
    {
        $info = pathinfo($output);

        $filename = $info['filename'];
        $extension = $info['extension'];
        $dirname = $info['dirname'];

        $files = scandir($dirname);

        $files = array_values(array_filter($files, function ($file) use ($filename, $extension) {
            return preg_match("/{$filename}-page-(\d+)-table-(\d+)\.{$extension}$/", $file);
        }));

        $result = [
            'pages' => []
        ];

        foreach ($files as $file) {
            if (preg_match("/^{$filename}-page-(\d+)-table-(\d+)\.{$extension}$/", $file, $matches)) {
                $page = (int) $matches[1];
                $table = (int) $matches[2];

                $content = json_decode(
                    file_get_contents($dirname . DIRECTORY_SEPARATOR . $file),
                    true
                );

                if (!isset($result['pages'][$page])) {
                    $result['pages'][$page] = [
                        'tables' => []
                    ];
                }

                $result['pages'][$page]['tables'][$table] = $content;
            }
        }

        return $result;
    }

    /**
     * Handle errors.
     *
     * @param string $command
     * @param string $errorOutput
     * @return void
     */
    protected function handleProcessError(string $command, string $errorOutput): void
    {
        if (str_contains($errorOutput, 'command not found') || str_contains($errorOutput, 'camelot: not found')) {
            throw new CamelotNotInstalledException("The 'camelot' command is not installed or not in the system PATH.\nCommand: {$command}\nError: {$errorOutput}");
        }

        if (str_contains($errorOutput, "Invalid value for 'FILEPATH'")) {
            throw new FileNotFoundException("Input file not found.\nCommand: {$command}\nError: {$errorOutput}");
        }

        if (str_contains($errorOutput, 'is not installed') || str_contains($errorOutput, 'gs: not found')) {
            throw new DependencyErrorException("Dependency is not installed or not found in PATH.\nCommand: {$command}\nError: {$errorOutput}");
        }

        throw new CamelotExecutionException("Camelot failed.\nCommand: {$command}\nError: {$errorOutput}");
    }

}