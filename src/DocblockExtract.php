<?php
/**
 * File for class DocblockExtract.
 *
 * @package Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

/**
 * The DocblockExtract class extract docblocks from a PHP source file.
 *
 * @psalm-type CodeObject = array{type: string, name: string, docblock: string, params?: list<string>}
 * @psalm-type CodeHierarchy = list<CodeObject>
 * @psalm-import-type Token from Tokenizer
 * @psalm-import-type TokensList from Tokenizer
 */
class DocblockExtract {
	/**
	 * Undocumented function
	 *
	 * @param string $type     Type.
	 * @param string $name     Name.
	 * @param string $docblock DocBlock.
	 *
	 * @return CodeObject
	 */
	public function code_object( string $type = '', string $name = '', string $docblock = '' ): array {
		return array(
			'type' => $type,
			'name' => $name,
			'docblock' => $docblock,
		);
	}

	/**
	 * Undocumented function
	 *
	 * @param TokensList $tokens          Tokens.
	 * @param string     $class_name      Class name.
	 * @param string     &$last_namespace Last namespace.
	 *
	 * @return CodeHierarchy
	 */
	public function get_code_hierarchy( array $tokens, string &$class_name, string &$last_namespace ): array { // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
		/**
		 * Hello.
		 *
		 * @var CodeHierarchy
		 */
		$stack = array();
		/**
		 * Mode for foreach loop.
		 *
		 * 0 ... seeking file docblock
		 * 1 ... seeking docblock
		 * 2 ... seeking type
		 * 3 ... seeking name
		 * 4 ... seeking start of params
		 * 5 ... collecting params
		 *
		 * @var int<0,5>
		 */
		$mode = 0;
		$temp_object = $this->code_object();
		$named_objects = array( 'T_CLASS', 'T_INTERFACE', 'T_TRAIT', 'T_FUNCTION', 'T_CONST', 'T_NAMESPACE' );
		$structural_elements = array( 'T_REQUIRE', 'T_REQUIRE_ONCE', 'T_INCLUDE', 'T_INCLUDE_ONCE', 'T_CLASS', 'T_INTERFACE', 'T_TRAIT', 'T_FUNCTION', 'T_CONST', 'T_VARIABLE', 'T_NAMESPACE' );
		$text_objects = array( 'string', 'T_STRING', 'T_NAME_QUALIFIED' );
		$namespace_name = '';

		foreach ( $tokens as $token ) {
			$token_name = $token['name'];
			$token_content = $token['content'];

			if ( 0 === $mode ) {
				if ( 'T_DOC_COMMENT' !== $token_name ) {
					continue;
				}
				$stack[] = $this->code_object( 'file', '', $token_content );
				$mode = 1;
				continue;
			}

			if ( 1 === $mode ) {
				if ( 'T_DOC_COMMENT' !== $token_name && 'T_NAMESPACE' !== $token_name ) {
					continue;
				}
				if ( 'T_NAMESPACE' === $token_name ) {
					$temp_object['type'] = $token_content;
					$mode = 3;
					continue;
				}
				$temp_object['docblock'] = $token_content;
				$mode = 2;
				continue;
			}

			if ( 2 === $mode ) {
				if ( in_array( $token_name, $structural_elements ) !== true ) {
					continue;
				}

				if ( 'T_VARIABLE' === $token_name ) {
					$temp_object['type'] = 'var';
					$temp_object['name'] = $token_content;
					$stack[] = $temp_object;
					$temp_object = $this->code_object();
					$mode = 1;
					continue;
				}

				$temp_object['type'] = $token_content;
				if ( in_array( $token_name, $named_objects ) === true ) {
					$mode = 3;
					continue;
				}

				$stack[] = $temp_object;
				$temp_object = $this->code_object();
				$mode = 1;
				continue;
			}

			if ( 3 === $mode ) {
				if ( in_array( $token_name, $text_objects ) !== true ) {
					continue;
				}

				if ( strcmp( 'namespace', trim( $temp_object['type'] ) ) === 0 ) {
					$namespace_name = $token_content;
					$last_namespace = $namespace_name;
				}
				$temp_object['name'] = $token_content;
				if (
					in_array( $temp_object['type'], array( 'class', 'interface', 'trait' ) ) &&
					'' !== $namespace_name
				) {
					$class_name = $temp_object['name'];
				}
				if ( 'function' === $temp_object['type'] ) {
					if ( '' !== $class_name ) {
						$temp_object['type'] = 'method';
					}
					$temp_object['name'] = $token_content;
					$temp_object['params'] = array();
					$mode = 4;
					continue;
				}

				$stack[] = $temp_object;
				$temp_object = $this->code_object();
				$mode = 1;
				continue;
			}

			if ( 4 === $mode ) {
				if ( 'string' !== $token_name || '(' !== $token_content ) {
					continue;
				}
				$mode = 5;
				continue;
			}

			if ( 5 === $mode ) {
				if ( 'string' === $token_name && ')' === $token_content ) {
					$temp_object['name'] .= '(' . implode( ', ', $temp_object['params'] ?? array() ) . ')';
					$stack[] = $temp_object;
					$temp_object = $this->code_object();
					$mode = 1;
					continue;
				}

				if ( 'T_VARIABLE' !== $token_name ) {
					continue;
				}

				$temp_object['params'][] = $token_content;
				continue;
			}
		}

		return $stack;
	}
}
