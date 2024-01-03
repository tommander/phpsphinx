<?php
/**
 * Undocumented.
 *
 * @package Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

use TMD\Documentation\Interfaces\FormatterInterface;
use TMD\Documentation\Formatters\FormatterRst;

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
	 * @var FileIndexer
	 */
	private FileIndexer $fileindexer;

	/**
	 * Undocumented function
	 */
	public function __construct() {
		$this->dir_list = new DirList();
		$this->docblock_extract = new DocblockExtract();
		$this->parameters = new Parameters();
		$this->tokenizer = new Tokenizer();
		$this->fileindexer = new FileIndexer();
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

		$this->generate_documentation( $format, $inputdir, $outputdir, $dry_run );
	}

	/**
	 * Undocumented function
	 *
	 * @return string|false
	 */
	public function get_current_commit(): string|false {
		$file_head = Helper::make_path( __DIR__, '..', '.git', 'HEAD' );
		if ( file_exists( $file_head ) !== true ) {
			return false;
		}
		$file_head_content = file_get_contents( $file_head );
		if ( preg_match( '/^(.*):\s+(.*)$/m', $file_head_content, $matches ) !== 1 ) {
			return false;
		}

		$file_ref = Helper::make_path( __DIR__, '..', '.git', $matches[2] );
		if ( file_exists( $file_ref ) !== true ) {
			return false;
		}
		$file_ref_content = file_get_contents( $file_ref );
		if ( preg_match( '/[abcdef0123456789]{40}/', $file_ref_content, $matches_ref ) !== 1 ) {
			return false;
		}

		return $matches_ref[0];
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
	public function generate_documentation( string $format, string $inputdir, string $outputdir, bool $dry_run = false ): void {
		if ( '' === $inputdir || file_exists( $inputdir ) !== true ) {
			$this->writeln( '[ERROR] Input directory "' . $inputdir . '" does not exist.' );
			return;
		}
		if ( '' === $outputdir || file_exists( $outputdir ) !== true ) {
			$this->writeln( '[ERROR] Output directory "' . $outputdir . '" does not exist.' );
			return;
		}
		if ( '' === $format || in_array( $format, array_keys( FormatterInterface::FORMATS ), true ) !== true ) {
			$this->writeln( '[ERROR] Format "' . $format . '" is unknown. Correct is one of (' . implode( ', ', array_keys( FormatterInterface::FORMATS ) ) . ').' );
			return;
		}

		$namespace_list = array();
		$this->fileindexer->start();

		/**
		 * Hello.
		 *
		 * @var class-string<FormatterInterface>
		 */
		$formatter_intf = FormatterInterface::FORMATS[ $format ]['class'];
		$commit = $this->get_current_commit();

		// Recursive dir scan.
		$this->writeln( 'Searching for PHP source files...' );
		$artifact_fileinfos = $this->dir_list->scandir_recursive( $inputdir, array( '/\.php$/' ), array( '/vendor\//', '/\.asset\.php$/', '/\/tests\/Test/' ) );
		$this->writeln( 'Found ' . strval( count( $artifact_fileinfos ) ) . ' files.' );
		foreach ( $artifact_fileinfos as $phpfile_info ) {
			$phpfile_full = $phpfile_info->getPathname();
			$phpfile_name = $phpfile_info->getFilename();

			$this->writeln( 'Processing "' . $phpfile_full . '"...' );

			// Tokenize content.
			$this->writeln( '   Tokenizing...' );
			$artifact_tokenlist = $this->tokenizer->tokenize_file( $phpfile_full );
			if ( false === $artifact_tokenlist ) {
				$this->writeln( '   [WARNING] File cannot be tokenized.' );
				continue;
			}

			// Create code hierarchy and collect namespaces.
			$this->writeln( '   Creating code hierarchy...' );
			$namespace = '';
			$artifact_codehierarchy = $this->docblock_extract->get_code_hierarchy( $artifact_tokenlist, $phpfile_name, $namespace );
			if ( '' !== $namespace && in_array( $namespace, $namespace_list, true ) !== true ) {
				$namespace_list[] = $namespace;
			}

			// Format code map.
			$this->writeln( '   Formatting code map...' );
			$file_relative = Helper::relative_path( realpath( Helper::make_path( __DIR__, '..' ) ), $phpfile_full );
			$outfile_content = $formatter_intf::format( $phpfile_name, $artifact_codehierarchy, Helper::make_string( $commit ), $file_relative );

			// Name output file and create output file path.
			$this->writeln( '   Creating file name...' );
			$outfile_ext = FormatterInterface::FORMATS[ $format ]['ext'];
			$outfile_name = Helper::make_path( Helper::fix_filename( $namespace ), Helper::fix_filename( $phpfile_name ) . $outfile_ext );
			$outfile_path = Helper::make_path( $outputdir, Helper::fix_filename( $namespace ) );
			if ( file_exists( $outfile_path ) !== true ) {
				mkdir( $outfile_path, 0755, true );
			}

			// Add file to index.
			$this->writeln( '   Adding file to index...' );
			$index_key = ( ( '' === $namespace ) ? '.' : addslashes( $namespace ) );
			$this->fileindexer->add( $index_key, Helper::fix_filename( $phpfile_name ) );

			// Save to file.
			$this->writeln( '   File will be saved to "' . $outfile_name . '"' );
			if ( true !== $dry_run ) {
				$outfile = Helper::make_path( $outputdir, $outfile_name );
				file_put_contents( $outfile, $outfile_content );
			}

			$this->writeln( '   Done.' );
		}

		$this->writeln( 'Saving index files...' );
		$this->fileindexer->finish(
			$formatter_intf,
			$namespace_list,
			Helper::make_string( $commit ),
			$dry_run,
			$outputdir,
			$format
		);

		$this->writeln( 'Script finished.' );
	}
}
