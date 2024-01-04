<?php
/**
 * File for class Tokenizer.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

/**
 * The Tokenizer class creates tokens list for PHP files.
 *
 * @psalm-type Token = array{name:string,content:string}
 * @psalm-type TokensList = array<Token>
 */
class Tokenizer {
	/**
	 * Reads a PHP file and returns an array of tokens of that file.
	 *
	 * @param string $file File.
	 *
	 * @return TokensList|false
	 */
	public static function tokenize_file( string $file ): array|false {
		if ( file_exists( $file ) !== true ) {
			return false;
		}

		$file_content = file_get_contents( $file );
		if ( false === $file_content ) {
			return false;
		}

		$tokens = token_get_all( $file_content );
		$tokens = array_filter(
			$tokens,
			function ( $value ) {
				return is_array( $value ) !== true || 'T_WHITESPACE' !== token_name( intval( $value[0] ) );
			}
		);
		/**
		 * Hey.
		 *
		 * @var TokensList
		 */
		$newtokens = array();
		foreach ( $tokens as $one_token ) {
			if ( is_array( $one_token ) !== true ) {
				$newtokens[] = array(
					'name' => 'string',
					'content' => $one_token,
				);
				continue;
			}
			$newtokens[] = array(
				'name' => token_name( intval( $one_token[0] ) ),
				'content' => $one_token[1],
			);
		}
		return $newtokens;
	}
}
