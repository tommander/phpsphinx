<?php

/**
 * File for class HelperTest.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation\Tests;

use PHPUnit\Framework\TestCase;
use Stringable;
use TMD\Documentation\Helper;

/**
 * Test class for Helper.
 */
final class HelperTest extends BaseTest
{
    /**
     * Test of function `makeString`.
     *
     * @return void
     */
    public function testMakeString(): void
    {
        // 1/ Empty.
        $input = null;
        $expected = '';
        $result = Helper::makeString($input);
        self::assertEquals($expected, $result, 'test_makestring_1');

        // 2/ Empty string.
        $input = '';
        $expected = '';
        $result = Helper::makeString($input);
        self::assertEquals($expected, $result, 'test_makestring_2');

        // 3/ Non-empty string.
        $input = 'abcd';
        $expected = 'abcd';
        $result = Helper::makeString($input);
        self::assertEquals($expected, $result, 'test_makestring_3');

        // 4/ Integer.
        $input = 128;
        $expected = '128';
        $result = Helper::makeString($input);
        self::assertEquals($expected, $result, 'test_makestring_4');

        // 5/ Object
        $input = new class () implements Stringable {
            /**
             * Undocumented function
             *
             * @return string
             */
            public function __toString(): string
            {
                return 'HiRalph';
            }
        };
        $expected = 'HiRalph';
        $result = Helper::makeString($input);
        self::assertEquals($expected, $result, 'test_makestring_5');

        // 6/ Array.
        $input = array( '1' => '2' );
        $expected = '';
        $result = Helper::makeString($input);
        self::assertEquals($expected, $result, 'test_makestring_6');

        // 7/ Bool.
        $input = true;
        $expected = '1';
        $result = Helper::makeString($input);
        self::assertEquals($expected, $result, 'test_makestring_7');

        // 8/ Bool.
        $input = false;
        $expected = '';
        $result = Helper::makeString($input);
        self::assertEquals($expected, $result, 'test_makestring_8');
    }

    /**
     * Test of function `makePath`.
     *
     * @return void
     */
    public function testMakePath(): void
    {
        // 1/ Empty.
        $expected = '';
        $result = Helper::makePath();
        self::assertEquals($expected, $result, 'test_makepath_1');

        // 2/ Empty string.
        $expected = '';
        $result = Helper::makePath('');
        self::assertEquals($expected, $result, 'test_makepath_2');

        // 3/ Only dir sep.
        $expected = DIRECTORY_SEPARATOR;
        $result = Helper::makePath(DIRECTORY_SEPARATOR);
        self::assertEquals($expected, $result, 'test_makepath_3');

        // 3/ Only dir sep.
        $expected = DIRECTORY_SEPARATOR . 'Hello' . DIRECTORY_SEPARATOR;
        $result = Helper::makePath(DIRECTORY_SEPARATOR . 'Hello' . DIRECTORY_SEPARATOR);
        self::assertEquals($expected, $result, 'test_makepath_4');

        // 3/ Only dir sep.
        $expected = DIRECTORY_SEPARATOR . 'Hello' . DIRECTORY_SEPARATOR . 'World' . DIRECTORY_SEPARATOR;
        $result = Helper::makePath(
            DIRECTORY_SEPARATOR . 'Hello' . DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR . 'World' . DIRECTORY_SEPARATOR
        );
        self::assertEquals($expected, $result, 'test_makepath_5');
    }

    /**
     * Test of function `getFilename`.
     *
     * @return void
     */
    public function testGetFilename(): void
    {
        // 1/ Empty.
        $path = '';
        $expected = '';
        $result = Helper::getFilename($path);
        self::assertEquals($expected, $result, 'test_getfilename_1');

        // 1/ Empty.
        $path = DIRECTORY_SEPARATOR;
        $expected = '';
        $result = Helper::getFilename($path);
        self::assertEquals($expected, $result, 'test_getfilename_2');

        // 1/ Empty.
        $path = 'name' . DIRECTORY_SEPARATOR;
        $expected = '';
        $result = Helper::getFilename($path);
        self::assertEquals($expected, $result, 'test_getfilename_3');

        // 1/ Empty.
        $path = DIRECTORY_SEPARATOR . 'name' . DIRECTORY_SEPARATOR;
        $expected = '';
        $result = Helper::getFilename($path);
        self::assertEquals($expected, $result, 'test_getfilename_4');

        // 1/ Empty.
        $path = DIRECTORY_SEPARATOR . 'name';
        $expected = 'name';
        $result = Helper::getFilename($path);
        self::assertEquals($expected, $result, 'test_getfilename_5');

        // 1/ Empty.
        $path = 'name';
        $expected = 'name';
        $result = Helper::getFilename($path);
        self::assertEquals($expected, $result, 'test_getfilename_6');
    }

    /**
     * Test of function `relative_path`.
     *
     * @return void
     */
    public function testRelativePath(): void
    {
        // 1/ Empty.
        $from = '';
        $to = '';
        $separator = DIRECTORY_SEPARATOR;
        $expected = '';
        $result = Helper::relativePath($from, $to, $separator);
        self::assertEquals($expected, $result, 'test_XXX_1');

        // 1/ Empty.
        $from = '/usr/bin/bookreader';
        $to = '/home/bookster/book.pdf';
        $separator = DIRECTORY_SEPARATOR;
        $expected = '../../..' . $to;
        $result = Helper::relativePath($from, $to, $separator);
        self::assertEquals($expected, $result, 'test_XXX_1');
    }
}
