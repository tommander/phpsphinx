<?php
/**
 * File for interface FormatterInterface.
 *
 * @package Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

/**
 * The FormatterInterface represents an abstract formatter of code hierarchy.
 *
 * @psalm-import-type FileIndex from PhpSphinx
 */
interface FormatterWithIndexInterface extends FormatterInterface {
	/**
	 * Undocumented function
	 *
	 * @param FileIndex $files_index Files index.
	 * @param string    $title_text  Title text.
	 * @param string    $caption     Caption.
	 *
	 * @return string
	 */
	public static function index_file( array $files_index, string $title_text = 'API', string $caption = '' ): string; // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
}
