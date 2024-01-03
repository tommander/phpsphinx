<?php
/**
 * File for class PhpDoc.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

use Exception;

/**
 * The PhpDoc class parses PHPDoc block comments.
 *
 * @psalm-type DocblockData = array<string, array{regex: string, fields: list<string>, value: bool|list<array<string, string>>}>
 */
class PhpDoc implements \Stringable {
	/**
	 * Original DocBlock
	 *
	 * @var string
	 */
	public string $docblock = '';
	/**
	 * Description (short and long)
	 *
	 * @var string
	 */
	public string $description = '';
	/**
	 * Arranged data extracted from DocBlock
	 *
	 * Schema:
	 * * `regex` (*string*) RegEx that is used to parse the attribute value
	 * * `fields` (*array*) Array of field names that are used in the regex and can be referenced in the output RST
	 * * `value` (*bool|array*) Value of the tag. For flag tags, it's either true or false, for the rest it's an array of arrays.
	 *
	 * @var DocblockData
	 */
	public const CLEAN_DATA = array(
		'abstract' => array(
			'regex' => '',
			'fields' => array(),
			'value' => false,
		),
		'access' => array(
			'regex' => 'ACCESS',
			'fields' => array( 'access' ),
			'value' => array(),
		),
		'author' => array(
			'regex' => 'AUTHOR',
			'fields' => array( 'name', 'email' ),
			'value' => array(),
		),
		'category' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
		'copyright' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
		'deprecated' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
		'example' => array(
			'regex' => 'TD',
			'fields' => array( 'type', 'desc' ),
			'value' => array(),
		),
		'final' => array(
			'regex' => '',
			'fields' => array(),
			'value' => false,
		),
		'filesource' => array(
			'regex' => '',
			'fields' => array(),
			'value' => false,
		),
		'global' => array(
			'regex' => 'TND',
			'fields' => array( 'type', 'name', 'desc' ),
			'value' => array(),
		),
		'ignore' => array(
			'regex' => '',
			'fields' => array(),
			'value' => false,
		),
		'internal' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
		'license' => array(
			'regex' => 'UD',
			'fields' => array( 'url', 'desc' ),
			'value' => array(),
		),
		'link' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
		'method' => array(
			'regex' => 'TFD',
			'fields' => array( 'type', 'name', 'desc' ),
			'value' => array(),
		),
		'name' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
		'package' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
		'param' => array(
			'regex' => 'TND',
			'fields' => array( 'type', 'name', 'desc' ),
			'value' => array(),
		),
		'property' => array(
			'regex' => 'TND',
			'fields' => array( 'type', 'name', 'desc' ),
			'value' => array(),
		),
		'property-read' => array(
			'regex' => 'TND',
			'fields' => array( 'type', 'name', 'desc' ),
			'value' => array(),
		),
		'property-write' => array(
			'regex' => 'TND',
			'fields' => array( 'type', 'name', 'desc' ),
			'value' => array(),
		),
		'return' => array(
			'regex' => 'TD',
			'fields' => array( 'type', 'desc' ),
			'value' => array(),
		),
		'see' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
		'since' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
		'static' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
		'staticvar' => array(
			'regex' => 'TND',
			'fields' => array( 'type', 'name', 'desc' ),
			'value' => array(),
		),
		'subpackage' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
		'todo' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
		'tutorial' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
		'uses' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
		'var' => array(
			'regex' => 'TND',
			'fields' => array( 'type', 'name', 'desc' ),
			'value' => array(),
		),
		'version' => array(
			'regex' => 'REST',
			'fields' => array( 'desc' ),
			'value' => array(),
		),
	);
	/**
	 * PhpDoc instance data.
	 *
	 * @var DocblockData
	 */
	public array $data = self::CLEAN_DATA;

	/**
	 * Clear the instance data.
	 *
	 * @return void
	 */
	public function clear(): void {
		$this->data = self::CLEAN_DATA;
		$this->docblock = '';
		$this->description = '';
	}

	/**
	 * Replace special placeholders in the text.
	 *
	 * A placeholder is `%%-xxx-%%`, where `xxx` is at least one instance of an alphanumeric characters, underscore or dash.
	 *
	 * @param string                $input  Input text with placeholders.
	 * @param array<string, string> $params Each key is a placeholder and its value is the replacement.
	 *
	 * @example
	 * $result = replace( 'hello %%-user-%%', array( 'user' => 'Anonymous' ) );
	 * echo $result; //= 'hello Anonymous'
	 */
	public function replace( string $input, array $params ): string {
		return preg_replace_callback(
			'/%%-([A-Za-z0-9_-]+)-%%/',
			function ( array $matches ) use ( $params ): string {
				return $params[ strtolower( $matches[1] ) ];
			},
			$input
		);
	}

	/**
	 * Given the tag name, parse the value of the tag and save it to the correct key in `PhpDoc::$data`.
	 *
	 * For example, this is the tag name and tag value:
	 *
	 * * `@tag string $something Description`
	 * * Name = `tag`
	 * * Value = `string $something Description`
	 *
	 * The tag must have an item in `PhpDoc::$data` e.g. to know, what regex to match the input against.
	 *
	 * @param string $input Tag value.
	 * @param string $tag   Tag name.
	 *
	 * @return string|bool True in case all went fine, false or string (with error message) otherwise.
	 */
	public function parse_tag( string $input, string $tag ): string|bool {
		if (
			array_key_exists( $tag, $this->data ) !== true ||
			array_key_exists( 'regex', $this->data[ $tag ] ) !== true ||
			array_key_exists( 'fields', $this->data[ $tag ] ) !== true ||
			array_key_exists( 'value', $this->data[ $tag ] ) !== true
		) {
			return 'Unknown or misconfigured tag "' . $tag . '"';
		}

		if ( count( $this->data[ $tag ]['fields'] ) === 0 ) {
			$this->data[ $tag ]['value'] = true;
			return true;
		}

		$regex = RegexBuilder::pattern( $this->data[ $tag ]['regex'] );

		if ( '' === $regex ) {
			return 'Empty regex';
		}
		if ( 1 !== preg_match( $regex, $input, $matches ) ) {
			return 'Input "' . $input . '" does not match regex "' . $regex . '"';
		}

		$tmp = array();
		foreach ( $this->data[ $tag ]['fields'] as $field ) {
			$subject = $matches[ $field ] ?? '';
			if ( '' === $subject ) {
				continue;
			}
			$tmp[ $field ] = preg_replace( '/' . RegexBuilder::RE_MULTILINE_TRASH . '/m', ' ', $subject );
		}
		if ( is_bool( $this->data[ $tag ]['value'] ) ) {
			$this->data[ $tag ]['value'] = array();
		}
		/**
		 * Hello.
		 *
		 * @psalm-suppress PossiblyInvalidArrayAssignment
		 */
		$this->data[ $tag ]['value'][] = $tmp;
		return true;
	}

	/**
	 * From a docblock separate short/long description and all tags with their values.
	 *
	 * @param string $docblock Input DocBlock.
	 *
	 * @return void
	 *
	 * @throws \Exception Yes it does.
	 */
	public function parse_docblock( string $docblock ): void {
		$regexp = '/(?:^\s*\*\s+@(?<attrname>[A-Za-z-]+)(?<attrval>(?:.*)(?:(?:\n\s*\*\s+[^@].*)*))|^\s*\*(?<text>[^\n\/]+))/m';
		$ret = preg_match_all(
			$regexp,
			$docblock,
			$matches,
			PREG_SET_ORDER,
			0
		);

		if ( is_int( $ret ) !== true || 0 === $ret ) {
			return;
		}

		/**
		 * Hello.
		 *
		 * @var array{0: string, text: string, attrname: string, attrval: string}
		 */
		foreach ( $matches as $one_match ) {
			if ( array_key_exists( 'text', $one_match ) ) {
				$this->description .= trim( $one_match['text'] ) . PHP_EOL;
				continue;
			}

			$attrname = '';
			if ( array_key_exists( 'attrname', $one_match ) ) {
				$attrname = trim( $one_match['attrname'] );
			}
			if ( array_key_exists( $attrname, $this->data ) !== true ) {
				continue;
			}

			$attrval = '';
			if ( array_key_exists( 'attrval', $one_match ) ) {
				$attrval = trim( $one_match['attrval'] );
			}

			$parse_result = $this->parse_tag( $attrval, $attrname );
			if ( is_string( $parse_result ) ) {
				throw new \Exception(
					sprintf(
						'String "%1$s" was not parsed. Error: %2$s' . PHP_EOL,
						$one_match[0], // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
						$parse_result // phpcs:ignore WordPress.Security.EscapeOutput.ExceptionNotEscaped
					)
				);
			}
		}
	}

	/**
	 * Extract DocBlock from input property and starting parsing it.
	 *
	 * @return void
	 */
	public function parse(): void {
		$tokens = token_get_all( $this->docblock );

		$docblock = null;
		foreach ( $tokens as $token ) {
			if ( T_DOC_COMMENT === $token[0] ) {
				$docblock = $token[1];
				break;
			}
		}
		if ( null === $docblock ) {
			return;
		}

		$this->parse_docblock( $docblock );
	}

	/**
	 * Return a text representation of this instance of PhpDoc.
	 *
	 * @return string
	 */
	public function __toString(): string {
		return json_encode(
			array(
				'description' => $this->description,
				'data' => $this->data,
				'docblock' => $this->docblock,
			),
			JSON_PRETTY_PRINT
		);
	}
}
