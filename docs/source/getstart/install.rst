Installation
============
This installation guide shows command-line installation of the project (for both users and contributors).

Prerequisites:

1. `php <https://php.net>`_ because this is a PHP project.

2. `git <https://git-scm.com/>`_ for retrieving the repo.

3. `composer <https://getcomposer.org/>`_ for dependency management.

4. `Sphinx <https://www.sphinx-doc.org/en/master/>`_ for final documentation building.

Step 1 - Find Installation Folder
---------------------------------
Find some installation folder, where a new subfolder will be created.

.. code:: sh

   cd /some/path

Step 2 - Clone GitHub Repo
--------------------------
Now we will clone the repository into the subfolder ``phpsphinx``.

.. code:: sh

   git clone https://github.com/tommander/phpsphinx.git phpsphinx

Alternatively, you can download the source code from GitHub manually to the installation folder.

.. code:: sh

   # Download the code

   unzip phpsphinx.zip

Step 3 - Move To the New Folder
-------------------------------
Move to the newly created folder.

.. code:: sh

   cd phpsphinx

Step 4 - Install Dependencies
-----------------------------
Install dependencies needed for the app to run.

.. code:: sh

   composer install

Step 5 - Test Build (optional)
------------------------------
This way you will build documentation for the project, and this should go without issues if everything went well. If not, check the output (missing dependencies, misconfiguration, ...).

.. code:: sh

   composer docs
