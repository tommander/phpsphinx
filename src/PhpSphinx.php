<?php
/**
 * Undocumented.
 *
 * @package Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

/**
 * The PhpSphinx class is glueing together features to extract and render inline documentation from PHP source files.
 *
 * @psalm-type FileIndex = array<string, array{content: string, filename: string, hierarchy: array, tokens: array}>
 */
class PhpSphinx {
	/**
	 * Undocumented variable
	 *
	 * @var string
	 */
	public static string $help_text = <<<EOS
	Transforms PHP class files docblocks to restructuredText files.

	Usage:
	  phpsphinx.php --inputdir=<dir> --outputdir=<dir> [--format=<format>]
	  phpsphinx.php -h | --help
	  phpsphinx.php --version

	Options:
	  -h --help           Show this screen.
	  --version           Show version.
	  --inputdir=<dir>    Existing input directory with PHP files.
	  --outputdir=<dir>   Existing output directory for documentation files.
	  --format=<format>   Output files format.
	
	Values:
	  dir      File path.
	  format   Output file format. One of: rst,md,html.
	EOS;
	/**
	 * Undocumented variable
	 *
	 * @var string
	 */
	public static string $version_text = 'v0.1.0';
	/**
	 * Undocumented variable
	 *
	 * @var string
	 */
	public static string $name_text = 'PHPSphinx';

	/**
	 * Undocumented variable
	 *
	 * @var DirList
	 */
	private DirList $dir_list;
	/**
	 * Undocumented variable
	 *
	 * @var DocblockExtract
	 */
	private DocblockExtract $docblock_extract;
	/**
	 * Undocumented variable
	 *
	 * @var Parameters
	 */
	private Parameters $parameters;
	/**
	 * Undocumented variable
	 *
	 * @var Tokenizer
	 */
	private Tokenizer $tokenizer;
	/**
	 * Undocumented variable
	 *
	 * @var Formatter
	 */
	private Formatter $formatter;

	/**
	 * Undocumented function
	 */
	public function __construct() {
		$this->dir_list = new DirList();
		$this->docblock_extract = new DocblockExtract();
		$this->parameters = new Parameters();
		$this->tokenizer = new Tokenizer();
		$this->formatter = new Formatter();
	}

	/**
	 * Undocumented function
	 *
	 * @param string $text Text.
	 *
	 * @return void
	 */
	private function writeln( string $text ): void {
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $text . PHP_EOL;
	}

	/**
	 * Undocumented function
	 *
	 * @return bool
	 */
	public function pre_param_check(): bool {
		// No options => show help.
		if ( count( $this->parameters->params ) === 0 ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo self::$help_text . PHP_EOL;
			return false;
		}

		// Help option => show help.
		if ( array_key_exists( 'h', $this->parameters->params ) === true || array_key_exists( 'help', $this->parameters->params ) === true ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo self::$help_text . PHP_EOL;
			return false;
		}

		// Version option => show version.
		if ( array_key_exists( 'version', $this->parameters->params ) === true ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo self::$name_text . ' ' . self::$version_text . PHP_EOL;
			return false;
		}

		return true;
	}

	/**
	 * Undocumented function
	 *
	 * @param array|null $opts_override Opts override.
	 * @param bool       $dry_run       Dry run.
	 *
	 * @return void
	 */
	public function do_run( array|null $opts_override = null, bool $dry_run = false ): void {
		// Get program parameters.
		$res_getparams = $this->parameters->prepare_params(
			$opts_override,
			array( $this, 'pre_param_check' )
		);
		if ( is_string( $res_getparams ) === true ) {
			$this->writeln( $res_getparams );
			return;
		}
		if ( true !== $res_getparams ) {
			return;
		}

		$format = Helper::make_string( $this->parameters->params['format'] );
		$inputdir = Helper::make_string( $this->parameters->params['inputdir'] );
		$outputdir = Helper::make_string( $this->parameters->params['outputdir'] );

		$this->run( $format, $inputdir, $outputdir, $dry_run );
	}

	/**
	 * Undocumented function
	 *
	 * @param string $format    Format.
	 * @param string $inputdir  Inputdir.
	 * @param string $outputdir Outputdir.
	 * @param bool   $dry_run   Dry run.
	 *
	 * @return void
	 */
	public function run( string $format, string $inputdir, string $outputdir, bool $dry_run = false ): void {
		if ( '' === $inputdir || file_exists( $inputdir ) !== true ) {
			$this->writeln( '[ERROR] Input directory "' . $inputdir . '" does not exist.' );
			return;
		}
		if ( '' === $outputdir || file_exists( $outputdir ) !== true ) {
			$this->writeln( '[ERROR] Output directory "' . $outputdir . '" does not exist.' );
			return;
		}
		if ( '' === $format || in_array( $format, array_keys( Formatter::FORMATS ), true ) !== true ) {
			$this->writeln( '[ERROR] Format "' . $format . '" is unknown. Correct is one of (' . implode( ', ', array_keys( Formatter::FORMATS ) ) . ').' );
			return;
		}

		$namespace_list = array();
		$index_files = array(
			'.' => array(),
		);

		// Recursive dir scan.
		$this->writeln( 'Searching for PHP source files...' );
		$phpfile_list = $this->dir_list->scandir_recursive( $inputdir, array( '/\.php$/' ), array( '/vendor\//', '/\.asset\.php$/', '/\/tests\/Test/' ) );
		$this->writeln( 'Found ' . strval( count( $phpfile_list ) ) . ' files.' );
		foreach ( $phpfile_list as $phpfile_info ) {
			$phpfile_full = $phpfile_info->getPathname();
			$phpfile_name = $phpfile_info->getFilename();

			$this->writeln( 'Processing "' . $phpfile_full . '"...' );

			// Tokenize content.
			$this->writeln( '   Tokenizing...' );
			$tokens = $this->tokenizer->tokenize_file( $phpfile_full );
			if ( false === $tokens ) {
				$this->writeln( '   [WARNING] File cannot be tokenized.' );
				continue;
			}

			// Create code hierarchy and collect namespaces.
			$this->writeln( '   Creating code hierarchy...' );
			$namespace = '';
			$hierarchy = $this->docblock_extract->get_code_hierarchy( $tokens, $phpfile_name, $namespace );
			if ( '' !== $namespace && in_array( $namespace, $namespace_list, true ) !== true ) {
				$namespace_list[] = $namespace;
			}

			// Format code map.
			$this->writeln( '   Formatting code map...' );
			$outfile_content = $this->formatter->call_func( $format, 'format', $phpfile_name, $hierarchy );

			// Name output file and create output file path.
			$this->writeln( '   Creating file name...' );
			$outfile_ext = $this->formatter->get_format_ext( $format );
			if ( false === $outfile_ext ) {
				$this->writeln( '[WARNING] Format "' . $format . '" does not have an extension.' );
				continue;
			}
			$outfile_name = Helper::make_path( Helper::fix_filename( $namespace ), Helper::fix_filename( $phpfile_name ) . $outfile_ext );
			$outfile_path = Helper::make_path( $outputdir, Helper::fix_filename( $namespace ) );
			if ( file_exists( $outfile_path ) !== true ) {
				mkdir( $outfile_path, 0755, true );
			}

			// Add file to index.
			$this->writeln( '   Adding file to index...' );
			$index_key = ( ( '' === $namespace ) ? '.' : addslashes( $namespace ) );
			if ( array_key_exists( $index_key, $index_files ) !== true ) {
				$index_files[ $index_key ] = array();
			}
			$index_files[ $index_key ][] = Helper::fix_filename( $phpfile_name );

			// Save to file.
			$this->writeln( '   File will be saved to "' . $outfile_name . '"' );
			if ( '' !== $outfile_content && true !== $dry_run ) {
				$outfile = Helper::make_path( $outputdir, $outfile_name );
				file_put_contents( $outfile, $outfile_content );
			}
			if ( count( $hierarchy ) > 0 && true !== $dry_run ) {
				$outfile = Helper::make_path( $outputdir, $outfile_name . '.hierarchy.json' );
				file_put_contents( $outfile, json_encode( $hierarchy, JSON_PRETTY_PRINT ) );
			}
			if ( count( $tokens ) > 0 && true !== $dry_run ) {
				$outfile = Helper::make_path( $outputdir, $outfile_name . '.tokens.json' );
				file_put_contents( $outfile, json_encode( $tokens, JSON_PRETTY_PRINT ) );
			}

			$this->writeln( '   Done.' );
		}

		$this->writeln( 'Saving index files...' );
		foreach ( $index_files as $index_file => $index_subfiles ) {
			$index_content = $this->formatter->call_func( $format, 'get_empty_subfolder_index', ( '' === $index_file || '.' === $index_file ) ? 'API' : $index_file );
			foreach ( $index_subfiles as $index_subfile ) {
				$index_content = $this->formatter->call_func( $format, 'add_to_subfolder_index', Helper::fix_filename( $index_subfile ), $index_content );
			}
			if ( true !== $dry_run ) {
				if ( '.' === $index_file || '' === $index_file ) {
					foreach ( $namespace_list as $one_namespace ) {
						$index_content = $this->formatter->call_func( $format, 'add_to_subfolder_index', Helper::fix_filename( $one_namespace ) . '/index', $index_content );
					}
					file_put_contents( Helper::make_path( $outputdir, 'index.rst' ), $index_content );
				} else {
					file_put_contents( Helper::make_path( $outputdir, Helper::fix_filename( $index_file ), 'index.rst' ), $index_content );
				}
			}
		}

		$this->writeln( 'Script finished.' );
	}
}
