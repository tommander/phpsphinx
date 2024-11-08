<?php

/**
 * File for class PhpSphinxTest.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation\Tests;

use PHPUnit\Framework\TestCase;
use TMD\Documentation\Helper;
use TMD\Documentation\PhpSphinx;

/**
 * Test class for PhpSphinx.
 */
final class PhpSphinxTest extends BaseTest
{
    /**
     * PhpSphinx instance.
     *
     * @var PhpSphinx|null
     */
    private ?PhpSphinx $phpsphinx = null;

    /**
     * Test setup - create new instance of PhpSphinx.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->phpsphinx = new PhpSphinx();
    }

    /**
     * Test teardown - free the instance of PhpSphinx.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->phpsphinx);
    }

    /**
     * Test of function `doRun`.
     *
     * @return void
     */
    public function testDoRun(): void
    {
        if (is_a($this->phpsphinx, PhpSphinx::class) !== true) {
            self::fail('test_dorun_0');
            return;
        }

        // 1/ Empty.
        $opts_override = array();
        $dry_run = true;
        ob_start();
        $this->phpsphinx->doRun($opts_override, $dry_run);
        $result = ob_get_contents();
        ob_end_clean();
        $expected = PhpSphinx::$help_text . PHP_EOL;
        self::assertEquals($expected, $result, 'test_dorun_1');

        // 2/ Help short
        $opts_override = array( 'h' => '' );
        $dry_run = true;
        ob_start();
        $this->phpsphinx->doRun($opts_override, $dry_run);
        $result = ob_get_contents();
        ob_end_clean();
        $expected = PhpSphinx::$help_text . PHP_EOL;
        self::assertEquals($expected, $result, 'test_dorun_2');

        // 3/ Help long
        $opts_override = array( 'help' => '' );
        $dry_run = true;
        ob_start();
        $this->phpsphinx->doRun($opts_override, $dry_run);
        $result = ob_get_contents();
        ob_end_clean();
        $expected = PhpSphinx::$help_text . PHP_EOL;
        self::assertEquals($expected, $result, 'test_dorun_3');

        // 4/ Version long
        $opts_override = array( 'version' => false );
        $dry_run = true;
        ob_start();
        $this->phpsphinx->doRun($opts_override, $dry_run);
        $result = ob_get_contents();
        ob_end_clean();
        $expected = PhpSphinx::$name_text . ' ' . PhpSphinx::$version_text . PHP_EOL;
        self::assertEquals($expected, $result, 'test_dorun_4');

        // 5/ Test of correct pass-through.
        $opts_override = array(
            'inputdir' => __DIR__ . '/TestDirEmpty',
            'outputdir' => __DIR__ . '/TestDirEmpty',
        );
        $dry_run = true;
        ob_start();
        $this->phpsphinx->doRun($opts_override, $dry_run);
        $result = ob_get_contents();
        ob_end_clean();
        $expected = 'Searching for PHP source files...' . PHP_EOL . 'Found 0 files.' . PHP_EOL . 'Saving index files...' . PHP_EOL . 'Script finished.' . PHP_EOL;
        self::assertEquals($expected, $result, 'test_dorun_5');
    }

    /**
     * Test of function `generateDocumentation`.
     *
     * @return void
     */
    public function testGenerateDocumentation(): void
    {
        if (is_a($this->phpsphinx, PhpSphinx::class) !== true) {
            self::fail('test_generatedocumentation_0');
            return;
        }

        // 1/ Empty
        $format = '';
        $inputdir = '';
        $outputdir = '';
        $dry_run = true;
        ob_start();
        $this->phpsphinx->generateDocumentation($format, $inputdir, $outputdir, $dry_run);
        $result = ob_get_contents();
        ob_end_clean();
        $expected = '[ERROR] Input directory "" does not exist.' . PHP_EOL;
        self::assertEquals($expected, $result, 'test_generatedocumentation_1');

        // 2/ Non-sense
        $format = 'pdf';
        $inputdir = '/hello/world/';
        $outputdir = '/this/does/not/exist';
        $dry_run = true;
        ob_start();
        $this->phpsphinx->generateDocumentation($format, $inputdir, $outputdir, $dry_run);
        $result = ob_get_contents();
        ob_end_clean();
        $expected = '[ERROR] Input directory "/hello/world/" does not exist.' . PHP_EOL;
        self::assertEquals($expected, $result, 'test_generatedocumentation_2');

        // 3/ Non-sense with existing inputdir
        $format = 'pdf';
        $inputdir = Helper::makePath(__DIR__, 'TestDir');
        $outputdir = '/this/does/not/exist';
        $dry_run = true;
        ob_start();
        $this->phpsphinx->generateDocumentation($format, $inputdir, $outputdir, $dry_run);
        $result = ob_get_contents();
        ob_end_clean();
        $expected = '[ERROR] Output directory "/this/does/not/exist" does not exist.' . PHP_EOL;
        self::assertEquals($expected, $result, 'test_generatedocumentation_3');

        // 4/ Non-sense with existing inputdir and outputdir
        $format = 'pdf';
        $inputdir = Helper::makePath(__DIR__, 'TestDir');
        $outputdir = Helper::makePath(__DIR__, 'TestDirEmpty');
        $dry_run = true;
        ob_start();
        $this->phpsphinx->generateDocumentation($format, $inputdir, $outputdir, $dry_run);
        $result = ob_get_contents();
        ob_end_clean();
        $expected = '[ERROR] Format "pdf" is unknown. Correct is one of (rst, md, html).' . PHP_EOL;
        self::assertEquals($expected, $result, 'test_generatedocumentation_4');

        // 5/ Basic
        $format = 'rst';
        $inputdir = Helper::makePath(__DIR__, 'TestDir');
        $outputdir = Helper::makePath(__DIR__, 'TestDirEmpty');
        $dry_run = true;
        ob_start();
        $this->phpsphinx->generateDocumentation($format, $inputdir, $outputdir, $dry_run);
        $result = ob_get_contents();
        ob_end_clean();
        $expected = <<<EOS
        Searching for PHP source files...
        Found 0 files.
        Saving index files...
        Script finished.

        EOS;
        self::assertEquals($expected, $result, 'test_generatedocumentation_5');
    }
}
