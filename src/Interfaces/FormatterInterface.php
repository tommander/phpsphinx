<?php
/**
 * File for interface FormatterInterface.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation\Interfaces;

use TMD\Documentation\{FormatterRst, FormatterHtml};

/**
 * The FormatterInterface represents an abstract formatter of code hierarchy.
 *
 * @psalm-import-type CodeHierarchy from \TMD\Documentation\DocblockExtract
 * @psalm-import-type DocblockData from \TMD\Documentation\PhpDoc
 */
interface FormatterInterface {
	/**
	 * List of available formats (as class refs) along with some metadata.
	 *
	 * @var array<string, array{ext: string, desc: string, class: class-string<FormatterInterface>}>
	 */
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
			'class' => FormatterHtml::class,
		),
	);

	/**
	 * Return a link to a GitHub repo commit.
	 *
	 * @param string $commit Commit.
	 *
	 * @return string
	 */
	public static function commit_link( string $commit ): string;

	/**
	 * Return a link to a GitHub repo file.
	 *
	 * @param string $commit Commit.
	 * @param string $file   File.
	 *
	 * @return string
	 */
	public static function file_link( string $commit, string $file ): string;

	/**
	 * Turns a code hierarchy into a single FORMAT file.
	 *
	 * @param string        $title     Title of the document.
	 * @param CodeHierarchy $hierarchy Input code hierarchy.
	 * @param string        $commit    Current commit.
	 * @param string        $file_rel  Path to the original PHP file, relative to repo root.
	 *
	 * @return string
	 */
	public static function format( string $title, array $hierarchy, string $commit = '', string $file_rel = '' ): string; // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint

	/**
	 * Returns a "Generated Automatically" badge that is included in every file.
	 *
	 * @param string $date   Current date.
	 * @param string $commit Current commit.
	 * @param string $file   Current PHP file.
	 *
	 * @return string
	 */
	public static function generated_automatically( string $date, string $commit, string $file ): string;

	/**
	 * Substite placeholders in a template with real content.
	 *
	 * @param string $text         Subject text.
	 * @param string $before_toc   Before ToC.
	 * @param string $after_toc    After ToC.
	 * @param string $start_of_toc Start of ToC.
	 * @param string $end_of_toc   End of ToC.
	 *
	 * @return string
	 */
	public static function substitute( string $text, string $before_toc, string $after_toc, string $start_of_toc, string $end_of_toc ): string;

	/**
	 * Returns a folder index template.
	 *
	 * @param string $title Title of the document.
	 *
	 * @return string
	 */
	public static function get_empty_index( string $title = '' ): string;

	/**
	 * Adds a file reference to the index.
	 *
	 * @param string $what            What to add.
	 * @param string $subfolder_index Folder index content.
	 *
	 * @return string
	 */
	public static function add_to_index( string $what, string $subfolder_index ): string;

	/**
	 * Returns a representation of the referenced PhpDoc data in restructuredText.
	 *
	 * @param string       $description Description.
	 * @param DocblockData $data        Data.
	 *
	 * @return string
	 */
	public static function output_str( string $description, array $data ): string; // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
}
