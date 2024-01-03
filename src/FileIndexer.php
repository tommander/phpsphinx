<?php
/**
 * File for class FileIndexer.
 *
 * @package Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

use TMD\Documentation\Interfaces\FormatterInterface;

/**
 * Class for directory listing features.
 */
class FileIndexer {
	/**
	 * Hello.
	 *
	 * @var array<string, list<string>>
	 */
	public array $data = array();

	/**
	 * Undocumented function
	 *
	 * @return void
	 */
	public function start(): void {
		$this->data = array();
		$this->data['.'] = array();
	}

	/**
	 * Undocumented function
	 *
	 * @param string $index Index.
	 * @param string $file  File.
	 *
	 * @return void
	 */
	public function add( string $index, string $file ): void {
		if ( array_key_exists( $index, $this->data ) !== true ) {
			$this->data[ $index ] = array();
		}
		$this->data[ $index ][] = $file;
	}

	/**
	 * Undocumented function
	 *
	 * @param class-string<FormatterInterface> $formatter_intf FormatterInterface.
	 * @param array<string>                    $namespace_list Namespace list.
	 * @param string                           $commit         Commit.
	 * @param bool                             $dry_run        Dry run.
	 * @param string                           $outputdir      Outputdir.
	 * @param string                           $format         Format.
	 *
	 * @return void
	 */
	public function finish( string $formatter_intf, array $namespace_list, string $commit, bool $dry_run, string $outputdir, string $format ): void { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		foreach ( $this->data as $index_file => &$index_subfiles ) {
			$index_content = $formatter_intf::get_empty_index( ( '' === $index_file || '.' === $index_file ) ? 'API' : $index_file );
			$before_toc = '';
			$after_toc = $formatter_intf::generated_automatically( gmdate( 'c' ), Helper::make_string( $commit ), '' );

			if ( '.' === $index_file ) {
				foreach ( $namespace_list as $one_namespace ) {
					$index_content = $formatter_intf::add_to_index( Helper::fix_filename( $one_namespace ) . '/index', $index_content );
				}
			}
			sort( $index_subfiles );
			foreach ( $index_subfiles as $index_subfile ) {
				$index_content = $formatter_intf::add_to_index( Helper::fix_filename( $index_subfile ), $index_content );
			}

			$index_content = $formatter_intf::substitute( $index_content, $before_toc, $after_toc, '', '' );

			if ( true !== $dry_run ) {
				if ( '.' === $index_file || '' === $index_file ) {
					file_put_contents( Helper::make_path( $outputdir, 'index' . FormatterInterface::FORMATS[ $format ]['ext'] ), $index_content );
				} else {
					file_put_contents( Helper::make_path( $outputdir, Helper::fix_filename( $index_file ), 'index' . FormatterInterface::FORMATS[ $format ]['ext'] ), $index_content );
				}
			}
		}
	}
}
