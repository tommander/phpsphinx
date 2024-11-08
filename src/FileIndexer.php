<?php

/**
 * File for class FileIndexer.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

use TMD\Documentation\Interfaces\FormatterInterface;

/**
 * Class for creating folder indices.
 */
class FileIndexer
{
    /**
     * This holds the data of indices content. Array key is the subfolder name ("." in case of root folder), array value is a list of file names in that subfolder (non-recursively).
     *
     * @var array<string, list<string>>
     */
    public array $data = array();

    /**
     * Start collecting data about indices.
     *
     * @return void
     */
    public function start(): void
    {
        $this->data = array();
        $this->data['.'] = array();
    }

    /**
     * Add a file to a subfolder. The subfolder key is added if it does not exist.
     *
     * @param string $index Index.
     * @param string $file  File.
     *
     * @return void
     */
    public function add(string $index, string $file): void
    {
        if (array_key_exists($index, $this->data) !== true) {
            $this->data[ $index ] = array();
        }
        $this->data[ $index ][] = $file;
    }

    /**
     * Finish collecting files for indices and save all indices to files.
     *
     * @param class-string<FormatterInterface> $formatter_intf Formatter that is used to output files (a FormatterInterface descendant class name).
     * @param array<string>                    $namespace_list List of namespaces (subfolders in the build output folder).
     * @param string                           $commit         Current commit.
     * @param bool                             $dry_run        If `true`, files will not be saved. Otherwise all stays the same.
     * @param string                           $outputdir      Output directory.
     * @param string                           $format         Format (rst,md,html).
     *
     * @return void
     */
    public function finish(string $formatter_intf, array $namespace_list, string $commit, bool $dry_run, string $outputdir, string $format): void  // phpcs:ignore Squiz.Commenting.FunctionComment.IncorrectTypeHint
    {
        foreach ($this->data as $index_file => &$index_subfiles) {
            $index_content = $formatter_intf::getEmptyIndex(( '' === $index_file || '.' === $index_file ) ? 'API' : $index_file);
            $before_toc = '';
            $after_toc = $formatter_intf::generatedAutomatically(gmdate('c'), Helper::makeString($commit), '');

            if ('.' === $index_file) {
                foreach ($namespace_list as $one_namespace) {
                    $index_content = $formatter_intf::addToIndex(Helper::fixFilename($one_namespace) . '/index', $index_content);
                }
            }
            sort($index_subfiles);
            foreach ($index_subfiles as $index_subfile) {
                $index_content = $formatter_intf::addToIndex(Helper::fixFilename($index_subfile), $index_content);
            }

            $index_content = $formatter_intf::substitute($index_content, $before_toc, $after_toc, '', '');

            if (true !== $dry_run) {
                if ('.' === $index_file || '' === $index_file) {
                    file_put_contents(Helper::makePath($outputdir, 'index' . FormatterInterface::FORMATS[ $format ]['ext']), $index_content);
                } else {
                    file_put_contents(Helper::makePath($outputdir, Helper::fixFilename($index_file), 'index' . FormatterInterface::FORMATS[ $format ]['ext']), $index_content);
                }
            }
        }
    }
}
