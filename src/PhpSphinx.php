<?php

/**
 * File for class PhpSphinx.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

use TMD\Documentation\Interfaces\FormatterInterface;
use TMD\Documentation\FormatterRst;

/**
 * The PhpSphinx class is glueing together features to extract and render inline documentation from PHP source files.
 *
 * @psalm-type FileIndex = array<string, array{content: string, filename: string, hierarchy: array, tokens: array}>
 */
class PhpSphinx
{
    /**
     * Help text to show in command-line.
     *
     * @var string
     */
    public static string $help_text = <<<EOS
    Transforms PHP class files docblocks to restructuredText files.

    Usage:
      phpsphinx.php --inputdir=<dir> --outputdir=<dir> [--format=<format>]
      phpsphinx.php -h | --help
      phpsphinx.php --version

    Options:
      -h --help           Show this screen.
      --version           Show version.
      --inputdir=<dir>    Existing input directory with PHP files.
      --outputdir=<dir>   Existing output directory for documentation files.
      --format=<format>   Output files format.
    
    Values:
      dir      File path.
      format   Output file format. One of: rst,md,html.
    EOS;
    /**
     * App version.
     *
     * @var string
     */
    public static string $version_text = 'v0.1.0';
    /**
     * App name.
     *
     * @var string
     */
    public static string $name_text = 'PHPSphinx';

    /**
     * Parameters instance.
     *
     * @var Parameters
     */
    private Parameters $parameters;
    /**
     * FileIndexer instance.
     *
     * @var FileIndexer
     */
    private FileIndexer $fileindexer;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->parameters = new Parameters();
        $this->fileindexer = new FileIndexer();
    }

    /**
     * Echo text and send end-of-line.
     *
     * @param string $text Text.
     *
     * @return void
     */
    private function writeln(string $text): void
    {
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $text . PHP_EOL;
    }

    /**
     * Check parameters that just show some text and exit (help and version).
     *
     * @return bool `false` in case a text was shown and the program should exit, `true` otherwise.
     */
    public function preParamCheck(): bool
    {
        // No options => show help.
        if (count($this->parameters->params) === 0) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo self::$help_text . PHP_EOL;
            return false;
        }

        // Help option => show help.
        if (array_key_exists('h', $this->parameters->params) === true || array_key_exists('help', $this->parameters->params) === true) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo self::$help_text . PHP_EOL;
            return false;
        }

        // Version option => show version.
        if (array_key_exists('version', $this->parameters->params) === true) {
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo self::$name_text . ' ' . self::$version_text . PHP_EOL;
            return false;
        }

        return true;
    }

    /**
     * Entrypoint for the app.
     *
     * @param array|null $opts_override Options used by the app should be these instead of from `getopt()`.
     * @param bool       $dry_run       If `true`, no file will be saved.
     *
     * @return void
     */
    public function doRun(array|null $opts_override = null, bool $dry_run = false): void
    {
        // Get program parameters.
        $res_getparams = $this->parameters->prepareParams(
            $opts_override,
            array( $this, 'preParamCheck' )
        );
        if (is_string($res_getparams) === true) {
            $this->writeln($res_getparams);
            return;
        }
        if (true !== $res_getparams) {
            return;
        }

        $format = Helper::makeString($this->parameters->params['format']);
        $inputdir = Helper::makeString($this->parameters->params['inputdir']);
        $outputdir = Helper::makeString($this->parameters->params['outputdir']);

        $this->generateDocumentation($format, $inputdir, $outputdir, $dry_run);
    }

    /**
     * Return current commit.
     *
     * Reading `/.git/HEAD` for reference and getting that reference for commit number.
     *
     * @return string|false
     */
    public function getCurrentCommit(): string|false
    {
        $file_head = Helper::makePath(__DIR__, '..', '.git', 'HEAD');
        if (file_exists($file_head) !== true) {
            return false;
        }
        $file_head_content = file_get_contents($file_head);
        if (preg_match('/^(.*):\s+(.*)$/m', $file_head_content, $matches) !== 1) {
            return false;
        }

        $file_ref = Helper::makePath(__DIR__, '..', '.git', $matches[2]);
        if (file_exists($file_ref) !== true) {
            return false;
        }
        $file_ref_content = file_get_contents($file_ref);
        if (preg_match('/[abcdef0123456789]{40}/', $file_ref_content, $matches_ref) !== 1) {
            return false;
        }

        return $matches_ref[0];
    }

    /**
     * Generates documentation with the given input.
     *
     * @param string $format    Format (rst,md,html).
     * @param string $inputdir  Input directory with PHP source files and inline docs.
     * @param string $outputdir Output directory for documentation.
     * @param bool   $dry_run   If `true`, no file will be saved.
     *
     * @return void
     */
    public function generateDocumentation(string $format, string $inputdir, string $outputdir, bool $dry_run = false): void
    {
        if ('' === $inputdir || file_exists($inputdir) !== true) {
            $this->writeln('[ERROR] Input directory "' . $inputdir . '" does not exist.');
            return;
        }
        if ('' === $outputdir || file_exists($outputdir) !== true) {
            $this->writeln('[ERROR] Output directory "' . $outputdir . '" does not exist.');
            return;
        }
        if ('' === $format || in_array($format, array_keys(FormatterInterface::FORMATS), true) !== true) {
            $this->writeln('[ERROR] Format "' . $format . '" is unknown. Correct is one of (' . implode(', ', array_keys(FormatterInterface::FORMATS)) . ').');
            return;
        }

        $namespace_list = array();
        $this->fileindexer->start();

        /**
         * Hello.
         *
         * @var class-string<FormatterInterface>
         */
        $formatter_intf = FormatterInterface::FORMATS[ $format ]['class'];
        $commit = $this->getCurrentCommit();

        // Recursive dir scan.
        $this->writeln('Searching for PHP source files...');
        $artifact_fileinfos = DirList::scandirRecursive($inputdir, array( '/\.php$/' ), array( '/vendor\//', '/\.asset\.php$/', '/\/tests\/Test/' ));
        $this->writeln('Found ' . strval(count($artifact_fileinfos)) . ' files.');
        foreach ($artifact_fileinfos as $phpfile_info) {
            $phpfile_full = $phpfile_info->getPathname();
            $phpfile_name = $phpfile_info->getFilename();

            $this->writeln('Processing "' . $phpfile_full . '"...');

            // Tokenize content.
            $this->writeln('   Tokenizing...');
            $artifact_tokenlist = Tokenizer::tokenizeFile($phpfile_full);
            if (false === $artifact_tokenlist) {
                $this->writeln('   [WARNING] File cannot be tokenized.');
                continue;
            }

            // Create code hierarchy and collect namespaces.
            $this->writeln('   Creating code hierarchy...');
            $namespace = '';
            $artifact_codehierarchy = DocblockExtract::getCodeHierarchy($artifact_tokenlist, $phpfile_name, $namespace);
            if ('' !== $namespace && in_array($namespace, $namespace_list, true) !== true) {
                $namespace_list[] = $namespace;
            }

            // Format code map.
            $this->writeln('   Formatting code map...');
            $file_relative = Helper::relativePath(realpath(Helper::makePath(__DIR__, '..')), realpath($phpfile_full));
            $artifact_outputcontent = $formatter_intf::format($phpfile_name, $artifact_codehierarchy, Helper::makeString($commit), $file_relative);

            // Name output file and create output file path.
            $this->writeln('   Creating file name...');
            $outfile_ext = FormatterInterface::FORMATS[ $format ]['ext'];
            $outfile_name = Helper::makePath(Helper::fixFilename($namespace), Helper::fixFilename($phpfile_name) . $outfile_ext);
            $outfile_path = Helper::makePath($outputdir, Helper::fixFilename($namespace));
            if (file_exists($outfile_path) !== true) {
                mkdir($outfile_path, 0755, true);
            }

            // Add file to index.
            $this->writeln('   Adding file to index...');
            $index_key = ( ( '' === $namespace ) ? '.' : addslashes($namespace) );
            $this->fileindexer->add($index_key, Helper::fixFilename($phpfile_name));

            // Save to file.
            $this->writeln('   File will be saved to "' . $outfile_name . '"');
            if (true !== $dry_run) {
                $outfile = Helper::makePath($outputdir, $outfile_name);
                file_put_contents($outfile, $artifact_outputcontent);
            }

            $this->writeln('   Done.');
        }

        $this->writeln('Saving index files...');
        $this->fileindexer->finish(
            $formatter_intf,
            $namespace_list,
            Helper::makeString($commit),
            $dry_run,
            $outputdir,
            $format
        );

        $this->writeln('Script finished.');
    }
}
