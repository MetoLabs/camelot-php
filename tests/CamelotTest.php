<?php

use PHPUnit\Framework\TestCase;
use MetoLabs\CamelotPHP\Camelot;
use MetoLabs\CamelotPHP\Exceptions\FileNotFoundException;
use MetoLabs\CamelotPHP\Exceptions\CamelotNotInstalledException;
use Spatie\TemporaryDirectory\Exceptions\PathAlreadyExists;

/**
 * @covers \MetoLabs\CamelotPHP\Camelot
 */
class CamelotTest extends TestCase
{
    /**
     * URL to a sample PDF file used in tests.
     *
     * @var string
     */
    protected string $pdfUrl = 'https://www.w3.org/WAI/WCAG21/working-examples/pdf-table/table.pdf';

    /**
     * Path to the temporary local PDF file.
     *
     * @var string
     */
    protected string $tmpPdf;

    /**
     * Set up a temporary PDF file before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->tmpPdf = tempnam(sys_get_temp_dir(), 'camelot_') . '.pdf';

        file_put_contents($this->tmpPdf, file_get_contents($this->pdfUrl));
    }

    /**
     * Clean up the downloaded file after each test.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        if (file_exists($this->tmpPdf)) {
            unlink($this->tmpPdf);
        }
    }

    /**
     * Test that Camelot can be instantiated properly.
     *
     * @return void
     * @throws PathAlreadyExists
     */
    public function testCanInstantiateCamelot(): void
    {
        $camelot = Camelot::make($this->tmpPdf);

        $this->assertInstanceOf(Camelot::class, $camelot);
    }

    /**
     * Test that Camelot can extract table data and return an array.
     */
    public function testExtractReturnsArray(): void
    {
        $camelot = Camelot::make($this->tmpPdf);
        $result = $camelot->extract('array');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('pages', $result);
    }

    /**
     * Test that extract can return a string when requested.
     *
     * @return void
     * @throws PathAlreadyExists
     */
    public function testExtractReturnsString(): void
    {
        $camelot = Camelot::make($this->tmpPdf);
        $result = $camelot->extract('string');

        $this->assertIsString($result);
        $this->assertStringContainsString('"pages"', $result);
    }

    /**
     * Test that an exception is thrown when the input file doesn't exist.
     *
     * @return void
     * @throws PathAlreadyExists
     */
    public function testThrowsExceptionOnMissingFile(): void
    {
        $this->expectException(FileNotFoundException::class);

        $camelot = Camelot::make('/invalid/path/to/file.pdf');
        $camelot->extract();
    }

    /**
     * Test getting the version string.
     *
     * @return void
     * @throws PathAlreadyExists
     */
    public function testVersionReturnsValidString(): void
    {
        $camelot = Camelot::make($this->tmpPdf);
        $version = $camelot->version();

        $this->assertMatchesRegularExpression('/^\d+\.\d+(\.\d+)?$/', $version);
    }

    /**
     * (Optional) Simulate when camelot is not installed and test exception.
     *
     * WARNING: Only enable if you mock or isolate shell execution.
     */
    // public function testThrowsExceptionWhenCamelotMissing(): void
    // {
    //     $this->expectException(CamelotNotInstalledException::class);
    //     $camelot = Camelot::make($this->tmpPdf, binPath: '/nonexistent/camelot');
    //     $camelot->extract();
    // }
}