User guide
==========
App is meant to be used in command-line.

Available parameters:

--format=format   Format of the generated documentation (currently `rst` and `html`).
--inputdir=path   Relative or absolute path to the input directory (which contains PHP source files).
--outputdir=path  Relative or absolute path to the output directory (which contains generated documentation).
-h, --help        Shows help.
--version         Shows version.

Parameters ``--format``, ``--inputdir`` and ``--outputdir`` are all required, so each call to the app must also be accompanied by values for these parameters.

.. code:: sh

   php bin/phpsphinx --format="rst" --inputdir="src/" --outputdir="docs/source/api"

If you want to define only specific files to read by the script or some other configuration, it needs to be changed in the code directly. For this specific case, you might want to check :php:meth:`TMD\\Documentation\\PhpSphinx::generate_documentation` in the part where :php:meth:`TMD\\Documentation\\DirList::scandir_recursive` is called, because there you can control included and excluded filenames via regular expressions.

In some bright future, more settings will be available here so that code adjustments are no longer necessary.
