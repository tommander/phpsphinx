<?php
/**
 * File for class Helper.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

/**
 * Class for directory listing features.
 */
class Helper {
	/**
	 * Stringify the input.
	 *
	 * @param mixed $input Input.
	 *
	 * @return string
	 */
	public static function make_string( mixed $input ): string {
		if ( is_string( $input ) ) {
			return $input;
		}
		if ( is_null( $input ) || is_object( $input ) || is_scalar( $input ) ) {
			return strval( $input );
		}
		return '';
	}

	/**
	 * Glue each element together into a path.
	 *
	 * @param string ...$path_elements Path elements.
	 *
	 * @return string
	 */
	public static function make_path( string ...$path_elements ): string {
		$dir_sep = DIRECTORY_SEPARATOR;
		$ret = '';
		foreach ( $path_elements as $path_element ) {
			if ( '' === $ret ) {
				$ret = $path_element;
				continue;
			}
			if ( str_starts_with( $path_element, $dir_sep ) !== true && str_ends_with( $ret, $dir_sep ) !== true ) {
				$ret .= $dir_sep . $path_element;
				continue;
			}
			if ( str_starts_with( $path_element, $dir_sep ) === true && str_ends_with( $ret, $dir_sep ) === true ) {
				$ret = substr( $ret, 0, -1 ) . $path_element;
				continue;
			}
			$ret .= $path_element;
		}
		return $ret;
	}

	/**
	 * Retrieve filename from the path.
	 *
	 * @param string $path Path.
	 *
	 * @return string
	 */
	public static function get_filename( string $path ): string {
		$path_arr = explode( DIRECTORY_SEPARATOR, $path );
		$res = array_pop( $path_arr );
		return $res;
	}

	/**
	 * Return relative path between two sources.
	 *
	 * @param string           $from      From.
	 * @param string           $to        To.
	 * @param non-empty-string $separator Separator.
	 * @return string
	 * @link https://stackoverflow.com/a/51874346/5098639
	 */
	public static function relative_path( string $from, string $to, string $separator = DIRECTORY_SEPARATOR ): string {
		$from   = str_replace( array( '/', '\\' ), $separator, $from );
		$to     = str_replace( array( '/', '\\' ), $separator, $to );

		$ar_from = explode( $separator, rtrim( $from, $separator ) );
		$ar_to = explode( $separator, rtrim( $to, $separator ) );
		// phpcs:ignore Squiz.PHP.DisallowSizeFunctionsInLoops.Found
		while ( count( $ar_from ) && count( $ar_to ) && ( $ar_from[0] === $ar_to[0] ) ) {
			array_shift( $ar_from );
			array_shift( $ar_to );
		}

		return str_pad( '', count( $ar_from ) * 3, '..' . $separator ) . implode( $separator, $ar_to );
	}

	/**
	 * Secure the string to be a valid filename.
	 *
	 * @param string $dirty_filename Filename.
	 *
	 * @return string
	 */
	public static function fix_filename( string $dirty_filename ): string {
		return trim(
			preg_replace(
				'/_+/',
				'_',
				preg_replace(
					'/[^A-Za-z0-9_.-]/',
					'_',
					strtolower( $dirty_filename )
				)
			),
			"_ \n\r\t\v\0"
		);
	}
}
