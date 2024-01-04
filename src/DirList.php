<?php
/**
 * File for class DirList.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

/**
 * Class for directory listing features.
 *
 * @psalm-type RegexArray = list<non-empty-string>
 * @psalm-type SplFileInfoArray = list<\SplFileInfo>
 */
class DirList {
	/**
	 * Scans the given directory recursively and returns a list of {@see \SplFileInfo} objects.
	 *
	 * This example will output all PHP files from a repo that are not from composer packages.
	 *
	 *    $files = scandir_recursive('/home/dev/github/lunch-question-resolver', ['/\.php$/'], ['/vendor\//']);
	 *    // $file = array(
	 *    //   0 => SplFileInfo('/home/dev/github/lunch-question-resolver/index.php'),
	 *    //   1 => SplFileInfo('/home/dev/github/lunch-question-resolver/src/lunch-resolver.php'),
	 *    // );
	 *
	 * @param string     $directory Directory to scan.
	 * @param RegexArray $include   Array of regexes that files have to match.
	 * @param RegexArray $exclude   Array of regexes that files have to not match.
	 *
	 * @return SplFileInfoArray List of file info for all files found.
	 */
	public static function scandir_recursive( string $directory, array $include, array $exclude ): array { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		// Check if the input is an existing directory.
		if ( true !== \file_exists( $directory ) || true !== is_dir( $directory ) ) {
			return array();
		}
		// Iterate through the input directory.
		$res = array();
		foreach ( new \DirectoryIterator( $directory ) as $file_info ) {
			// Ignore dot files `.` and `..`.
			if ( $file_info->isDot() ) {
				continue;
			}
			// If the full path of the current file matches any of the exclude regexes, ignore this file.
			$bool = false;
			foreach ( $exclude as $one_exclude ) {
				if ( @preg_match( $one_exclude, $file_info->getRealPath() ) === 1 ) {
					$bool = true;
					break;
				}
			}
			if ( $bool ) {
				continue;
			}
			// If the file is directory, scan through it.
			if ( $file_info->isDir() ) {
				$res_tmp = self::scandir_recursive( $file_info->getRealPath(), $include, $exclude );
				$res = array_merge( $res, $res_tmp );
				continue;
			}
			// If the full path of the current file matches any of the include regexes, include the file.
			$bool = false;
			foreach ( $include as $one_include ) {
				if ( @preg_match( $one_include, $file_info->getRealPath() ) === 1 ) {
					$bool = true;
					break;
				}
			}
			if ( $bool ) {
				// $res[] = $file_info->getPathname();
				$res[] = $file_info->getFileInfo();
			}
		}
		return $res;
	}
}
