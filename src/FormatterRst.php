<?php
/**
 * File for class FormatterRst.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

use TMD\Documentation\Helper;
use TMD\Documentation\Interfaces\FormatterInterface;
use TMD\Documentation\PhpDoc;

/**
 * The FormatterHtml class transform code hierarchy into a restructuredText.
 *
 * @psalm-import-type FileIndex from \TMD\Documentation\PhpSphinx
 * @psalm-import-type CodeHierarchy from \TMD\Documentation\DocblockExtract
 * @psalm-import-type DocblockData from \TMD\Documentation\PhpDoc
 */
class FormatterRst implements FormatterInterface {
	public const DIRECTIVE_ATTR = 'php:attr';
	public const DIRECTIVE_CASE = 'php:case';
	public const DIRECTIVE_CLASS = 'php:class';
	public const DIRECTIVE_CONST = 'php:const';
	public const DIRECTIVE_ENUM = 'php:enum';
	public const DIRECTIVE_EXCEPTION = 'php:exception';
	public const DIRECTIVE_FUNCTION = 'php:function';
	public const DIRECTIVE_GLOBAL = 'php:global';
	public const DIRECTIVE_INTERFACE = 'php:interface';
	public const DIRECTIVE_NAMESPACE = 'php:namespace';
	public const DIRECTIVE_METHOD = 'php:method';
	public const DIRECTIVE_STATICMETHOD = 'php:staticmethod';
	public const DIRECTIVE_TRAIT = 'php:trait';
	public const REFERENCE_ATTR = 'php:attr';
	public const REFERENCE_CASE = 'php:case';
	public const REFERENCE_CLASS = 'php:class';
	public const REFERENCE_CONST = 'php:const';
	public const REFERENCE_ENUM = 'php:enum';
	public const REFERENCE_EXCEPTION = 'php:exc';
	public const REFERENCE_FUNCTION = 'php:func';
	public const REFERENCE_GLOBAL = 'php:global';
	public const REFERENCE_INTERFACE = 'php:interface';
	public const REFERENCE_NAMESPACE = 'php:ns';
	public const REFERENCE_METHOD = 'php:meth';
	public const REFERENCE_TRAIT = 'php:trait';

	/**
	 * RST templates for Docblock tags.
	 *
	 * @var array<string, string>
	 */
	public const CLEAN_RST_DATA = array(
		'abstract' => ':abstract:',
		'access' => ':access: %%-access-%%',
		'author' => ':author: %%-name-%% <%%-email-%%>',
		'category' => ':category: %%-desc-%%',
		'copyright' => ':copyright: %%-desc-%%',
		'deprecated' => ':deprecated: %%-desc-%%',
		'example' => ':example: (%%-type-%%) - %%-desc-%%',
		'final' => ':final:',
		'filesource' => ':filesource:',
		'global' => ':global: %%-name-%% (%%-type-%%) - %%-desc-%%',
		'ignore' => ':ignore:',
		'internal' => ':internal: %%-desc-%%',
		'license' => ':license: (%%-url-%%) - %%-desc-%%',
		'link' => ':link: %%-desc-%%',
		'method' => ':method: %%-name-%% (%%-type-%%) - %%-desc-%%',
		'name' => ':name: %%-desc-%%',
		'package' => ':package: %%-desc-%%',
		'param' => ':param %%-type-%% %%-name-%%: %%-desc-%%',
		'property' => ':property: %%-name-%% (%%-type-%%) - %%-desc-%% READWRITE',
		'property-read' => ':property: %%-name-%% (%%-type-%%) - %%-desc-%% READ',
		'property-write' => ':property: %%-name-%% (%%-type-%%) - %%-desc-%% WRITE',
		'return' => ':returns: (%%-type-%%) - %%-desc-%%',
		'see' => ':see: %%-desc-%%',
		'since' => ':since: %%-desc-%%',
		'static' => ':static: %%-desc-%%',
		'staticvar' => ':var %%-type-%% static %%-name-%%: %%-desc-%%',
		'subpackage' => ':subpackage: %%-desc-%%',
		'todo' => ':todo: %%-desc-%%',
		'tutorial' => ':tutorial: %%-desc-%%',
		'uses' => ':uses: %%-desc-%%',
		'var' => ':var %%-type-%% %%-name-%%: %%-desc-%%',
		'version' => ':version: %%-desc-%%',
	);

	/**
	 * Do not use.
	 *
	 * @deprecated 0.0.0
	 * @psalm-suppress PossiblyUnusedMethod
	 */
	public function __construct() {
	}

	/**
	 * Returns a properly indented directive with its content.
	 *
	 * @param string $directive Directive name.
	 * @param string $name      Name of the section.
	 * @param string $content   Content.
	 * @param int    $level     Level of indentation (0 = none, 1 = 3 spaces, 2 = 6 spaces etc.).
	 * @param bool   $return    Return (`true`) the value or echo (`false`).
	 *
	 * @return string|void
	 */
	public static function directive( string $directive, string $name, string $content = '', int $level = 0, bool $return = true ) {
		$res = sprintf(
			'%s.. %s:: %s%s',
			str_repeat( '   ', $level ),
			$directive,
			$name,
			PHP_EOL . PHP_EOL
		);
		if ( '' !== $content ) {
			$res .= preg_replace( '/^( {0,' . ( ( ( $level + 1 ) * 3 ) - 1 ) . '})([^ ])/', str_repeat( '   ', $level + 1 ) . '$2', $content );
			$res .= PHP_EOL;
		}
		if ( true === $return ) {
			return $res;
		}
		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo $res;
	}

	/**
	 * Renders a code hierarchy item as restructuredText (a directive).
	 *
	 * @param string $type    Type.
	 * @param string $name    Name.
	 * @param string $content Content.
	 *
	 * @return string
	 */
	public static function type_to_rst( string $type, string $name, string $content ): string {
		$directive = '';
		$type_lower = strtolower( trim( $type ) );
		if ( 'attr' === $type_lower ) {
			$directive = self::DIRECTIVE_ATTR;
		}
		if ( 'case' === $type_lower ) {
			$directive = self::DIRECTIVE_CASE;
		}
		if ( 'class' === $type_lower ) {
			$directive = self::DIRECTIVE_CLASS;
		}
		if ( 'const' === $type_lower ) {
			$directive = self::DIRECTIVE_CONST;
		}
		if ( 'enum' === $type_lower ) {
			$directive = self::DIRECTIVE_ENUM;
		}
		if ( 'exception' === $type_lower ) {
			$directive = self::DIRECTIVE_EXCEPTION;
		}
		if ( 'function' === $type_lower ) {
			$directive = self::DIRECTIVE_FUNCTION;
		}
		if ( 'global' === $type_lower ) {
			$directive = self::DIRECTIVE_GLOBAL;
		}
		if ( 'interface' === $type_lower ) {
			$directive = self::DIRECTIVE_INTERFACE;
		}
		if ( 'namespace' === $type_lower ) {
			$directive = self::DIRECTIVE_NAMESPACE;
		}
		if ( 'method' === $type_lower ) {
			$directive = self::DIRECTIVE_METHOD;
		}
		if ( 'staticmethod' === $type_lower ) {
			$directive = self::DIRECTIVE_STATICMETHOD;
		}
		if ( 'trait' === $type_lower ) {
			$directive = self::DIRECTIVE_TRAIT;
		}
		if ( 'var' === $type_lower ) {
			$directive = self::DIRECTIVE_ATTR;
		}
		if ( '' === $directive ) {
			return $content;
		}
		return self::directive( $directive, $name, $content ) ?? '';
	}

	/**
	 * Each line of the input string will be ensured to start with at least `$min_indent * 3` spaces.
	 *
	 * @param string $text       Input text.
	 * @param int    $min_indent Minimum indentation level (non-positive value will cause this function to just return `$text`).
	 *
	 * @return string
	 */
	public static function fix_indentation( string $text, int $min_indent ): string {
		if ( $min_indent <= 0 ) {
			return $text;
		}
		return preg_replace(
			'/^\s+$/',
			'',
			preg_replace(
				sprintf(
					'/^ {0,%d}([^ \n\r\0])/m',
					( $min_indent * 3 ) - 1
				),
				sprintf(
					'%s$1',
					str_repeat( '   ', $min_indent )
				),
				$text
			)
		);
	}

	/**
	 * Turns a code hierarchy into a single RST file.
	 *
	 * @param string        $title     Title of the document.
	 * @param CodeHierarchy $hierarchy Input code hierarchy.
	 * @param string        $commit    Current commit.
	 * @param string        $file_rel  Path to the original PHP file, relative to repo root.
	 *
	 * @return string
	 */
	public static function format( string $title, array $hierarchy, string $commit = '', string $file_rel = '' ): string { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		$underline = str_repeat( '=', strlen( $title ) );
		$indent = 0;

		$file_template_path = Helper::make_path( __DIR__, '..', 'templates', 'rst', 'file.rst' );
		$file_template = '';
		if ( file_exists( $file_template_path ) === true ) {
			$file_template = file_get_contents( $file_template_path );
		}

		$hierarchy_content = '';
		foreach ( $hierarchy as $hier_item ) {
			$hier_docblock = Helper::make_string( $hier_item['docblock'] );
			$hier_type = Helper::make_string( $hier_item['type'] );
			if ( '' !== $hier_docblock || 'namespace' === $hier_type ) {
				$hier_name = Helper::make_string( $hier_item['name'] );
				$phpdoc_data = PhpDoc::get_phpdoc_data( "<?php\n" . $hier_docblock );
				$hier_rst = self::output_str( $phpdoc_data['description'], $phpdoc_data['data'] );
				$hierarchy_content .= self::fix_indentation( self::type_to_rst( $hier_type, $hier_name, self::fix_indentation( $hier_rst, $indent + 1 ) ), $indent ) . PHP_EOL;
			}
			if ( in_array( $hier_type, array( 'class', 'interface', 'trait' ) ) ) {
				$indent = 1;
			}
		}
		$file_content = self::substitute( $file_template, '', self::generated_automatically( gmdate( 'c' ), $commit, $file_rel ), $hierarchy_content, '' );
		return sprintf(
			$file_content,
			$title,
			$underline
		);
	}

	/**
	 * Return a link to a GitHub repo commit.
	 *
	 * @param string $commit Commit.
	 *
	 * @return string
	 */
	public static function commit_link( string $commit ): string {
		if ( '' === trim( $commit ) ) {
			return '';
		}
		return sprintf(
			'`#%1$s <%2$s%3$s>`_',
			substr( $commit, 0, 7 ),
			'https://github.com/tommander/phpsphinx/commit/',
			$commit
		);
	}

	/**
	 * Return a link to a GitHub repo file.
	 *
	 * @param string $commit Commit.
	 * @param string $file   File.
	 *
	 * @return string
	 */
	public static function file_link( string $commit, string $file ): string {
		if ( '' === trim( $file ) ) {
			return '';
		}
		return sprintf(
			'`%1$s <%2$s%1$s>`_',
			$file,
			'https://github.com/tommander/phpsphinx/blob/' . $commit . '/'
		);
	}

	/**
	 * Returns a "Generated Automatically" badge that is included in every file.
	 *
	 * @param string $date   Current date.
	 * @param string $commit Current commit.
	 * @param string $file   Current PHP file.
	 *
	 * @return string
	 */
	public static function generated_automatically( string $date, string $commit, string $file ): string {
		$file_template_path = Helper::make_path( __DIR__, '..', 'templates', 'rst', 'generated.rst' );
		$file_template = '';
		if ( file_exists( $file_template_path ) === true ) {
			$file_template = file_get_contents( $file_template_path );
		}

		return sprintf(
			$file_template,
			$date,
			self::commit_link( $commit ),
			self::file_link( $commit, $file ),
		) . PHP_EOL;
	}

	/**
	 * Returns a folder index template.
	 *
	 * @param string $title Title of the document.
	 *
	 * @return string
	 */
	public static function get_empty_index( string $title = '' ): string {
		if ( '' === $title ) {
			$title = 'Subfolder';
		}
		$underline = str_repeat( '=', strlen( $title ) );

		$index_template_path = Helper::make_path( __DIR__, '..', 'templates', 'rst', 'index.rst' );
		$index_template = '';
		if ( file_exists( $index_template_path ) === true ) {
			$index_template = file_get_contents( $index_template_path );
		}
		return sprintf(
			$index_template,
			$title,
			$underline
		);
	}

	/**
	 * Adds a file reference to the index.
	 *
	 * @param string $what            What to add.
	 * @param string $subfolder_index Folder index content.
	 *
	 * @return string
	 */
	public static function add_to_index( string $what, string $subfolder_index ): string {
		return str_replace( ':subst:`ENDOFTOC`', '   ' . $what . PHP_EOL . ':subst:`ENDOFTOC`', $subfolder_index );
	}

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
	public static function substitute( string $text, string $before_toc, string $after_toc, string $start_of_toc, string $end_of_toc ): string {
		return str_replace(
			array( ':subst:`BEFORETOC`', ':subst:`AFTERTOC`', ':subst:`STARTOFTOC`', ':subst:`ENDOFTOC`' ),
			array( $before_toc, $after_toc, $start_of_toc, $end_of_toc ),
			$text
		);
	}

	/**
	 * Returns a representation of the referenced PhpDoc data in restructuredText.
	 *
	 * @param string       $description Description.
	 * @param DocblockData $data        Data.
	 *
	 * @return string
	 */
	public static function output_str( string $description, array $data ): string { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		$res = $description . PHP_EOL;

		foreach ( $data as $data_tag => $data_data ) {
			if ( is_array( $data_data['value'] ) ) {
				try {
					foreach ( $data_data['value'] as &$one_value ) {
						$arr = array();
						foreach ( $data_data['fields'] as $field ) {
							if ( array_key_exists( $field, $one_value ) !== true || ( 'deprecated' !== $data_tag && 'desc' !== $field && trim( $one_value[ $field ] ) === '' ) ) {
								$arr[ $field ] = 'no' . $field;
							} else {
								$arr[ $field ] = trim( $one_value[ $field ] );
							}
						}
						$res .= PhpDoc::replace( self::CLEAN_RST_DATA[ $data_tag ], $arr ) . PHP_EOL;
					}
				} catch ( \ArgumentCountError $exc ) {
					printf(
						'[ACE] %s%s',
						json_encode(
							array(
								'rst' => self::CLEAN_RST_DATA[ $data_tag ],
								// 'arr' => $arr,.
							),
							JSON_PRETTY_PRINT
						),
						PHP_EOL
					);
					// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo "ACE:\"$data_tag\"" . PHP_EOL;
				}
				continue;
			}
			if ( true === $data_data['value'] ) {
				$res .= self::CLEAN_RST_DATA[ $data_tag ] . PHP_EOL;
			}
		}
		return $res;
	}
}
