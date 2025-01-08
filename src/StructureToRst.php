<?php

/**
 * File for class StructureToRst.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

namespace TMD\Documentation;

use SimpleXMLElement;

/**
 * Converts phpDoc `structure.xml` to reStructuredText.
 */
class StructureToRst
{
    public string $projectName = '';
    /** @var array<string, array{title: string, content: string}> */
    public array $files = [];

    private static function attr(SimpleXMLElement $elem, string $attr, string $default = ''): string
    {
        return ($elem[$attr] !== null) ? (string) $elem[$attr] : $default;
    }

    /**
     * Each line of the input string will be ensured to start with at least `$min_indent * 3` spaces.
     *
     * @param string $text       Input text.
     * @param int    $min_indent Minimum indentation level (non-positive value will cause this function to just return `$text`).
     *
     * @return string
     */
    private static function fixIndentation(string $text, int $min_indent): string
    {
        if ($min_indent <= 0) {
            return $text;
        }
        return preg_replace(
            '/^\s+$/',
            '',
            preg_replace(
                sprintf(
                    '/^ {0,%d}([^ \n\r\0])/m',
                    ( $min_indent * 3 ) - 1
                ),
                sprintf(
                    '%s$1',
                    str_repeat('   ', $min_indent)
                ),
                $text
            )
        );
    }

    private static function tag(SimpleXMLElement $tag): string
    {
        $tagName = (string) $tag['name'];
        $tagDesc = (string) $tag['description'];
        $tagType = (string) $tag['type'];
        $tagVar = (string) $tag['variable'];
        $tagVer = (string) $tag['version'];
        $tagLink = (string) $tag['link'];

        if (in_array($tagName, ['package', 'subpackage', 'psalm-type', 'psalm-import-type', 'psalm-suppress', 'example'], true)) {
            return ":{$tagName}: $tagDesc";
        }
        if ($tagName === 'return') {
            return ":return: {$tagDesc}" . PHP_EOL . ":returntype: {$tagType}";
        }
        if ($tagName === 'param') {
            return ":param {$tagType} {$tagVar}: {$tagDesc}";
        }
        if ($tagName === 'var') {
            return ":var {$tagType}: {$tagDesc}";
        }
        if ($tagName === 'deprecated') {
            return ":deprecated: ({$tagVer}) {$tagDesc}";
        }
        if ($tagName === 'link') {
            return ":link: `<{$tagLink}>`_ {$tagDesc}";
        }
        if ($tagName === 'throws') {
            return ":throws: {$tagType} {$tagDesc}";
        }

        return ':tag: Unknown';
    }

    private static function docblock(string &$content, mixed $docblock, int $indent = 0): void
    {
        if (!($docblock instanceof SimpleXMLElement)) {
            return;
        }
        $short = (string) $docblock->description;
        $long = (string) $docblock->{'long-description'};

        if ($short !== '') {
            $content .= static::fixIndentation($short . PHP_EOL . PHP_EOL, $indent);
        }
        if ($long !== '') {
            $content .= static::fixIndentation($long . PHP_EOL . PHP_EOL, $indent);
        }
        if (!$docblock->tag) {
            return;
        }
        foreach ($docblock->tag as $tag) {
            if (!($tag instanceof SimpleXMLElement)) {
                continue;
            }
            $content .= static::fixIndentation(self::tag($tag) . PHP_EOL, $indent);
        }
    }

    /**
     * CIET = Class Interface Enum Trait
     */
    private static function ciet(string &$title, string &$content, SimpleXMLElement $elem, string $titlePrefix, string $directive): void
    {
        $title = $titlePrefix . ' ' . (string) $elem->name;

        if ($elem->getName() === 'enum') {
            $content .= '.. ' . $directive . ' :: ' . (string) $elem->name . ' : ' . $elem->value . PHP_EOL . PHP_EOL;
        } else {
            $content .= '.. ' . $directive . ' :: ' . (string) $elem->name . PHP_EOL . PHP_EOL;
        }

        static::docblock($content, $elem->docblock, 1);

        $classImplements = (string) $elem->implements;
        if ($classImplements !== '') {
            $content .= static::fixIndentation(':implements: ' . $classImplements, 1) . PHP_EOL;
        }
        $classExtends = (string) $elem->extends;
        if ($classExtends !== '') {
            $content .= static::fixIndentation(':extends: ' . $classExtends, 1) . PHP_EOL;
        }

        foreach ($elem->constant as $constant) {
            /** @psalm-suppress RiskyTruthyFalsyComparison */
            if (!$constant) {
                continue;
            }
            $content .= PHP_EOL . '.. php:const :: ' . (string) $constant->name . ' : ' . (string) $constant->value . PHP_EOL . PHP_EOL;
            static::docblock($content, $constant->docblock, 1);
        }
        foreach ($elem->property as $property) {
            /** @psalm-suppress RiskyTruthyFalsyComparison */
            if (!$property) {
                continue;
            }
            $content .= PHP_EOL . '.. php:attr :: ' . (string) $property->name . ' : ' . (string) $property->default . PHP_EOL . PHP_EOL;
            static::docblock($content, $property->docblock, 1);
        }
        foreach ($elem->method as $method) {
            /** @psalm-suppress RiskyTruthyFalsyComparison */
            if (!$method) {
                continue;
            }
            $content .= PHP_EOL . '.. php:method :: ' . (string) $method->name . '() ' . PHP_EOL . PHP_EOL;
            static::docblock($content, $method->docblock, 1);
        }
    }

    public function parseXml(string $xml): int
    {
        $this->projectName = '';
        $this->files = [];

        $xmlRoot = null;
        try {
            $xmlRoot = new SimpleXMLElement($xml);
        } catch (\Exception $err) {
            return 1;
        }
        if (!$xmlRoot) {
            return 2;
        }
        if ($xmlRoot->getName() !== 'project') {
            return 3;
        }
        $this->projectName = self::attr($xmlRoot, 'name');

        foreach ($xmlRoot->file as $file) {
            if (!($file instanceof SimpleXMLElement)) {
                continue;
            }
            $filePath = self::attr($file, 'path');
            $this->files[$filePath] = [
                'title' => "File \"{$filePath}\"",
                'content' => '',
            ];
            static::docblock($this->files[$filePath]['content'], $file->docblock, 0);

            $fileNamespace = trim((string) $file->{'namespace-alias'}['name'], '\\');
            if ($fileNamespace !== '') {
                $this->files[$filePath]['content'] .= PHP_EOL . '.. php:namespace :: ' . $fileNamespace . PHP_EOL . PHP_EOL;
            }

            foreach ($file->class as $class) {
                /** @psalm-suppress RiskyTruthyFalsyComparison */
                if (!$class) {
                    continue;
                }
                /** @var SimpleXMLElement $class */
                static::ciet($this->files[$filePath]['title'], $this->files[$filePath]['content'], $class, 'Class', 'php:class');
            }
            foreach ($file->interface as $interface) {
                /** @psalm-suppress RiskyTruthyFalsyComparison */
                if (!$interface) {
                    continue;
                }
                /** @var SimpleXMLElement $interface */
                static::ciet($this->files[$filePath]['title'], $this->files[$filePath]['content'], $interface, 'Interface', 'php:interface');
            }

            foreach ($file->enum as $enum) {
                /** @psalm-suppress RiskyTruthyFalsyComparison */
                if (!$enum) {
                    continue;
                }
                /** @var SimpleXMLElement $enum */
                static::ciet($this->files[$filePath]['title'], $this->files[$filePath]['content'], $enum, 'Enum', 'php:enum');
            }

            foreach ($file->trait as $trait) {
                /** @psalm-suppress RiskyTruthyFalsyComparison */
                if (!$trait) {
                    continue;
                }
                /** @var SimpleXMLElement $trait */
                static::ciet($this->files[$filePath]['title'], $this->files[$filePath]['content'], $trait, 'Trait', 'php:trait');
            }
        }

        return 0;
    }

    public function parseXmlFile(string $xmlPath, string $apiPath): bool
    {
        if (
            !file_exists($xmlPath) ||
            !is_file($xmlPath) ||
            !is_readable($xmlPath) ||
            !file_exists($apiPath) ||
            !is_dir($apiPath) ||
            !is_writable($apiPath)
        ) {
            return false;
        }

        $xml = file_get_contents($xmlPath);
        if ($xml === false) {
            return false;
        }

        $res = $this->parseXml($xml);
        if ($res !== 0) {
            return false;
        }

        $index = "{$this->projectName} API" . PHP_EOL . str_repeat('=', strlen($this->projectName) + 4) . PHP_EOL . PHP_EOL . ".. toctree::" . PHP_EOL . "   :caption: Contents:" . PHP_EOL . "   :maxdepth: 1" . PHP_EOL . PHP_EOL;

        foreach ($this->files as $fileKey => $fileData) {
            $filePathName = preg_replace('/[^A-Za-z0-9_-]/', '_', $fileKey) . '.rst';
            $index .= "   " . $filePathName . PHP_EOL;
            $filePath = $apiPath . DIRECTORY_SEPARATOR . $filePathName;
            $fileContent = $fileData['title'] . PHP_EOL . str_repeat('=', strlen($fileData['title'])) . PHP_EOL . $fileData['content'];
            file_put_contents($filePath, $fileContent);
        }
        file_put_contents($apiPath . DIRECTORY_SEPARATOR . 'index.rst', $index);
        return true;
    }
}
