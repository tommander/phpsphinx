<?php

/**
 * File for class PhpDocTest.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation\Tests;

use PHPUnit\Framework\TestCase;
use TMD\Documentation\PhpDoc;

/**
 * Test class for PhpDoc.
 */
final class PhpDocTest extends BaseTest
{
    /**
     * Test of function `replace`.
     *
     * @return void
     */
    public function testReplace(): void
    {
        // N/ XXX.
        $input = '';
        $params = array();
        $expected = '';
        $result = PhpDoc::replace($input, $params);
        self::assertEquals($expected, $result, 'test_replace_1');

        // N/ XXX.
        $input = '1nPuT';
        $params = array();
        $expected = '1nPuT';
        $result = PhpDoc::replace($input, $params);
        self::assertEquals($expected, $result, 'test_replace_2');

        // N/ XXX.
        $input = 'Input: %%-thisworks-%% %%-doesnt#work-%% %%doesntwork%% %%-AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789_--%%';
        $params = array(
            'thisworks' => 'Hola!',
            'doesnt#work' => 'Oops',
            'doesntwork' => 'Yup',
            'aabbccddeeffgghhiijjkkllmmnnooppqqrrssttuuvvwwxxyyzz0123456789_-' => 'itworks!',
        );
        $expected = 'Input: Hola! %%-doesnt#work-%% %%doesntwork%% itworks!';
        $result = PhpDoc::replace($input, $params);
        self::assertEquals($expected, $result, 'test_replace_3');
    }

    /**
     * Test of function `parseTag`.
     *
     * @return void
     */
    public function testParseTag(): void
    {
        // 1/ Empty.
        $input = '';
        $tag = '';
        $data = array();
        $expected = 'Unknown or misconfigured tag ""';
        $result = PhpDoc::parseTag($data, $input, $tag);
        self::assertEquals($expected, $result, 'test_parsetag_1');

        // 2/ Abstract (because it has no field).
        $input = '';
        $tag = 'abstract';
        $data = PhpDoc::CLEAN_DATA;
        $expected_result = true;
        $expected_value = true;
        $result = PhpDoc::parseTag($data, $input, $tag);
        self::assertEquals($expected_result, $result, 'test_parsetag_2a');
        self::assertEquals($expected_value, $data[ $tag ]['value'], 'test_parsetag_2b');

        // 3/ Broken Abstract (because it has fields but no regex).
        $data = PhpDoc::CLEAN_DATA;
        $data['brokenabstract'] = array(
            'regex' => '',
            'fields' => array( 'type' ),
            'value' => false,
        );
        $input = '';
        $tag = 'brokenabstract';
        $expected_result = 'Empty regex';
        $expected_value = false;
        $result = PhpDoc::parseTag($data, $input, $tag);
        self::assertEquals($expected_result, $result, 'test_parsetag_3a');
        self::assertEquals($expected_value, $data[ $tag ]['value'], 'test_parsetag_3b');

        // 4/ Broken Abstract (because it has fields but no regex).
        $data = PhpDoc::CLEAN_DATA;
        $input = '@param string $hehehe Hehehe.';
        $tag = 'author';
        $expected_result = 'Input "@param string $hehehe Hehehe." does not match regex "/^\s*(?<name>.*)[\t ]+<(?<email>.*)>\s*$/"';
        $expected_value = array();
        $result = PhpDoc::parseTag($data, $input, $tag);
        self::assertEquals($expected_result, $result, 'test_parsetag_4a');
        self::assertEquals($expected_value, $data[ $tag ]['value'], 'test_parsetag_4b');
    }

    /**
     * Test of function `parseDocblock`.
     *
     * @return void
     */
    public function testParseDocblock(): void
    {
        // N/ XXX.
        $docblock = '';
        $data_before = array(
            'description' => '',
            'data' => PhpDoc::CLEAN_DATA,
        );
        $data_after = PhpDoc::parseDocblock($docblock);
        self::assertEquals($data_before, $data_after, 'test_parsedocblock_1');

        // N/ XXX.
        $docblock = '/** @abstract */';
        $data_expected = array(
            'description' => '',
            'data' => array_merge(
                array(
                    'abstract' => array(
                        'value' => true,
                    ),
                ),
                PhpDoc::CLEAN_DATA,
            ),
        );
        $data_actual = PhpDoc::parseDocblock($docblock);
        self::assertEquals($data_expected, $data_actual, 'test_parsedocblock_2');
    }

    /**
     * Test of function `get_phpdoc_data`.
     *
     * @return void
     */
    public function testGetPhpdocData(): void
    {
        // N/ XXX.
        $docblock = '';
        $expected = array(
            'description' => '',
            'data' => PhpDoc::CLEAN_DATA,
        );
        $result = PhpDoc::getPhpdocData($docblock);
        self::assertEquals($expected, $result, 'test_getphpdocdata_1');
    }
}
