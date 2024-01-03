<?php
/**
 * File for class RegexBuilderTest.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation\Tests;

use PHPUnit\Framework\TestCase;
use TMD\Documentation\RegexBuilder;

/**
 * Test class for RegexBuilder.
 */
final class RegexBuilderTest extends BaseTest {
	/**
	 * RegexBuilder instance.
	 *
	 * @var RegexBuilder|null
	 */
	private ?RegexBuilder $regexbuilder = null;

	/**
	 * Test setup - create new instance of RegexBuilder.
	 *
	 * @return void
	 */
	protected function setUp(): void {
		$this->regexbuilder = new RegexBuilder();
	}

	/**
	 * Test teardown - free the instance of RegexBuilder.
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		unset( $this->regexbuilder );
	}

	/**
	 * Test of function `optional`.
	 *
	 * @return void
	 */
	public function testOptional(): void {
		// 1/ Empty
		$content = '';
		$expected = '(?:)';
		$result = RegexBuilder::optional( $content );
		self::assertEquals( $expected, $result, 'test_optional_1' );

		// 2/ Basic
		$content = 'CoNT3nt';
		$expected = '(?:CoNT3nt)';
		$result = RegexBuilder::optional( $content );
		self::assertEquals( $expected, $result, 'test_optional_1' );
	}

	/**
	 * Test of function `pattern`.
	 *
	 * @return void
	 */
	public function testPattern(): void {
		// 1/ Empty
		$pattern = '';
		$expected = '';
		$result = RegexBuilder::pattern( $pattern );
		self::assertEquals( $expected, $result, 'test_pattern_1' );

		// 2/ All known
		foreach ( array( RegexBuilder::RE_PATTERN_TND, RegexBuilder::RE_PATTERN_TFD, RegexBuilder::RE_PATTERN_REST, RegexBuilder::RE_PATTERN_AUTHOR, RegexBuilder::RE_PATTERN_ACCESS, RegexBuilder::RE_PATTERN_TD, RegexBuilder::RE_PATTERN_UD ) as $one_pattern ) {
			$result = RegexBuilder::pattern( $one_pattern );
			self::assertNotEmpty( $result, 'test_pattern_2' );
		}

		// 3/ Unknown
		$pattern = 'TNT';
		$expected = 'TNT';
		$result = RegexBuilder::pattern( $pattern );
		self::assertEquals( $expected, $result, 'test_pattern_3' );
	}

	/**
	 * Test of function `group`.
	 *
	 * @return void
	 */
	public function testGroup(): void {
		// 1/ Empty, name empty string
		$name = '';
		$content = '';
		$expected = '(?<>)';
		$result = RegexBuilder::group( $name, $content );
		self::assertEquals( $expected, $result, 'test_group_1' );

		// 2/ Empty, name null
		$name = null;
		$content = '';
		$expected = '(?:)';
		$result = RegexBuilder::group( $name, $content );
		self::assertEquals( $expected, $result, 'test_group_2' );

		// 3/ Content, name null
		$name = null;
		$content = 'C0nTeNT';
		$expected = '(?:C0nTeNT)';
		$result = RegexBuilder::group( $name, $content );
		self::assertEquals( $expected, $result, 'test_group_3' );

		// 4/ Content, name
		$name = 'N4mE';
		$content = 'C0nTeNT';
		$expected = '(?<N4mE>C0nTeNT)';
		$result = RegexBuilder::group( $name, $content );
		self::assertEquals( $expected, $result, 'test_group_4' );
	}
}
