# PhpSphinx

![PHP QA](https://github.com/tommander/phpsphinx/actions/workflows/php.yml/badge.svg) ![Build Docs](https://github.com/tommander/phpsphinx/actions/workflows/docs.yml/badge.svg)

A PHP script for use in command line. This script scans the given input directory for PHP source files, extracts all docblocks and generates a Sphinx-ready API documentation in the output folder.

Can be (actually, is meant to be) chained with Sphinx to automate generating of documentation.

## Installation

Prerequisites:

1. [git](https://git-scm.com/)
2. [composer 2.2+](https://getcomposer.org/)
3. [Sphinx](https://www.sphinx-doc.org/en/master/)
4. [PHP 7.4+](https://php.net)

Note: if your PHP version is different from 8.2, you *might* need to run `composer update`.

### Composer

First require the package as a dev dependency.

```sh
cd /path/to/your/project
composer require --dev tommander/phpsphinx
```

Then you can generate documentation like this:

```sh
vendor/bin/phpsphinx --format="rst" --inputdir="/path/to/src" --outputdir="/path/to/docs/api"
```

### Manual

First clone the repo and install dependencies.

```sh
cd /some/path
git clone https://github.com/tommander/phpsphinx
cd phpsphinx
composer install
```

Then you can generate documentation like this:

```sh
php bin/phpsphinx --format="rst" --inputdir="/path/to/src" --outputdir="/path/to/docs/api"
```

## Example

This project uses itself and Sphinx to generate its own code documentation that is available on GitHub Pages.

1. Empty folder `/docs/source/api`
2. Generate RST documentation in `/docs/source/api`
3. Run sphinx in `/docs`

The whole documentation is saved in `/docs/source` and the automatically generated code documentation in the subfolder `api`; this allows for a static documentation that wraps the code documentation and is ignored by the automatic documentation tool. There we can have a documentation of processes, troubleshooting, detailed explanations etc. that should not be included along with the code.

You can examine the composer script "docs" in [composer.json](composer.json) and the [Build Docs GH Workflow](.github/workflows/docs.yml).

### Chain with Sphinx

First check the [Makefile](Makefile) that the `SOURCEDIR` and `BUILDDIR` variables are correct.

Then verify that you have Sphinx installed.

```sh
sphinx-build --version
```

If not, follow the [Sphinx Installation Guide](https://www.sphinx-doc.org/en/master/usage/installation.html). If you just don't have it available globally, make sure to adjust the sphinx-build command in the [Makefile](Makefile).

Then the flow that this project uses is as follows:

```sh
# Empty the folder where API documentation is generated by PhpSphinx.
# This way renamed/deleted files won't stay in there.
rm -rf docs/source/api/*

# Keep the folder even if we don't generate any API docs.
touch docs/source/api/.gitkeep

# Run PhpSphinx.
@php bin/phpsphinx --inputdir="." --outputdir="docs/source/api"

# Sphinx cleanup.
make clean

# Sphinx build docs in HTML format.
make html
```

Now you should have your beautiful documentation in `docs/build`.

This flow is just an example. Feel free to adjust it to your needs.

## Quality Assurance

It is recommended to run QA check before pushing anything to the repo. For branches "main" and "devel", this check runs automatically on a push/PR anyway.

[PHP_CodeSniffer](https://github.com/squizlabs/PHP_CodeSniffer) with coding standards `PSR-12` and `PHP-Compatibility`.

[psalm](https://psalm.dev/) for static analysis.

[phpunit](https://github.com/sebastianbergmann/phpunit) for unit testing.

```sh
composer validate --strict
composer qa
```

## Issues

Please track in [GitHub Issues](https://github.com/tommander/phpsphinx/issues).

## Pull Requests

[Contributing](.github/CONTRIBUTING.md).

## Documentation

[Documentation on GitHub Pages](https://tommander.github.io/phpsphinx/).

## License

[MIT License](LICENSE).
