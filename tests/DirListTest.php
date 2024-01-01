<?php
/**
 * DirList Test.
 *
 * @package Fejz.
 */

declare(strict_types=1);

namespace TMD\Documentation\Tests;

use PHPUnit\Framework\TestCase;
use TMD\Documentation\DirList;

/**
 * Undocumented class
 */
final class DirListTest extends BaseTest {
	/**
	 * Undocumented variable
	 *
	 * @var DirList|null
	 */
	private ?DirList $dirlist = null;

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	protected function setUp(): void {
		$this->dirlist = new DirList();
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		unset( $this->dirlist );
	}

	/**
	 * Undocumented function
	 *
	 * - Error #0 - DirList instance is not initialized.
	 * - Error #1 - Test "Test something" failed.
	 *
	 * @return void
	 */
	public function testScandirRecursive(): void {
		// 0/ Check dependencies and prerequisites.
		if ( is_a( $this->dirlist, DirList::class ) !== true ) {
			self::fail( 'test_scandirrecursive_0' );
			return;
		}

		// 1/ Basic test.
		$directory = __DIR__;
		$include = array( '/\.php$/' );
		$exclude = array( '/bootstrap.php/' );
		$result_raw = $this->dirlist->scandir_recursive( $directory, $include, $exclude );
		$result = array();
		foreach ( $result_raw as $one_result_raw ) {
			$result[] = $one_result_raw->getPathname();
		}
		foreach ( $result as &$one_result ) {
			$one_path = explode( DIRECTORY_SEPARATOR, $one_result );
			$one_result = end( $one_path );
		}
		sort( $result );
		$expected = array( 'BaseTest.php', 'DirListTest.php', 'DocblockExtractTest.php', 'FormatterRstTest.php', 'FormatterTest.php', 'HelperTest.php', 'ParametersTest.php', 'PhpDocTest.php', 'PhpSphinxTest.php', 'RegexBuilderTest.php', 'TokenizerTest.php', 'folderfile1.php', 'rootfile1.php', 'rootfile2.php', 'subfolderfile1.php' );
		self::assertEquals( $expected, $result, 'test_scandirrecursive_1' );

		// 2/ Empty directory means nothing is scanned. Specifically testing this to make sure it is not understood as relative path (i.e. scan current directory).
		$directory = '';
		$include = array( '/.*/' );
		$exclude = array();
		$result = $this->dirlist->scandir_recursive( $directory, $include, $exclude );
		$expected = array();
		self::assertEquals( $expected, $result, 'test_scandirrecursive_2' );

		// 3/ Gracefully handle that include/exclude contain non-regex strings.
		$directory = __DIR__;
		$include = array( 'x' );
		$exclude = array( 'y' );
		$result = $this->dirlist->scandir_recursive( $directory, $include, $exclude );
		$expected = array();
		self::assertEquals( $expected, $result, 'test_scandirrecursive_3' );
	}
}
