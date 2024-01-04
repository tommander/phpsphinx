About
=====

A PHP application for automatic generation of documentation for PHP projects.

The output is meant to be included in a documentation that will be built by Sphinx.

Use case
--------

Suppose you have a folder with a PHP project. All paths from now on will be relative to this folder.

In this folder you have:

- few PHP files

- subfolder ``src`` with PHP files

- subfolder ``tests`` with PHP unit test files

- a subfolder ``docs/build`` that contains final documentation

- a subfolder ``docs/source`` that contains documentation ready for processing by Sphinx

- a subfolder ``docs/source/api`` that contains RST-formatted code documentation

So your folder has a structure similar to this:

.. code::

   .
   +--+ docs
   |  +--+ build
   |  +--+ source
   |     +--+ api
   +--+ src
   +--+ tests


PhpSphinx scans all PHP files in the folder and creates an RST-formatted documentation based on the PHP code and docblock comments. For all these files a namespace-based naming will be used. These all will be saved to ``docs/source/api``.

Afterwards Sphinx can process the whole folder ``docs/source``, so the generated documentation is one section in the documentation, and the final documentation is saved in ``docs/build``.

The whole documentation process is fully automated and can be executed e.g. via composer scripts.

After initial setup, the only effort is to keep up-to-date the static documentation in ``docs/source`` (all outside the subfolder ``api``).

.. code:: json

   {
   	"scripts": {
   		"phpdocs": [
   			"rm -rf docs/source/api/*",
   			"touch docs/source/api/.gitkeep",
   			"@php index.php --inputdir=\".\" --outputdir=\"docs/source/api\""
   		],
   		"sphinx": [
   			"make clean",
   			"make html"
   		],
   		"docs": [
   			"@phpdocs",
   			"@sphinx"
   		]
   	}
   }

