<?php

/**
 * File for class TokenizerTest.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation\Tests;

use PHPUnit\Framework\TestCase;
use TMD\Documentation\Tokenizer;

/**
 * Test class for Tokenizer.
 */
final class TokenizerTest extends BaseTest
{
    /**
     * Test of function `tokenize_file`.
     *
     * @return void
     */
    public function testTokenizeFile(): void
    {
        // 1/ Empty.
        $file = '';
        $expected = false;
        $result = Tokenizer::tokenizeFile($file);
        self::assertEquals($expected, $result, 'test_format_1');

        // 1/ Empty.
        $file = \TMD\Documentation\Helper::makePath(__DIR__, 'TestAssets', 'to_tokenize.php.txt');
		// phpcs:disable WordPress.Arrays.ArrayDeclarationSpacing.AssociativeArrayFound
        $expected = array(
            array( 'name' => 'T_OPEN_TAG', 'content' => '<?php' . PHP_EOL ),
            array( 'name' => 'T_DOC_COMMENT', 'content' => '/**' . PHP_EOL . ' * File comment.' . PHP_EOL . ' * ' . PHP_EOL . ' * @package Package.' . PHP_EOL . ' */' ),
            array( 'name' => 'T_DECLARE', 'content' => 'declare' ),
            array( 'name' => 'string', 'content' => '(' ),
            array( 'name' => 'T_STRING', 'content' => 'strict_types' ),
            array( 'name' => 'string', 'content' => '=' ),
            array( 'name' => 'T_LNUMBER', 'content' => '1' ),
            array( 'name' => 'string', 'content' => ')' ),
            array( 'name' => 'string', 'content' => ';' ),
            array( 'name' => 'T_NAMESPACE', 'content' => 'namespace' ),
            array( 'name' => 'T_NAME_QUALIFIED', 'content' => 'TMD\\Documentation\\Tests\\Assets' ),
            array( 'name' => 'string', 'content' => ';' ),
            array( 'name' => 'T_DOC_COMMENT', 'content' => '/**' . PHP_EOL . ' * Class comment.' . PHP_EOL . ' */' ),
            array( 'name' => 'T_CLASS', 'content' => 'class' ),
            array( 'name' => 'T_STRING', 'content' => 'HelloWorld' ),
            array( 'name' => 'string', 'content' => '{' ),
            array( 'name' => 'string', 'content' => '}' ),
        );
		// phpcs:enable
        $result = Tokenizer::tokenizeFile($file);
        self::assertEquals($expected, $result, 'test_format_2');
    }
}
