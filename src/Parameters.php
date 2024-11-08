<?php

/**
 * File for class Parameters.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

use TMD\Documentation\Interfaces\FormatterInterface;

/**
 * The Parameters class prepares and validates program parameters.
 */
class Parameters
{
    /**
     * Short parameters.
     *
     * @var string
     */
    public string $short_params = 'h';
    /**
     * Long parameters.
     *
     * @var array
     */
    public array $long_params = array(
        'inputdir:',
        'outputdir:',
        'format:',
        'help',
        'version',
    );
    /**
     * List of parameters with their (optional) value.
     *
     * @var array
     */
    public array $params = array();

    /**
     * Get parameters, evaluate them and decide the next action.
     *
     * @param array|null $opts_override Read options from here instead of `getopt()`.
     * @param callable   $before        Callback that is triggered before checking starts, but after parameters are read.
     *
     * @return bool|string String is always an error message, `false` is an unknown error and `true` is success.
     */
    public function prepareParams(array|null $opts_override = null, callable $before = null): bool|string
    {
        // Parse script options.
        if (is_array($opts_override) === true) {
            $this->params = $opts_override;
        } else {
            $this->params = getopt($this->short_params, $this->long_params);
        }

        if ($before) {
            $res = call_user_func($before);
            if (true !== $res) {
                return false;
            }
        }

        // Inputdir option not specified => end.
        if (array_key_exists('inputdir', $this->params) !== true) {
            return '[ERROR] Input directory not specified.';
        }

        // Outputdir option not specified => end.
        if (array_key_exists('outputdir', $this->params) !== true) {
            return '[ERROR] Output directory not specified.';
        }

        if (array_key_exists('format', $this->params) !== true) {
            $this->params['format'] = 'rst';
        }

        $input_directory = Helper::makeString($this->params['inputdir']);
        $output_directory = Helper::makeString($this->params['outputdir']);
        $format = Helper::makeString($this->params['format'] ?? 'rst');

        // Input dir path empty or does not exist => end.
        if ('' === $input_directory || file_exists($input_directory) !== true) {
            return '[ERROR] Input directory "' . $input_directory . '" is empty or does not exist.';
        }

        // Output dir path empty or does not exist => end.
        if ('' === $output_directory || file_exists($output_directory) !== true) {
            return '[ERROR] Output directory "' . $output_directory . '" is empty or does not exist.';
        }

        if (in_array($format, array_keys(FormatterInterface::FORMATS), true) !== true) {
            return '[ERROR] Unknown format "' . $format . '". Known formats are "' . implode('", "', array_keys(FormatterInterface::FORMATS)) . '".';
        }

        return true;
    }
}
