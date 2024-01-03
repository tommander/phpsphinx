<?php
/**
 * File for interface FormatterInterface.
 *
 * @package Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation\Interfaces;

use TMD\Documentation\Formatters\{FormatterRst, FormatterHtml};

/**
 * The FormatterInterface represents an abstract formatter of code hierarchy.
 *
 * @psalm-import-type CodeHierarchy from \TMD\Documentation\DocblockExtract
 */
interface FormatterInterface {
	/**
	 * Hello.
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
	 * Undocumented function
	 *
	 * @param string        $title     Title.
	 * @param CodeHierarchy $hierarchy Hierarchy.
	 * @param string        $commit    Commit.
	 * @param string        $file_rel  File rel.
	 *
	 * @return string
	 */
	public static function format( string $title, array $hierarchy, string $commit = '', string $file_rel = '' ): string; // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint

	/**
	 * Undocumented function
	 *
	 * @param string $date   Date.
	 * @param string $commit Commit.
	 * @param string $file   File.
	 *
	 * @return string
	 */
	public static function generated_automatically( string $date, string $commit, string $file ): string;

	/**
	 * Undocumented function
	 *
	 * @param string $text         Text.
	 * @param string $before_toc   Before ToC.
	 * @param string $after_toc    After ToC.
	 * @param string $start_of_toc Start of ToC.
	 * @param string $end_of_toc   End of ToC.
	 *
	 * @return string
	 */
	public static function substitute( string $text, string $before_toc, string $after_toc, string $start_of_toc, string $end_of_toc ): string;

	/**
	 * Undocumented function
	 *
	 * @param string $title Title.
	 *
	 * @return string
	 */
	public static function get_empty_index( string $title = '' ): string;

	/**
	 * Undocumented function
	 *
	 * @param string $what            What.
	 * @param string $subfolder_index Subfolder index.
	 *
	 * @return string
	 */
	public static function add_to_index( string $what, string $subfolder_index ): string;

	/**
	 * Returns a representation of this instance of PhpDoc in a specific format.
	 *
	 * @param \TMD\Documentation\PhpDoc $phpdoc PHPDoc.
	 *
	 * @return string
	 */
	public static function output_str( \TMD\Documentation\PhpDoc $phpdoc ): string;
}
