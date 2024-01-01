<?php
/**
 * File for class RegexBuilder.
 *
 * @package Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

use Exception;

/**
 * Class for regex building features.
 */
class RegexBuilder {
	public const RE_URL = '(?:(?:[A-Za-z]{3,9}:(?:\/\/)?)(?:[-;:&=\+\$,\w]+@)?[A-Za-z0-9.-]+|(?:www.|[-;:&=\+\$,\w]+@)[A-Za-z0-9.-]+)(?:(?:\/[\+~%\/.\w_-]*)?\??(?:[-\+=&;%@.\w_]*)#?(?:[.\!\/\\w]*))?';
	public const RE_EMAIL = '';
	public const RE_FILEPATH = '';
	public const RE_ACCESS = 'private|protected|public';
	public const RE_AUTHOR = '^\s*(?<name>.*)[\t ]+<(?<email>.*)>\s*$';
	public const RE_FIELD = '[^\r\n ]+';
	public const RE_FIELD_SEPARATOR = '[\t ]+';
	public const RE_REST = '.*(?:[\r\n]+[\t ]+\*[\t ]+.*)*';
	public const RE_MULTILINE_TRASH = '[\r\n]+[\t ]*\*[\t ]*'; // /gm
	public const RE_ANY = '.+';
	public const RE_TYPENAMEDESC = '';
	public const RE_PATTERN_TND = 'TND';
	public const RE_PATTERN_TFD = 'TFD';
	public const RE_PATTERN_REST = 'REST';
	public const RE_PATTERN_AUTHOR = 'AUTHOR';
	public const RE_PATTERN_ACCESS = 'ACCESS';
	public const RE_PATTERN_TD = 'TD';
	public const RE_PATTERN_UD = 'UD';

	/**
	 * Undocumented function
	 *
	 * @param string $content Content.
	 *
	 * @return string
	 */
	public static function optional( string $content ): string {
		return self::group( null, $content );
	}

	/**
	 * Undocumented function
	 *
	 * @param string $pattern Pattern.
	 *
	 * @return string
	 */
	public static function pattern( string $pattern ): string {
		if ( self::RE_PATTERN_TND === $pattern ) {
			return '/^(?<type>[^\r\n ]+)(?:[\t ]+(?<name>\$[^\r\n ]+))?(?:[\t ]+(?<desc>.*))?/s';
			// return '/^' .
			// self::group( 'type', self::RE_FIELD ) .
			// self::optional(
			// self::RE_FIELD_SEPARATOR .
			// self::group( 'name', '\$' . self::RE_FIELD )
			// ) .
			// self::RE_FIELD_SEPARATOR .
			// self::group( 'desc', self::RE_ANY ) .
			// '/ms';.
		}

		if ( self::RE_PATTERN_TFD === $pattern ) {
			return '/^' .
				self::group( 'type', self::RE_FIELD ) .
				self::optional(
					self::RE_FIELD_SEPARATOR .
					self::group( 'name', self::RE_FIELD . '\(\)' )
				) .
				self::RE_FIELD_SEPARATOR .
				self::group( 'desc', self::RE_ANY ) .
				'/ms';
		}

		if ( self::RE_PATTERN_REST === $pattern ) {
			return '/' .
				self::group( 'desc', '.*(?:[\r\n]+[\t ]+\*[\t ]+.*)*' ) .
				'/s';
		}

		if ( self::RE_PATTERN_AUTHOR === $pattern ) {
			return '/' . self::RE_AUTHOR . '/';
		}

		if ( self::RE_PATTERN_ACCESS === $pattern ) {
			return '/' . self::group( 'access', self::RE_ACCESS ) . '/';
		}

		if ( self::RE_PATTERN_TD === $pattern ) {
			return '/^\s*' .
				self::group( 'type', self::RE_FIELD ) .
				self::group(
					null,
					'[\t ]*' .
					self::group( 'desc', '.*' )
				) .
				'?/ms';
		}

		if ( self::RE_PATTERN_UD === $pattern ) {
			return '/^\s*' .
				self::group( 'url', self::RE_URL ) . '?' .
				self::group( null, '[\t]*' . self::group( 'desc', '.*' ) ) .
				'?$/';
		}

		return $pattern;
	}

	/**
	 * Undocumented function
	 *
	 * @param string|null $name    Name.
	 * @param string      $content Content.
	 *
	 * @return string
	 */
	public static function group( ?string $name, string $content ): string {
		return ( is_string( $name ) ? '(?<' . $name . '>' : '(?:' ) . $content . ')';
	}

	/**
	 * Undocumented function
	 *
	 * @param string ...$input Input.
	 *
	 * @return string
	 */

	/*
	Ppublic static function parse( string ...$input ): string {
		$res = '';
		foreach ( $input as $one_input ) {
			$res .= $one_input;
		}
		return $res;
	}
	*/
}
