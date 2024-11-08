<?php

/**
 * File for class DocblockExtractTest.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation\Tests;

use PHPUnit\Framework\TestCase;
use TMD\Documentation\DocblockExtract;

/**
 * Test class for DocblockExtract.
 */
final class DocblockExtractTest extends BaseTest
{
    /**
     * Test of function `code_object`.
     *
     * @return void
     */
    public function testCodeObject(): void
    {
        // 1/ Empty strings.
        $type = '';
        $name = '';
        $docblock = '';
        $expected = array(
            'type' => '',
            'name' => '',
            'docblock' => '',
        );
        $result = DocblockExtract::codeObject($type, $name, $docblock);
        self::assertEquals($expected, $result, 'test_codeobject_1');

        // 2/ Populated strings.
        $type = 'type';
        $name = 'nAmE';
        $docblock = 'd0cblOck';
        $expected = array(
            'type' => $type,
            'name' => $name,
            'docblock' => $docblock,
        );
        $result = DocblockExtract::codeObject($type, $name, $docblock);
        self::assertEquals($expected, $result, 'test_codeobject_2');
    }

    /**
     * Test of function `get_code_hierarchy`.
     *
     * @return void
     */
    public function testGetCodeHierarchy(): void
    {
        // 1/ Empty.
        $tokens = array();
        $class_name = '';
        $last_namespace = '';
        $expected = array();
        $result = DocblockExtract::getCodeHierarchy($tokens, $class_name, $last_namespace);
        self::assertEquals($expected, $result, 'test_getcodehierarchy_1');

        // 2/ Something.
		// phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound,Universal.WhiteSpace.CommaSpacing.TooMuchSpaceAfter
        $tokens = array(
            array( 'name' => 'T_DOC_COMMENT', 'content' => 'AFile' ),
            array( 'name' => 'T_DOC_COMMENT', 'content' => '/** something */' ),
            array( 'name' => 'T_FUNCTION',    'content' => 'function' ),
            array( 'name' => 'string',        'content' => 'AFunction' ),
            array( 'name' => 'string',        'content' => '(' ),
            array( 'name' => 'T_VARIABLE',    'content' => 'param1' ),
            array( 'name' => 'string',        'content' => ')' ),
            array( 'name' => 'T_DOC_COMMENT', 'content' => '/** toodlydoo */' ),
            array( 'name' => 'T_VARIABLE',    'content' => 'var1' ),
        );
        $expected = array(
            array( 'type' => 'file', 'name' => '', 'docblock' => 'AFile' ),
            array( 'type' => 'function', 'name' => 'AFunction(param1)', 'docblock' => '/** something */', 'params' => array( 'param1' ) ),
            array( 'type' => 'var', 'name' => 'var1', 'docblock' => '/** toodlydoo */' ),
        );
		// phpcs:enable
        $class_name = '';
        $last_namespace = '';
        $result = DocblockExtract::getCodeHierarchy($tokens, $class_name, $last_namespace);
        self::assertEquals($expected, $result, 'test_getcodehierarchy_2');
    }
}
