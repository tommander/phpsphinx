<?php

/**
 * File for class ParametersTest.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation\Tests;

use PHPUnit\Framework\TestCase;
use TMD\Documentation\Parameters;

/**
 * Test class for Parameters.
 */
final class ParametersTest extends BaseTest
{
    /**
     * Parameters instance.
     *
     * @var Parameters|null
     */
    private ?Parameters $parameters = null;

    /**
     * Test setup - create new instance of Parameters.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->parameters = new Parameters();
    }

    /**
     * Test teardown - free the instance of Parameters.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->parameters);
    }

    /**
     * Test of function `prepareParams`.
     *
     * @return void
     */
    public function testPrepareParams(): void
    {
        if (is_a($this->parameters, Parameters::class) !== true) {
            self::fail('test_prepareparams_0');
            return;
        }

        // 1/ Empty.
        $opts_override = array();
        $expected_result = '[ERROR] Input directory not specified.';
        $expected_value = array();
        ob_start();
        $result = $this->parameters->prepareParams($opts_override);
        ob_end_clean();
        self::assertEquals($expected_result, $result, 'test_prepareparams_1a');
        self::assertEquals($expected_value, $this->parameters->params, 'test_prepareparams_1b');

        // 2/ OK, Input Directory specified.
        $opts_override = array(
            'inputdir' => '/nope',
        );
        $expected_result = '[ERROR] Output directory not specified.';
        $expected_value = array(
            'inputdir' => '/nope',
        );
        $result = $this->parameters->prepareParams($opts_override);
        self::assertEquals($expected_result, $result, 'test_prepareparams_2a');
        self::assertEquals($expected_value, $this->parameters->params, 'test_prepareparams_2b');

        // 3/ Fine, Output Directory specified.
        $opts_override = array(
            'inputdir' => '/nope',
            'outputdir' => '/hello',
        );
        $expected_result = '[ERROR] Input directory "/nope" is empty or does not exist.';
        $expected_value = array(
            'inputdir' => '/nope',
            'outputdir' => '/hello',
            'format' => 'rst',
        );
        $result = $this->parameters->prepareParams($opts_override);
        self::assertEquals($expected_result, $result, 'test_prepareparams_3a');
        self::assertEquals($expected_value, $this->parameters->params, 'test_prepareparams_3b');

        // 4/ Yeah, now it's ok.
        $opts_override = array(
            'inputdir' => __DIR__,
            'outputdir' => '/hello',
        );
        $expected_result = '[ERROR] Output directory "/hello" is empty or does not exist.';
        $expected_value = array(
            'inputdir' => __DIR__,
            'outputdir' => '/hello',
            'format' => 'rst',
        );
        $result = $this->parameters->prepareParams($opts_override);
        self::assertEquals($expected_result, $result, 'test_prepareparams_4a');
        self::assertEquals($expected_value, $this->parameters->params, 'test_prepareparams_4b');

        // 5/ Sheeesh, how many things are still wrong??
        $opts_override = array(
            'inputdir' => __DIR__,
            'outputdir' => __DIR__,
            'format' => 'pdf',
        );
        $expected_result = '[ERROR] Unknown format "pdf". Known formats are "rst", "md", "html".';
        $expected_value = array(
            'inputdir' => __DIR__,
            'outputdir' => __DIR__,
            'format' => 'pdf',
        );
        $result = $this->parameters->prepareParams($opts_override);
        self::assertEquals($expected_result, $result, 'test_prepareparams_5a');
        self::assertEquals($expected_value, $this->parameters->params, 'test_prepareparams_5b');

        // 6/ OK this time it's gotta go!
        $opts_override = array(
            'inputdir' => __DIR__,
            'outputdir' => __DIR__,
            'format' => 'rst',
        );
        $expected_result = true;
        $expected_value = $opts_override;
        $result = $this->parameters->prepareParams($opts_override);
        self::assertEquals($expected_result, $result, 'test_prepareparams_6a');
        self::assertEquals($expected_value, $this->parameters->params, 'test_prepareparams_6b');
    }
}
