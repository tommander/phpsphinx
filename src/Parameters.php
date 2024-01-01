<?php
/**
 * File for class Parameters.
 *
 * @package Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

/**
 * The Parameters class prepares program parameters.
 */
class Parameters {
	/**
	 * Undocumented variable
	 *
	 * @var string
	 */
	public string $short_params = 'h';
	/**
	 * Undocumented variable
	 *
	 * @var array
	 */
	public array $long_params = array(
		'inputdir:',
		'outputdir:',
		'format:',
		'help',
		'version',
	);
	/**
	 * Undocumented variable
	 *
	 * @var array
	 */
	public array $params = array();

	/**
	 * Undocumented function
	 *
	 * @param array|null $opts_override Opts override.
	 * @param callable   $before        Before.
	 *
	 * @return bool|string
	 */
	public function prepare_params( array|null $opts_override = null, callable $before = null ): bool|string {
		// Parse script options.
		if ( is_array( $opts_override ) === true ) {
			$this->params = $opts_override;
		} else {
			$this->params = getopt( $this->short_params, $this->long_params );
		}

		if ( $before ) {
			$res = call_user_func( $before );
			if ( true !== $res ) {
				return false;
			}
		}

		// Inputdir option not specified => end.
		if ( array_key_exists( 'inputdir', $this->params ) !== true ) {
			return '[ERROR] Input directory not specified.';
		}

		// Outputdir option not specified => end.
		if ( array_key_exists( 'outputdir', $this->params ) !== true ) {
			return '[ERROR] Output directory not specified.';
		}

		if ( array_key_exists( 'format', $this->params ) !== true ) {
			$this->params['format'] = 'rst';
		}

		$input_directory = Helper::make_string( $this->params['inputdir'] );
		$output_directory = Helper::make_string( $this->params['outputdir'] );
		$format = Helper::make_string( $this->params['format'] ?? 'rst' );

		// Input dir path empty or does not exist => end.
		if ( '' === $input_directory || file_exists( $input_directory ) !== true ) {
			return '[ERROR] Input directory "' . $input_directory . '" is empty or does not exist.';
		}

		// Output dir path empty or does not exist => end.
		if ( '' === $output_directory || file_exists( $output_directory ) !== true ) {
			return '[ERROR] Output directory "' . $output_directory . '" is empty or does not exist.';
		}

		if ( in_array( $format, array_keys( Formatter::FORMATS ), true ) !== true ) {
			return '[ERROR] Unknown format "' . $format . '". Known formats are "' . implode( '", "', array_keys( Formatter::FORMATS ) ) . '".';
		}

		return true;
	}
}
