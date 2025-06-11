<?php

namespace MetoLabs\CamelotPHP\Command;

use Spatie\TemporaryDirectory\Exceptions\PathAlreadyExists;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class Configuration
{
    /**
     * Argument bin path index.
     *
     * @var int
     */
    public const ARGUMENT_BIN_PATH = 0;

    /**
     * Argument mode index.
     *
     * @var int
     */
    public const ARGUMENT_MODE = 1;

    /**
     * Argument file path.
     *
     * @var int
     */
    public const ARGUMENT_FILE_PATH = 2;

    /**
     * Hybrid mode.
     *
     * @var string
     */
    public const MODE_HYBRID = 'hybrid';

    /**
     * Lattice mode.
     *
     * @var string
     */
    public const MODE_LATTICE = 'lattice';

    /**
     * Network mode.
     *
     * @var string
     */
    public const MODE_NETWORK = 'network';

    /**
     * Stream mode.
     *
     * @var string
     */
    public const MODE_STREAM = 'stream';

    /**
     * Version mode.
     *
     * @var string
     */
    public const MODE_VERSION = 'version';

    /**
     * Path to the Camelot binary.
     *
     * @var string
     */
    protected string $binPath = 'camelot';

    /**
     * Output format (csv, json, excel, html, markdown, sqlite).
     *
     * @var string
     */
    protected string $format = 'json';

    /**
     * Parsing mode.
     *
     * @var string
     */
    protected string $mode = 'version';

    /**
     * Debug.
     *
     * @var bool
     */
    protected bool $debug = false;

    /**
     * Path to the input PDF file.
     *
     * @var string
     */
    protected string $filePath;

    /**
     * Page numbers to extract tables from (e.g., "1,3-5").
     *
     * @var string|null
     */
    protected ?string $pages = null;

    /**
     * Password for encrypted PDF.
     *
     * @var string|null
     */
    protected ?string $password = null;

    /**
     * Path to output file or directory.
     *
     * @var string|null
     */
    protected ?string $output = null;

    /**
     * Temporary directory.
     *
     * @var TemporaryDirectory|null
     */
    protected ?TemporaryDirectory $tmpDir = null;

    /**
     * Comma-separated column positions (for Stream mode).
     *
     * @var string|null
     */
    protected ?string $columns = null;

    /**
     * Table areas as comma-separated values.
     *
     * @var string|null
     */
    protected ?string $tableAreas = null;

    /**
     * Shift text vertically for better accuracy.
     *
     * @var string|null
     */
    protected ?string $shiftText = null;

    /**
     * Split text inside cells.
     *
     * @var string|null
     */
    protected ?string $splitText = null;

    /**
     * Use font size to flag rows.
     *
     * @var string|null
     */
    protected ?string $flagSize = null;

    /**
     * Characters to strip from extracted text.
     *
     * @var string|null
     */
    protected ?string $stripText = null;

    /**
     * Language used for OCR (if applicable).
     *
     * @var string|null
     */
    protected ?string $language = null;

    /**
     * Parameter-to-flag mapping for CLI construction.
     *
     * @var array<string, string|int>
     */
    protected array $parameters = [
        'binPath'    => self::ARGUMENT_BIN_PATH,
        'format'     => '--format',
        'pages'      => '--pages',
        'password'   => '--password',
        'output'     => '--output',
        'columns'    => '--columns',
        'tableAreas' => '--table_areas',
        'shiftText'  => '--shift_text',
        'splitText'  => '--split_text',
        'flagSize'   => '--flag_size',
        'stripText'  => '--strip_text',
        'language'   => '--language',
        'mode'       => self::ARGUMENT_MODE,
        'filePath'   => self::ARGUMENT_FILE_PATH,
    ];

    /**
     * Create a new Configuration instance.
     *
     * @return self
     * @throws PathAlreadyExists
     */
    public static function make(): self
    {
        return (new self())->setOutput(null);
    }

    /**
     * Get binary path.
     *
     * @return string
     */
    public function getBinPath(): string
    {
        return $this->binPath;
    }

    /**
     * Get mode.
     *
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Get debug flag.
     *
     * @return bool
     */
    public function debug(): bool
    {
        return $this->debug;
    }

    /**
     * Get output path.
     *
     * @return ?string
     */
    public function getOutput(): ?string
    {
        return $this->output;
    }

    /**
     * Get temporary directory.
     *
     * @return ?TemporaryDirectory
     */
    public function getTmpDir(): ?TemporaryDirectory
    {
        return $this->tmpDir;
    }

    /**
     * Set binary path.
     *
     * @param string|null $binPath
     * @return self
     */
    public function setBinPath(?string $binPath): self
    {
        $this->binPath = $binPath ?? 'camelot';
        return $this;
    }

    /**
     * Set debug.
     *
     * @param bool $debug
     * @return self
     */
    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;
        return $this;
    }

    /**
     * Set file path.
     *
     * @param string $filePath
     * @return self
     */
    public function setFilePath(string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    /**
     * Set mode.
     *
     * @param string|null $mode
     * @return self
     */
    public function setMode(?string $mode): self
    {
        if ($mode === self::MODE_VERSION) {
            $this->output = null;
            $this->tmpDir = null;
        }

        $this->mode = $mode ?? static::MODE_LATTICE;
        return $this;
    }

    /**
     * Set output format.
     *
     * @param string|null $format
     * @return self
     */
    public function setFormat(?string $format): self
    {
        $this->format = $format ?? 'json';
        return $this;
    }

    /**
     * Set pages.
     *
     * @param string|null $pages
     * @return self
     */
    public function setPages(?string $pages): self
    {
        $this->pages = $pages;
        return $this;
    }

    /**
     * Set password.
     *
     * @param string|null $password
     * @return self
     */
    public function setPassword(?string $password): self
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Set output path.
     *
     * @param string|null $output
     * @return self
     * @throws PathAlreadyExists
     */
    public function setOutput(?string $output): self
    {
        $this->output = $output;

        if (is_null($output)) {
            $this->tmpDir = (new TemporaryDirectory())->create();
            $this->output = $this->tmpDir->path('tables.json');
        }

        return $this;
    }

    /**
     * Set temporary directory.
     *
     * @param TemporaryDirectory $tmpDir
     * @return $this
     */
    public function setTmpDir(TemporaryDirectory $tmpDir): self
    {
        $this->tmpDir = $tmpDir;
        return $this;
    }

    /**
     * Set column positions.
     *
     * @param string|null $columns
     * @return self
     */
    public function setColumns(?string $columns): self
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * Set table areas.
     *
     * @param string|null $tableAreas
     * @return self
     */
    public function setTableAreas(?string $tableAreas): self
    {
        $this->tableAreas = $tableAreas;
        return $this;
    }

    /**
     * Set shift text value.
     *
     * @param string|null $shiftText
     * @return self
     */
    public function setShiftText(?string $shiftText): self
    {
        $this->shiftText = $shiftText;
        return $this;
    }

    /**
     * Set split text value.
     *
     * @param string|null $splitText
     * @return self
     */
    public function setSplitText(?string $splitText): self
    {
        $this->splitText = $splitText;
        return $this;
    }

    /**
     * Set flag size option.
     *
     * @param string|null $flagSize
     * @return self
     */
    public function setFlagSize(?string $flagSize): self
    {
        $this->flagSize = $flagSize;
        return $this;
    }

    /**
     * Set strip text characters.
     *
     * @param string|null $stripText
     * @return self
     */
    public function setStripText(?string $stripText): self
    {
        $this->stripText = $stripText;
        return $this;
    }

    /**
     * Set OCR language.
     *
     * @param string|null $language
     * @return self
     */
    public function setLanguage(?string $language): self
    {
        $this->language = $language;
        return $this;
    }

    /**
     * Convert configuration to CLI argument array.
     *
     * @return array<string|int, string>
     */
    public function toArray(): array
    {
        if ($this->mode === static::MODE_VERSION) {
            return [
                $this->binPath,
                '--version',
            ];
        }

        $result = [];

        foreach ($this->parameters as $property => $flag) {
            if (! property_exists($this, $property)) {
                continue;
            }

            $value = $this->{$property};

            if ($value === null) {
                continue;
            }

            $result[$flag] = $value;
        }

        return $result;
    }
}
