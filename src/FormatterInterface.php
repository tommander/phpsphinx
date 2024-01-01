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
 * @psalm-import-type CodeHierarchy from DocblockExtract
 */
interface FormatterInterface {
	/**
	 * Undocumented function
	 *
	 * @param string        $title     Title.
	 * @param CodeHierarchy $hierarchy Hierarchy.
	 *
	 * @return string
	 */
	public static function format( string $title, array $hierarchy ): string; // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint

	/**
	 * Undocumented function
	 *
	 * @param string $title Title.
	 *
	 * @return string
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function get_empty_subfolder_index( string $title = '' ): string;

	/**
	 * Undocumented function
	 *
	 * @param string $title Title.
	 *
	 * @return string
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function get_empty_api_index( string $title = '' ): string;

	/**
	 * Undocumented function
	 *
	 * @param string $what            What.
	 * @param string $subfolder_index Subfolder index.
	 *
	 * @return string
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function add_to_subfolder_index( string $what, string $subfolder_index ): string;

	/**
	 * Undocumented function
	 *
	 * @param string $what      What.
	 * @param string $api_index Subfolder index.
	 *
	 * @return string
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public static function add_to_api_index( string $what, string $api_index ): string;
}
