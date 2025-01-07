<?php

/**
 * File for StructureToRstTest class.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation\Tests;

use PHPUnit\Framework\TestCase;
use TMD\Documentation\StructureToRst;

/**
 * Test class for DirList.
 */
final class StructureToRstTest extends TestCase
{
    /**
     * StructureToRst instance.
     *
     * @var StructureToRst|null
     */
    private ?StructureToRst $structureToRst = null;

    /**
     * Test setup - create new instance of StructureToRst.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->structureToRst = new StructureToRst();
    }

    /**
     * Test teardown - free the instance of StructureToRst.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->structureToRst);
    }

    /**
     * Basic test of function `parseXml`.
     *
     * @return void
     */
    public function testBasicParseXml(): void
    {
        if (!($this->structureToRst instanceof StructureToRst)) {
            self::fail('test_basicparsexml_0');
        }

        $parseResult = $this->structureToRst->parseXml('');
        $expected = 1;
        self::assertEquals($expected, $parseResult, 'test_basicparsexml_1');

        $parseResult = $this->structureToRst->parseXml('hello');
        $expected = 1;
        self::assertEquals($expected, $parseResult, 'test_basicparsexml_2');

        $parseResult = $this->structureToRst->parseXml('<xml>');
        $expected = 1;
        self::assertEquals($expected, $parseResult, 'test_basicparsexml_3');

        $parseResult = $this->structureToRst->parseXml('</xml>');
        $expected = 1;
        self::assertEquals($expected, $parseResult, 'test_basicparsexml_4');

        $parseResult = $this->structureToRst->parseXml('<xml></xml>');
        $expected = 2;
        self::assertEquals($expected, $parseResult, 'test_basicparsexml_5');

        $parseResult = $this->structureToRst->parseXml('<project></project>');
        $expected = 2;
        self::assertEquals($expected, $parseResult, 'test_basicparsexml_6');

        $parseResult = $this->structureToRst->parseXml("<?xml version=\"1.0\"?>\n<pro></pro>\n");
        $expected = 2;
        self::assertEquals($expected, $parseResult, 'test_basicparsexml_7');

        $parseResult = $this->structureToRst->parseXml('<?xml version="1.0"?><project></project>');
        $expected = 2;
        self::assertEquals($expected, $parseResult, 'test_basicparsexml_8');

        $parseResult = $this->structureToRst->parseXml('<xml><notxml/></xml>');
        $expected = 3;
        self::assertEquals($expected, $parseResult, 'test_basicparsexml_9');

        $parseResult = $this->structureToRst->parseXml("<?xml version=\"1.0\"?><pro><notxml/></pro>");
        $expected = 3;
        self::assertEquals($expected, $parseResult, 'test_basicparsexml_A');

        $parseResult = $this->structureToRst->parseXml('<project><notxml/></project>');
        $expected = 0;
        self::assertEquals($expected, $parseResult, 'test_basicparsexml_B');

        $parseResult = $this->structureToRst->parseXml('<?xml version="1.0"?><project><notxml/></project>');
        $expected = 0;
        self::assertEquals($expected, $parseResult, 'test_basicparsexml_C');
    }

    /**
     * Extended test of function `parseXml`.
     *
     * @return void
     */
    public function testExtendedParseXml(): void
    {
        if (!($this->structureToRst instanceof StructureToRst)) {
            self::fail('test_extendedparsexml_0');
        }

        self::assertEquals($this->structureToRst->projectName, '', 'test_extendedparsexml_1a');
        self::assertEmpty($this->structureToRst->files, 'test_extendedparsexml_1b');

        $parseResult = $this->structureToRst->parseXml('<?xml version="1.0"?><project><something/></project>');
        $expected = 0;
        self::assertEquals($expected, $parseResult, 'test_extendedparsexml_2a');
        self::assertEquals($this->structureToRst->projectName, '', 'test_extendedparsexml_2b');
        self::assertEmpty($this->structureToRst->files, 'test_extendedparsexml_2c');

        $parseResult = $this->structureToRst->parseXml('<?xml version="1.0"?><project name=""><file/></project>');
        $expected = 0;
        self::assertEquals($expected, $parseResult, 'test_extendedparsexml_3a');
        self::assertEquals($this->structureToRst->projectName, '', 'test_extendedparsexml_3b');
        self::assertArrayHasKey('', $this->structureToRst->files, 'test_extendedparsexml_3c');
        self::assertEquals('File ""', $this->structureToRst->files['']['title'], 'test_extendedparsexml_3d');
        self::assertEquals('', $this->structureToRst->files['']['content'], 'test_extendedparsexml_3e');

        $parseResult = $this->structureToRst->parseXml('<?xml version="1.0"?><project name="Hello World"><file/></project>');
        $expected = 0;
        self::assertEquals($expected, $parseResult, 'test_extendedparsexml_4a');
        self::assertEquals($this->structureToRst->projectName, 'Hello World', 'test_extendedparsexml_4b');
        self::assertArrayHasKey('', $this->structureToRst->files, 'test_extendedparsexml_4c');
        self::assertEquals('File ""', $this->structureToRst->files['']['title'], 'test_extendedparsexml_4d');
        self::assertEquals('', $this->structureToRst->files['']['content'], 'test_extendedparsexml_4e');

        $parseResult = $this->structureToRst->parseXml('<?xml version="1.0"?><project name="Hello World"><file path="hi"/></project>');
        $expected = 0;
        self::assertEquals($expected, $parseResult, 'test_extendedparsexml_5a');
        self::assertEquals($this->structureToRst->projectName, 'Hello World', 'test_extendedparsexml_5b');
        self::assertArrayHasKey('hi', $this->structureToRst->files, 'test_extendedparsexml_5c');
        self::assertEquals('File "hi"', $this->structureToRst->files['hi']['title'], 'test_extendedparsexml_5d');
        self::assertEquals('', $this->structureToRst->files['hi']['content'], 'test_extendedparsexml_5e');

        $parseResult = $this->structureToRst->parseXml('<?xml version="1.0"?><project name="Hello World"><file path="hi"><docblock><description>Yey</description></docblock></file></project>');
        $expected = 0;
        self::assertEquals($expected, $parseResult, 'test_extendedparsexml_6a');
        self::assertEquals($this->structureToRst->projectName, 'Hello World', 'test_extendedparsexml_6b');
        self::assertArrayHasKey('hi', $this->structureToRst->files, 'test_extendedparsexml_6c');
        self::assertEquals('File "hi"', $this->structureToRst->files['hi']['title'], 'test_extendedparsexml_6d');
        self::assertEquals('Yey' . PHP_EOL . PHP_EOL, $this->structureToRst->files['hi']['content'], 'test_extendedparsexml_6e');

        $parseResult = $this->structureToRst->parseXml('<?xml version="1.0"?><project name="Hello World"><file path="hi"><docblock><description>Yey</description><long-description>Foo</long-description></docblock></file></project>');
        $expected = 0;
        self::assertEquals($expected, $parseResult, 'test_extendedparsexml_7a');
        self::assertEquals($this->structureToRst->projectName, 'Hello World', 'test_extendedparsexml_7b');
        self::assertArrayHasKey('hi', $this->structureToRst->files, 'test_extendedparsexml_7c');
        self::assertEquals('File "hi"', $this->structureToRst->files['hi']['title'], 'test_extendedparsexml_7d');
        self::assertEquals('Yey' . PHP_EOL . PHP_EOL . 'Foo' . PHP_EOL . PHP_EOL, $this->structureToRst->files['hi']['content'], 'test_extendedparsexml_7e');

        $parseResult = $this->structureToRst->parseXml('<?xml version="1.0"?><project name="Hello World"><file path="hi"><docblock><description>Yey</description><long-description>Foo</long-description></docblock></file></project>');
        $expected = 0;
        self::assertEquals($expected, $parseResult, 'test_extendedparsexml_7a');
        self::assertEquals($this->structureToRst->projectName, 'Hello World', 'test_extendedparsexml_7b');
        self::assertArrayHasKey('hi', $this->structureToRst->files, 'test_extendedparsexml_7c');
        self::assertEquals('File "hi"', $this->structureToRst->files['hi']['title'], 'test_extendedparsexml_7d');
        self::assertEquals('Yey' . PHP_EOL . PHP_EOL . 'Foo' . PHP_EOL . PHP_EOL, $this->structureToRst->files['hi']['content'], 'test_extendedparsexml_7e');
    }
}
