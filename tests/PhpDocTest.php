<?php
/**
 * PhpDoc Test.
 *
 * @package Fejz.
 */

declare(strict_types=1);

namespace TMD\Documentation\Tests;

use PHPUnit\Framework\TestCase;
use TMD\Documentation\PhpDoc;

/**
 * Undocumented class
 */
final class PhpDocTest extends BaseTest {
	/**
	 * Undocumented variable
	 *
	 * @var PhpDoc|null
	 */
	private ?PhpDoc $phpdoc = null;

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	protected function setUp(): void {
		$this->phpdoc = new PhpDoc();
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	protected function tearDown(): void {
		unset( $this->phpdoc );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	// public function testDocblockToRst(): void {
	// if ( is_a( $this->phpdoc, PhpDoc::class ) !== true ) {
	// self::fail( 'test_docblocktorst_0' );
	// return;
	// }
	// // 1/ Empty.
	// $input = '';
	// $expected = PHP_EOL;
	// $result = PhpDoc::docblock_to_rst( $input );
	// self::assertEquals( $expected, $result, 'test_docblocktorst_1' );
	// }.

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function testReplace(): void {
		if ( is_a( $this->phpdoc, PhpDoc::class ) !== true ) {
			self::fail( 'test_replace_0' );
			return;
		}

		// N/ XXX.
		$input = '';
		$params = array();
		$expected = '';
		$result = $this->phpdoc->replace( $input, $params );
		self::assertEquals( $expected, $result, 'test_replace_N' );

		// N/ XXX.
		$input = '1nPuT';
		$params = array();
		$expected = '1nPuT';
		$result = $this->phpdoc->replace( $input, $params );
		self::assertEquals( $expected, $result, 'test_replace_N' );

		// N/ XXX.
		$input = 'Input: %%-thisworks-%% %%-doesnt#work-%% %%doesntwork%% %%-AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789_--%%';
		$params = array(
			'thisworks' => 'Hola!',
			'doesnt#work' => 'Oops',
			'doesntwork' => 'Yup',
			'aabbccddeeffgghhiijjkkllmmnnooppqqrrssttuuvvwwxxyyzz0123456789_-' => 'itworks!',
		);
		$expected = 'Input: Hola! %%-doesnt#work-%% %%doesntwork%% itworks!';
		$result = $this->phpdoc->replace( $input, $params );
		self::assertEquals( $expected, $result, 'test_replace_N' );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function testParseTag(): void {
		if ( is_a( $this->phpdoc, PhpDoc::class ) !== true ) {
			self::fail( 'test_XXX_0' );
			return;
		}

		// 1/ Empty.
		$input = '';
		$tag = '';
		$expected = 'Unknown or misconfigured tag ""';
		$result = $this->phpdoc->parse_tag( $input, $tag );
		self::assertEquals( $expected, $result, 'test_parsetag_1' );

		// 2/ Abstract (because it has no field).
		$input = '';
		$tag = 'abstract';
		$expected_result = true;
		$expected_value = true;
		$result = $this->phpdoc->parse_tag( $input, $tag );
		self::assertEquals( $expected_result, $result, 'test_parsetag_2a' );
		self::assertEquals( $expected_value, $this->phpdoc->data[ $tag ]['value'], 'test_parsetag_2b' );

		// 3/ Broken Abstract (because it has fields but no regex).
		$this->phpdoc->data['brokenabstract'] = array(
			'regex' => '',
			'fields' => array( 'type' ),
			'value' => false,
		);
		$input = '';
		$tag = 'brokenabstract';
		$expected_result = 'Empty regex';
		$expected_value = false;
		$result = $this->phpdoc->parse_tag( $input, $tag );
		self::assertEquals( $expected_result, $result, 'test_parsetag_3a' );
		self::assertEquals( $expected_value, $this->phpdoc->data[ $tag ]['value'], 'test_parsetag_3b' );
		unset( $this->phpdoc->data['brokenabstract'] );

		// 4/ Broken Abstract (because it has fields but no regex).
		$input = '@param string $hehehe Hehehe.';
		$tag = 'author';
		$expected_result = 'Input "@param string $hehehe Hehehe." does not match regex "/^\s*(?<name>.*)[\t ]+<(?<email>.*)>\s*$/"';
		$expected_value = array();
		$result = $this->phpdoc->parse_tag( $input, $tag );
		self::assertEquals( $expected_result, $result, 'test_parsetag_4a' );
		self::assertEquals( $expected_value, $this->phpdoc->data[ $tag ]['value'], 'test_parsetag_4b' );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function testParseDocblock(): void {
		if ( is_a( $this->phpdoc, PhpDoc::class ) !== true ) {
			self::fail( 'test_XXX_0' );
			return;
		}

		// N/ XXX.
		$docblock = '';
		$data_before = $this->phpdoc->data;
		$this->phpdoc->parse_docblock( $docblock );
		self::assertEquals( $data_before, $this->phpdoc->data, 'test_docblocktorst_N' );

		// N/ XXX.
		$docblock = '/** @abstract */';
		$data_expected = array_merge(
			array(
				'abstract' => array(
					'value' => true,
				),
			),
			$this->phpdoc->data
		);
		$this->phpdoc->parse_docblock( $docblock );
		self::assertEquals( $data_expected, $this->phpdoc->data, 'test_docblocktorst_N' );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function testParse(): void {
		if ( is_a( $this->phpdoc, PhpDoc::class ) !== true ) {
			self::fail( 'test_XXX_0' );
			return;
		}

		// N/ XXX.
		$this->phpdoc->docblock = '';
		$data_before = $this->phpdoc->data;
		$this->phpdoc->parse();
		self::assertEquals( $data_before, $this->phpdoc->data, 'test_docblocktorst_N' );
	}

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function testToString(): void {
		if ( is_a( $this->phpdoc, PhpDoc::class ) !== true ) {
			self::fail( 'test_XXX_0' );
			return;
		}

		// N/ XXX.
		$this->phpdoc->clear();
		self::assertNotEmpty( $this->phpdoc->__toString() );
	}
}
