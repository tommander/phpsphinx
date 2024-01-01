<?php
/**
 * File for class FileMap.
 *
 * @package Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

/**
 * The FileMap class represents a custom map of PHP source file.
 *
 * @psalm-import-type FileIndex from PhpSphinx
 * @psalm-import-type CodeHierarchy from DocblockExtract
 */
class FormatterRst implements FormatterWithIndexInterface {
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
	 * Undocumented function
	 *
	 * This directive declares a new PHP namespace. It accepts nested namespaces by separating
	 * namespaces with `\\`. It does not generate any content like {@see php:class} does. It will however,
	 * generate an entry in the namespace/module index.
	 *
	 * It has `synopsis` and `deprecated` options, similar to **py:module**.
	 *
	 * @param string $directive Directive.
	 * @param string $name      Name.
	 * @param string $content   Content.
	 * @param int    $level     Level.
	 * @param bool   $return    Return.
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
	 * Undocumented function
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
	 * Undocumented function
	 *
	 * @param string $text       Text.
	 * @param int    $min_indent Min indent.
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
	 * Undocumented function
	 *
	 * @param string        $title     Title.
	 * @param CodeHierarchy $hierarchy Hierarchy.
	 *
	 * @return string
	 */
	public static function format( string $title, array $hierarchy ): string { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		$result = $title . PHP_EOL . str_repeat( '=', strlen( $title ) ) . PHP_EOL . PHP_EOL;
		$indent = 0;

		$phpdoc = new PhpDoc();

		foreach ( $hierarchy as $hier_item ) {
			$hier_docblock = Helper::make_string( $hier_item['docblock'] );
			$hier_type = Helper::make_string( $hier_item['type'] );
			if ( '' !== $hier_docblock || 'namespace' === $hier_type ) {
				$hier_name = Helper::make_string( $hier_item['name'] );
				$phpdoc->clear();
				$phpdoc->docblock = "<?php\n" . $hier_docblock;
				$phpdoc->parse();
				$hier_rst = $phpdoc->toRst();
				$result .= self::fix_indentation( self::type_to_rst( $hier_type, $hier_name, self::fix_indentation( $hier_rst, $indent + 1 ) ), $indent ) . PHP_EOL;
			}
			if ( in_array( $hier_type, array( 'class', 'interface', 'trait' ) ) ) {
				$indent = 1;
			}
		}
		return $result;
	}

	/**
	 * Undocumented function
	 *
	 * @param FileIndex $files_index Files index.
	 * @param string    $title_text  Title text.
	 * @param string    $caption     Caption.
	 *
	 * @return string
	 */
	public static function index_file( array $files_index, string $title_text = 'API', string $caption = '' ): string { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		if ( '' === $title_text ) {
			$title_text = 'API';
		}
		$title_underline = str_repeat( '=', strlen( $title_text ) );
		$result = $title_text . PHP_EOL . $title_underline . PHP_EOL . PHP_EOL . '.. toctree::' . PHP_EOL;
		if ( '' !== $caption ) {
			$result .= '   :caption: ' . $caption . ':' . PHP_EOL;
		}
		$result .= PHP_EOL;
		foreach ( $files_index as $one_file_data ) {
			$result .= '   ' . $one_file_data['filename'] . PHP_EOL;
		}
		return $result;
	}


	/**
	 * Undocumented function
	 *
	 * @param string $title Title.
	 *
	 * @return string
	 */
	public static function get_empty_subfolder_index( string $title = '' ): string {
		if ( '' === $title ) {
			$title = 'Subfolder';
		}
		$underline = str_repeat( '=', strlen( $title ) );
		return <<<EOS
		$title
		$underline

		.. toctree::
		   :caption: Contents:


		EOS;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $title Title.
	 *
	 * @return string
	 */
	public static function get_empty_api_index( string $title = '' ): string {
		if ( '' === $title ) {
			$title = 'API';
		}
		$underline = str_repeat( '=', strlen( $title ) );
		return <<<EOS
		$title
		$underline

		.. toctree::
		   :caption: Contents:


		EOS;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $what            What.
	 * @param string $subfolder_index Subfolder index.
	 *
	 * @return string
	 */
	public static function add_to_subfolder_index( string $what, string $subfolder_index ): string {
		return $subfolder_index . '   ' . $what . PHP_EOL;
	}

	/**
	 * Undocumented function
	 *
	 * @param string $what      What.
	 * @param string $api_index Subfolder index.
	 *
	 * @return string
	 */
	public static function add_to_api_index( string $what, string $api_index ): string {
		return $api_index . '   ' . $what . PHP_EOL;
	}
}
