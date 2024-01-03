# PhpSphinx

A PHP script for use in terminal. This script scans the given input directory for PHP source files, extracts all docblocks and generates a Sphinx-ready API documentation in the output folder.

Can be (actually, is meant to be) chained with Sphinx to automate generating of documentation.

## Status

:hatching_chick: **Beta** ![PHP QA](https://github.com/tommander/phpsphinx/actions/workflows/php.yml/badge.svg) ![Build Docs](https://github.com/tommander/phpsphinx/actions/workflows/docs.yml/badge.svg)

Code here is relatively safe to try, as it is checked by static analysis and is covered by unit tests to some extent.

But it needs testing. A lot. And code review. And many improvements.

## Usage

```sh
# Generate documentation
php /path/to/phpsphinx/index.php --format="rst" --inputdir="/path/to/src" --outputdir="/path/to/docs/api"
# Help
php /path/to/phpsphinx/index.php --help
# Version
php /path/to/phpsphinx/index.php --version
```

## Example

This project uses itself and Sphinx to generate its own code documentation that is available on GitHub Pages.

1. Empty folder `/docs/source/api`
2. Generate RST documentation in `/docs/source/api`
3. Run sphinx in `/docs`

The whole documentation is saved in `/docs/source` and the automatically generated code documentation in the subfolder `api`; this allows for a static documentation that wraps the code documentation and is ignored by the automatic documentation tool. There we can have a documentation of processes, troubleshooting, detailed explanations etc. that should not be included along with the code.

You can examine the composer script "docs" in `composer.json`.

## Installation

Prerequisites:

1. [git](https://git-scm.com/)
2. [composer](https://getcomposer.org/)
3. [Sphinx](https://www.sphinx-doc.org/en/master/)

```sh
cd /some/path
git clone <this_repo> phpsphinx
cd phpsphinx
composer install
```

## Quality Assurance

[PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) with coding standard `WordPress`.

[psalm](https://psalm.dev/) for static analysis.

[phpunit](https://github.com/sebastianbergmann/phpunit) for unit testing.

```sh
composer qa
composer docs
```

## Issues

Please track in [GitHub Issues](https://github.com/tommander/phpsphinx/issues).

## Pull Requests

[Contributing](.github/CONTRIBUTING.md).

## Documentation

[Documentation on GitHub Pages](https://tommander.github.io/phpsphinx/).

## License

[MIT License](LICENSE).
