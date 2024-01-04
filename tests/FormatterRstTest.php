<?php
/**
 * File for class FormatterRstTest.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation\Tests;

use PHPUnit\Framework\TestCase;
use TMD\Documentation\FormatterRst;

/**
 * Test class for FormatterRst.
 */
final class FormatterRstTest extends BaseTest {
	/**
	 * Test of function `directive`.
	 *
	 * @return void
	 */
	public function testDirective(): void {
		// 1/ Empty
		$directive = '';
		$name = '';
		$content = '';
		$level = 0;
		$return = true;
		$expected = '.. :: ' . PHP_EOL . PHP_EOL;
		$result = FormatterRst::directive( $directive, $name, $content, $level, $return );
		self::assertEquals( $expected, $result, 'test_directive_1' );

		// 2/ Basic
		$directive = 'DIRect1VE';
		$name = 'NaMe';
		$content = 'c0nTenT';
		$level = 1;
		$return = true;
		$expected = '   .. DIRect1VE:: NaMe' . PHP_EOL . PHP_EOL . '      c0nTenT' . PHP_EOL;
		$result = FormatterRst::directive( $directive, $name, $content, $level, $return );
		self::assertEquals( $expected, $result, 'test_directive_2' );

		// 3/ Basic
		$directive = 'DIRect1VE';
		$name = 'NaMe';
		$content = '      c0nTenT';
		$level = 1;
		$return = true;
		$expected = '   .. DIRect1VE:: NaMe' . PHP_EOL . PHP_EOL . '      c0nTenT' . PHP_EOL;
		$result = FormatterRst::directive( $directive, $name, $content, $level, $return );
		self::assertEquals( $expected, $result, 'test_directive_3' );

		// 4/ Basic
		$directive = 'DIRect1VE';
		$name = 'NaMe';
		$content = '         c0nTenT';
		$level = 1;
		$return = true;
		$expected = '   .. DIRect1VE:: NaMe' . PHP_EOL . PHP_EOL . '         c0nTenT' . PHP_EOL;
		$result = FormatterRst::directive( $directive, $name, $content, $level, $return );
		self::assertEquals( $expected, $result, 'test_directive_4' );
	}

	/**
	 * Test of function `type_to_rst`.
	 *
	 * @return void
	 */
	public function testTypeToRst(): void {
		// 1/ Empty
		$type = '';
		$name = '';
		$content = '';
		$expected = '';
		$result = FormatterRst::type_to_rst( $type, $name, $content );
		self::assertEquals( $expected, $result, 'test_typetorst_1' );

		// 2/ Basic
		$type = '';
		$name = '';
		$content = 'conT3nt';
		$expected = 'conT3nt';
		$result = FormatterRst::type_to_rst( $type, $name, $content );
		self::assertEquals( $expected, $result, 'test_typetorst_2' );

		// 3/ Basic
		$type = 'abcd';
		$name = '';
		$content = 'conT3nt';
		$expected = 'conT3nt';
		$result = FormatterRst::type_to_rst( $type, $name, $content );
		self::assertEquals( $expected, $result, 'test_typetorst_3' );

		// 4/ Basic
		$type = 'attr';
		$name = 'hello';
		$content = 'conT3nt';
		$expected = '.. php:attr:: hello' . PHP_EOL . PHP_EOL . '   conT3nt' . PHP_EOL;
		$result = FormatterRst::type_to_rst( $type, $name, $content );
		self::assertEquals( $expected, $result, 'test_typetorst_4' );
	}

	/**
	 * Test of function `fix_indentation`.
	 *
	 * @return void
	 */
	public function testFixIndentation(): void {
		// 1/ Empty
		$text = '';
		$min_indent = 0;
		$expected = '';
		$result = FormatterRst::fix_indentation( $text, $min_indent );
		self::assertEquals( $expected, $result, 'test_fixindentation_1' );

		// 2/ Basic (0 indent)
		$text = 'Text' . PHP_EOL . ' Text' . PHP_EOL . '       Text' . PHP_EOL . PHP_EOL . ' ';
		$min_indent = 0;
		$expected = $text;
		$result = FormatterRst::fix_indentation( $text, $min_indent );
		self::assertEquals( $expected, $result, 'test_fixindentation_2' );

		// 3/ Basic (negative indent)
		$text = 'Text' . PHP_EOL . ' Text' . PHP_EOL . '       Text' . PHP_EOL . PHP_EOL . ' ';
		$min_indent = -1;
		$expected = $text;
		$result = FormatterRst::fix_indentation( $text, $min_indent );
		self::assertEquals( $expected, $result, 'test_fixindentation_3' );

		// 4/ Basic (positive indent)
		$text = 'Text' . PHP_EOL . ' Text' . PHP_EOL . '       Text' . PHP_EOL . PHP_EOL . ' ';
		$min_indent = 1;
		$expected = '   Text' . PHP_EOL . '   Text' . PHP_EOL . '       Text' . PHP_EOL . '' . PHP_EOL . ' ';
		$result = FormatterRst::fix_indentation( $text, $min_indent );
		self::assertEquals( $expected, $result, 'test_fixindentation_4' );
	}

	/**
	 * Test of function `format`.
	 *
	 * @return void
	 */
	public function testFormat(): void {
		// 1/ Empty.
		$title = '';
		$hierarchy = array();
		$result = FormatterRst::format( $title, $hierarchy );
		self::assertNotEmpty( trim( $result ), 'test_format_1' );

		// 2/ Basic.
		$title = 'T1tLe';
		$hierarchy = array();
		$expected = 'T1tLe' . PHP_EOL . '=====' . str_repeat( PHP_EOL, 4 ) . '..';
		$result = FormatterRst::format( $title, $hierarchy );
		self::assertTrue( str_starts_with( $result, $expected ), 'test_format_2' );

		// 3/ With hierarchy.
		$title = 'T1tLe';
		$hierarchy = array(
			array(
				'docblock' => '/** */',
				'type' => 'attr',
				'name' => 'AttRn4me',
			),
		);
		$expected = 'T1tLe' . PHP_EOL . '=====' . PHP_EOL . PHP_EOL . '.. php:attr:: AttRn4me' . str_repeat( PHP_EOL, 5 );
		$result = FormatterRst::format( $title, $hierarchy );
		self::assertTrue( str_starts_with( $result, $expected ), 'test_format_3' );
	}
}
