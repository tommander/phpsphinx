<?php
/**
 * PhpSphinx Test.
 *
 * @package Fejz.
 */

declare(strict_types=1);

namespace TMD\Documentation\Tests;

use PHPUnit\Framework\TestCase;
use TMD\Documentation\Helper;
use TMD\Documentation\PhpSphinx;

/**
 * Undocumented class
 */
final class PhpSphinxTest extends BaseTest {
	/**
	 * Undocumented variable
	 *
	 * @var PhpSphinx|null
	 */
	private ?PhpSphinx $phpsphinx = null;

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	protected function setUp(): void {
		$this->phpsphinx = new PhpSphinx();
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		unset( $this->phpsphinx );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function testDoRun(): void {
		if ( is_a( $this->phpsphinx, PhpSphinx::class ) !== true ) {
			self::fail( 'test_dorun_0' );
			return;
		}

		// 1/ Empty.
		$opts_override = array();
		$dry_run = true;
		ob_start();
		$this->phpsphinx->do_run( $opts_override, $dry_run );
		$result = ob_get_contents();
		ob_end_clean();
		$expected = PhpSphinx::$help_text . PHP_EOL;
		self::assertEquals( $expected, $result, 'test_dorun_1' );

		// 2/ Help short
		$opts_override = array( 'h' => '' );
		$dry_run = true;
		ob_start();
		$this->phpsphinx->do_run( $opts_override, $dry_run );
		$result = ob_get_contents();
		ob_end_clean();
		$expected = PhpSphinx::$help_text . PHP_EOL;
		self::assertEquals( $expected, $result, 'test_dorun_2' );

		// 3/ Help long
		$opts_override = array( 'help' => '' );
		$dry_run = true;
		ob_start();
		$this->phpsphinx->do_run( $opts_override, $dry_run );
		$result = ob_get_contents();
		ob_end_clean();
		$expected = PhpSphinx::$help_text . PHP_EOL;
		self::assertEquals( $expected, $result, 'test_dorun_3' );

		// 4/ Version long
		$opts_override = array( 'version' => false );
		$dry_run = true;
		ob_start();
		$this->phpsphinx->do_run( $opts_override, $dry_run );
		$result = ob_get_contents();
		ob_end_clean();
		$expected = PhpSphinx::$name_text . ' ' . PhpSphinx::$version_text . PHP_EOL;
		self::assertEquals( $expected, $result, 'test_dorun_4' );

		// 5/ Test of correct pass-through.
		$opts_override = array(
			'inputdir' => __DIR__ . '/TestDirEmpty',
			'outputdir' => __DIR__ . '/TestDirEmpty',
		);
		$dry_run = true;
		ob_start();
		$this->phpsphinx->do_run( $opts_override, $dry_run );
		$result = ob_get_contents();
		ob_end_clean();
		$expected = 'Searching for PHP source files...' . PHP_EOL . 'Found 0 files.' . PHP_EOL . 'Saving index files...' . PHP_EOL . 'Script finished.' . PHP_EOL;
		self::assertEquals( $expected, $result, 'test_dorun_5' );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function testRun(): void {
		if ( is_a( $this->phpsphinx, PhpSphinx::class ) !== true ) {
			self::fail( 'test_run_0' );
			return;
		}

		// 1/ Empty
		$format = '';
		$inputdir = '';
		$outputdir = '';
		$dry_run = true;
		ob_start();
		$this->phpsphinx->run( $format, $inputdir, $outputdir, $dry_run );
		$result = ob_get_contents();
		ob_end_clean();
		$expected = '[ERROR] Input directory "" does not exist.' . PHP_EOL;
		self::assertEquals( $expected, $result, 'test_run_1' );

		// 2/ Non-sense
		$format = 'pdf';
		$inputdir = '/hello/world/';
		$outputdir = '/this/does/not/exist';
		$dry_run = true;
		ob_start();
		$this->phpsphinx->run( $format, $inputdir, $outputdir, $dry_run );
		$result = ob_get_contents();
		ob_end_clean();
		$expected = '[ERROR] Input directory "/hello/world/" does not exist.' . PHP_EOL;
		self::assertEquals( $expected, $result, 'test_run_2' );

		// 3/ Non-sense with existing inputdir
		$format = 'pdf';
		$inputdir = Helper::make_path( __DIR__, 'TestDir' );
		$outputdir = '/this/does/not/exist';
		$dry_run = true;
		ob_start();
		$this->phpsphinx->run( $format, $inputdir, $outputdir, $dry_run );
		$result = ob_get_contents();
		ob_end_clean();
		$expected = '[ERROR] Output directory "/this/does/not/exist" does not exist.' . PHP_EOL;
		self::assertEquals( $expected, $result, 'test_run_3' );

		// 4/ Non-sense with existing inputdir and outputdir
		$format = 'pdf';
		$inputdir = Helper::make_path( __DIR__, 'TestDir' );
		$outputdir = Helper::make_path( __DIR__, 'TestDirEmpty' );
		$dry_run = true;
		ob_start();
		$this->phpsphinx->run( $format, $inputdir, $outputdir, $dry_run );
		$result = ob_get_contents();
		ob_end_clean();
		$expected = '[ERROR] Format "pdf" is unknown. Correct is one of (rst, md, html).' . PHP_EOL;
		self::assertEquals( $expected, $result, 'test_run_4' );

		// 5/ Basic
		$format = 'rst';
		$inputdir = Helper::make_path( __DIR__, 'TestDir' );
		$outputdir = Helper::make_path( __DIR__, 'TestDirEmpty' );
		$dry_run = true;
		ob_start();
		$this->phpsphinx->run( $format, $inputdir, $outputdir, $dry_run );
		$result = ob_get_contents();
		ob_end_clean();
		$expected = <<<EOS
		Searching for PHP source files...
		Found 0 files.
		Saving index files...
		Script finished.

		EOS;
		self::assertEquals( $expected, $result, 'test_run_5' );
	}
}
