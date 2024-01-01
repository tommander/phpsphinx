<?php
/**
 * File for class Formatter.
 *
 * @package Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

/**
 * The Formatter class does things.
 *
 * @psalm-import-type FileIndex from PhpSphinx
 * @psalm-import-type CodeHierarchy from DocblockExtract
 */
class Formatter {
	public const FORMATS = array(
		'rst' => array(
			'ext' => '.rst',
			'desc' => 'reStructuredText',
			'class' => FormatterRst::class,
		),
		'md' => array(
			'ext' => '.md',
			'desc' => 'Markdown',
			'class' => FormatterRst::class,
		),
		'html' => array(
			'ext' => '.html',
			'desc' => 'HTML',
			'class' => FormatterRst::class,
		),
	);

	/**
	 * Undocumented function
	 *
	 * @param string $format Format.
	 *
	 * @return bool
	 */
	public function format_exists( string $format ): bool {
		return in_array( $format, array_keys( self::FORMATS ), true ) === true;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $format Format.
	 *
	 * @return string|false
	 */
	public function get_format_ext( string $format ): string|false {
		if ( $this->format_exists( $format ) !== true ) {
			return false;
		}
		return self::FORMATS[ $format ]['ext'];
	}

	/**
	 * Undocumented function
	 *
	 * @param string $format Format.
	 *
	 * @return string|false
	 */
	public function get_format_class( string $format ): string|false {
		if ( $this->format_exists( $format ) !== true ) {
			return false;
		}
		return self::FORMATS[ $format ]['class'];
	}

	/**
	 * Undocumented function
	 *
	 * @param string $format         Format.
	 * @param string $func_name      Function name.
	 * @param mixed  ...$func_params Function parameters.
	 *
	 * @return string
	 */
	public function call_func( string $format, string $func_name, ...$func_params ): string {
		if ( $this->format_exists( $format ) !== true ) {
			return '';
		}

		$intf = $this->get_format_class( $format );
		if ( is_a( $intf, FormatterInterface::class, true ) !== true ) {
			return '';
		}

		/**
		 * Undocumented.
		 *
		 * @var string
		 */
		$func_result = call_user_func( $intf . '::' . $func_name, ...$func_params );
		return $func_result;
	}
}
