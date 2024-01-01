<?php
/**
 * Formatter Test.
 *
 * @package Fejz.
 */

declare(strict_types=1);

namespace TMD\Documentation\Tests;

use PHPUnit\Framework\TestCase;
use TMD\Documentation\Formatter;

/**
 * Undocumented class
 */
final class FormatterTest extends BaseTest {
	/**
	 * Undocumented variable
	 *
	 * @var Formatter|null
	 */
	private ?Formatter $formatter = null;

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	protected function setUp(): void {
		$this->formatter = new Formatter();
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		unset( $this->formatter );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function testFormatExists(): void {
		// public static function format( string $title, array $hierarchy ): string {.
		if ( is_a( $this->formatter, Formatter::class ) !== true ) {
			self::fail( 'test_formatexists_0' );
			return;
		}

		// 1/ Empty.
		$format = '';
		$expected = false;
		$result = $this->formatter->format_exists( $format );
		self::assertEquals( $expected, $result, 'test_formatexists_1' );

		// 2/ Exists.
		$formats = array( 'rst', 'md', 'html' );
		$expected = true;
		$result = true;
		foreach ( $formats as $format ) {
			$result = $result && $this->formatter->format_exists( $format );
		}
		self::assertEquals( $expected, $result, 'test_formatexists_2' );

		// 3/ Unknown.
		$formats = array( 'xml', 'txt', 'pdf' );
		$expected = false;
		$result = true;
		foreach ( $formats as $format ) {
			$result = $result && $this->formatter->format_exists( $format );
		}
		self::assertEquals( $expected, $result, 'test_formatexists_3' );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function testGetFormatExt(): void {
		// public static function format( string $title, array $hierarchy ): string {.
		if ( is_a( $this->formatter, Formatter::class ) !== true ) {
			self::fail( 'test_getformatext_0' );
			return;
		}

		// 1/ Empty.
		$format = '';
		$expected = '';
		$result = $this->formatter->get_format_ext( $format );
		self::assertEquals( $expected, $result, 'test_getformatext_1' );

		// 2/ Exists.
		$format = 'md';
		$expected = '.md';
		$result = $this->formatter->get_format_ext( $format );
		self::assertEquals( $expected, $result, 'test_getformatext_2' );

		// 3/ Unknown.
		$format = 'pdf';
		$expected = '';
		$result = $this->formatter->get_format_ext( $format );
		self::assertEquals( $expected, $result, 'test_getformatext_3' );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function testGetFormatClass(): void {
		// public static function format( string $title, array $hierarchy ): string {.
		if ( is_a( $this->formatter, Formatter::class ) !== true ) {
			self::fail( 'test_format_0' );
			return;
		}

		// 1/ Empty.
		$format = '';
		$expected = '';
		$result = $this->formatter->get_format_class( $format );
		self::assertEquals( $expected, $result, 'test_format_1' );

		// 2/ Exists.
		$format = 'html';
		$expected = \TMD\Documentation\FormatterRst::class;
		$result = $this->formatter->get_format_class( $format );
		self::assertEquals( $expected, $result, 'test_format_2' );

		// 3/ Unknown.
		$format = 'pdf';
		$expected = false;
		$result = $this->formatter->get_format_class( $format );
		self::assertEquals( $expected, $result, 'test_format_3' );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function testFormatHierarchy(): void {
		// public static function format( string $title, array $hierarchy ): string {.
		if ( is_a( $this->formatter, Formatter::class ) !== true ) {
			self::fail( 'test_formathierarchy_0' );
			return;
		}

		// 1/ Empty.
		$format = '';
		$title = '';
		$hierarchy = array();
		$expected = '';
		$result = $this->formatter->call_func( $format, 'format', $title, $hierarchy );
		self::assertEquals( $expected, $result, 'test_formathierarchy_1' );

		// 2/ Exists.
		$format = 'rst';
		$title = 'T1tLe';
		$hierarchy = array(
			array(
				'docblock' => '/** */',
				'type' => 'attr',
				'name' => 'AttRn4me',
			),
		);
		$expected = 'T1tLe' . PHP_EOL . '=====' . PHP_EOL . PHP_EOL . '.. php:attr:: AttRn4me' . PHP_EOL . PHP_EOL . PHP_EOL;
		$result = $this->formatter->call_func( $format, 'format', $title, $hierarchy );
		self::assertEquals( $expected, $result, 'test_formathierarchy_2' );

		// 3/ Unknown.
		$format = 'pdf';
		$title = 'T1tLe';
		$hierarchy = array(
			array(
				'docblock' => '/** */',
				'type' => 'attr',
				'name' => 'AttRn4me',
			),
		);
		$expected = '';
		$result = $this->formatter->call_func( $format, 'format', $title, $hierarchy );
		self::assertEquals( $expected, $result, 'test_formathierarchy_3' );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function testFormatIndexFile(): void {
		// public static function format( string $title, array $hierarchy ): string {.
		if ( is_a( $this->formatter, Formatter::class ) !== true ) {
			self::fail( 'test_format_0' );
			return;
		}

		// 1/ Empty.
		$format = '';
		$files_index = array();
		$title_text = '';
		$caption = '';
		$expected = '';
		$result = $this->formatter->call_func( $format, 'index_file', $files_index, $title_text, $caption );
		self::assertEquals( $expected, $result, 'test_format_1' );

		// 2/ Unknown.
		$format = 'pdf';
		$files_index = array();
		$title_text = 'T1tl3';
		$caption = 'C4pTi0n';
		$expected = '';
		$result = $this->formatter->call_func( $format, 'index_file', $files_index, $title_text, $caption );
		self::assertEquals( $expected, $result, 'test_format_1' );

		// 3/ Unknown.
		$format = 'rst';
		// phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
		$files_index = array(
			'a' => array( 'filename' => 'Abcd', 'content' => '', 'hierarchy' => array(), 'tokens' => array() ),
			'b' => array( 'filename' => 'Efgh', 'content' => '', 'hierarchy' => array(), 'tokens' => array() ),
		);
		// phpcs:enable
		$title_text = 'T1tl3';
		$caption = 'C4pTi0n';
		$expected = <<<EOS
		T1tl3
		=====
		
		.. toctree::
		   :caption: C4pTi0n:
		
		   Abcd
		   Efgh
		
		EOS;
		$result = $this->formatter->call_func( $format, 'index_file', $files_index, $title_text, $caption );
		self::assertEquals( $expected, $result, 'test_format_1' );
	}
}
