<?php
/**
 * File for class FormatterHtml.
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
 * The FormatterHtml class transform code hierarchy into an HTML.
 *
 * @psalm-import-type FileIndex from \TMD\Documentation\PhpSphinx
 * @psalm-import-type CodeHierarchy from \TMD\Documentation\DocblockExtract
 * @psalm-import-type DocblockData from \TMD\Documentation\PhpDoc
 */
class FormatterHtml implements FormatterInterface {
	/**
	 * HTML templates for Docblock tags.
	 *
	 * @var array<string, string>
	 */
	public const CLEAN_HTML_DATA = array(
		'abstract' => '<div class="property"><span class="propname">abstract</span><span class="propvalue"></span></div>',
		'access' => '<div class="property"><span class="propname">access</span><span class="propvalue">%%-access-%%</span></div>',
		'author' => '<div class="property"><span class="propname">author</span><span class="propvalue"><a href="mailto:%%-email-%%">%%-name-%% <%%-email-%%></a></span></div>',
		'category' => '<div class="property"><span class="propname">category</span><span class="propvalue">%%-desc-%%</span></div>',
		'copyright' => '<div class="property"><span class="propname">copyright</span><span class="propvalue">%%-desc-%%</span></div>',
		'deprecated' => '<div class="property"><span class="propname">deprecated</span><span class="propvalue">%%-desc-%%</span></div>',
		'example' => '<div class="property"><span class="propname">example</span><span class="propvalue">(%%-type-%%) - %%-desc-%%</span></div>',
		'final' => '<div class="property"><span class="propname">final</span><span class="propvalue"></span></div>',
		'filesource' => '<div class="property"><span class="propname">filesource</span><span class="propvalue"></span></div>',
		'global' => '<div class="property"><span class="propname">global</span><span class="propvalue">%%-name-%% (%%-type-%%) - %%-desc-%%</span></div>',
		'ignore' => '<div class="property"><span class="propname">ignore</span><span class="propvalue"></span></div>',
		'internal' => '<div class="property"><span class="propname">internal</span><span class="propvalue"> %%-desc-%%</span></div>',
		'license' => '<div class="property"><span class="propname">license</span><span class="propvalue"> (%%-url-%%) - %%-desc-%%</span></div>',
		'link' => '<div class="property"><span class="propname">link</span><span class="propvalue"> %%-desc-%%</span></div>',
		'method' => '<div class="property"><span class="propname">method</span><span class="propvalue"> %%-name-%% (%%-type-%%) - %%-desc-%%</span></div>',
		'name' => '<div class="property"><span class="propname">name</span><span class="propvalue"> %%-desc-%%</span></div>',
		'package' => '<div class="property"><span class="propname">package</span><span class="propvalue"> %%-desc-%%</span></div>',
		'param' => '<div class="property"><span class="propname">param</span><span class="propvalue">%%-type-%% %%-name-%%: %%-desc-%%</span></div>',
		'property' => '<div class="property"><span class="propname">property</span><span class="propvalue"> %%-name-%% (%%-type-%%) - %%-desc-%% READWRITE</span></div>',
		'property-read' => '<div class="property"><span class="propname">property</span><span class="propvalue"> %%-name-%% (%%-type-%%) - %%-desc-%% READ</span></div>',
		'property-write' => '<div class="property"><span class="propname">property</span><span class="propvalue"> %%-name-%% (%%-type-%%) - %%-desc-%% WRITE</span></div>',
		'return' => '<div class="property"><span class="propname">returns</span><span class="propvalue"> (%%-type-%%) - %%-desc-%%</span></div>',
		'see' => '<div class="property"><span class="propname">see</span><span class="propvalue"> %%-desc-%%</span></div>',
		'since' => '<div class="property"><span class="propname">since</span><span class="propvalue"> %%-desc-%%</span></div>',
		'static' => '<div class="property"><span class="propname">static</span><span class="propvalue"> %%-desc-%%</span></div>',
		'staticvar' => '<div class="property"><span class="propname">var</span><span class="propvalue"> %%-type-%% static %%-name-%%: %%-desc-%%</span></div>',
		'subpackage' => '<div class="property"><span class="propname">subpackage</span><span class="propvalue"> %%-desc-%%</span></div>',
		'todo' => '<div class="property"><span class="propname">todo</span><span class="propvalue"> %%-desc-%%</span></div>',
		'tutorial' => '<div class="property"><span class="propname">tutorial</span><span class="propvalue"> %%-desc-%%</span></div>',
		'uses' => '<div class="property"><span class="propname">uses</span><span class="propvalue"> %%-desc-%%</span></div>',
		'var' => '<div class="property"><span class="propname">var</span><span class="propvalue"> %%-type-%% %%-name-%%: %%-desc-%%</span></div>',
		'version' => '<div class="property"><span class="propname">version</span><span class="propvalue"> %%-desc-%%</span></div>',
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
	 * Renders a code hierarchy item as HTML.
	 *
	 * @param string $type    Type.
	 * @param string $name    Name.
	 * @param string $content Content.
	 *
	 * @return string
	 */
	public static function type_to_html( string $type, string $name, string $content ): string {
		$type_lower = strtolower( trim( $type ) );
		return "<div class=\"design design_$type_lower\"><div class=\"name\">$name</div><div class=\"content\">$content</div></div>";
	}

	/**
	 * Turns a code hierarchy into a single HTML file.
	 *
	 * @param string        $title     Title of the document.
	 * @param CodeHierarchy $hierarchy Input code hierarchy.
	 * @param string        $commit    Current commit.
	 * @param string        $file_rel  Path to the original PHP file, relative to repo root.
	 *
	 * @return string
	 */
	public static function format( string $title, array $hierarchy, string $commit = '', string $file_rel = '' ): string { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		$file_template_path = Helper::make_path( __DIR__, '..', 'templates', 'html', 'file.html' );
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
				$hierarchy_content .= self::type_to_html( $hier_type, $hier_name, $hier_rst, ) . PHP_EOL;
			}
		}
		$file_content = self::substitute( $file_template, '', self::generated_automatically( gmdate( 'c' ), $commit, $file_rel ), $hierarchy_content, '' );
		return sprintf(
			$file_content,
			$title
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
			'<a href="%2$s%3$s">#%1$s</a>',
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
			'<a href="%2$s%1$s">%1$s</a>',
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
		$file_template_path = Helper::make_path( __DIR__, '..', 'templates', 'html', 'generated.html' );
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

		$index_template_path = Helper::make_path( __DIR__, '..', 'templates', 'html', 'index.html' );
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
		return str_replace( '<!--`ENDOFTOC`-->', "\t\t<li><a href=\"$what.html\">$what</a></li>" . PHP_EOL . '<!--`ENDOFTOC`-->', $subfolder_index );
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
			array( '<!--`BEFORETOC`-->', '<!--`AFTERTOC`-->', '<!--`STARTOFTOC`-->', '<!--`ENDOFTOC`-->' ),
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
						$res .= PhpDoc::replace( self::CLEAN_HTML_DATA[ $data_tag ], $arr ) . PHP_EOL;
					}
				} catch ( \ArgumentCountError $exc ) {
					printf(
						'[ACE] %s%s',
						json_encode(
							array(
								'html' => self::CLEAN_HTML_DATA[ $data_tag ],
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
				$res .= self::CLEAN_HTML_DATA[ $data_tag ] . PHP_EOL;
			}
		}
		return $res;
	}
}
