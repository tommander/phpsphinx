Overview
========

The actual workflow of the application is in the method :php:meth:`TMD\\Documentation\\PhpSphinx::generate_documentation`.

1. Find all PHP files and with each file:

   a. **Tokenize file**
   b. Transform to **code hierarchy**
   c. Remember names of namespaces encountered in the file
   d. **Format code hierarchy** with selected formatter
   e. Add file to index

2. Save index files and for root index, add references to namespace indexes.

:php:class:`TMD\\Documentation\\PhpSphinx` brings the functionality together and acts as application interface.

.. graphviz::

   digraph {
      start [style=filled, fillcolor=red, shape=Mcircle];
      end [style=filled, fillcolor=red, shape=Mcircle];

      "PhpSphinx\n\ndo_run\ngenerate_documentation" [style=filled, fillcolor=aqua, shape=box];
      "DirList\n\nscandir_recursive" [style=filled, fillcolor=aqua, shape=box];
      "Tokenizer\n\ntokenize_file" [style=filled, fillcolor=aqua, shape=box];
      "DocblockExtract\n\nget_code_hierarchy" [style=filled, fillcolor=aqua, shape=box];
      "Formatter\n\nformat" [style=filled, fillcolor=aqua, shape=box];
      "FileIndexer\n\nstart" [style=filled, fillcolor=aqua, shape=box];
      "FileIndexer\n\nadd" [style=filled, fillcolor=aqua, shape=box];
      "FileIndexer\n\nfinish" [style=filled, fillcolor=aqua, shape=box];

      "Create filename." [style=filled, fillcolor=aqua, shape=box];
      "Add to index." [style=filled, fillcolor=aqua, shape=box];
      "Save file." [style=filled, fillcolor=aqua, shape=box];
      "Loop." [style=filled, fillcolor=gray, shape=diamond];

      "Artifact\n\nFilesIndex" [style=filled, fillcolor=green];
      "Artifact\n\nFileInfos" [style=filled, fillcolor=green];
      "Artifact\n\nTokenList" [style=filled, fillcolor=green];
      "Artifact\n\nCodeHierarchy" [style=filled, fillcolor=green];
      "Artifact\n\nOutputContent" [style=filled, fillcolor=green];

      start -> "PhpSphinx\n\ndo_run\ngenerate_documentation";
      
      "PhpSphinx\n\ndo_run\ngenerate_documentation" -> "FileIndexer\n\nstart";
      "FileIndexer\n\nstart" -> "Artifact\n\nFilesIndex";
      "FileIndexer\n\nadd" -> "Artifact\n\nFilesIndex";
      "Artifact\n\nFilesIndex" -> "FileIndexer\n\nfinish";
      "FileIndexer\n\nfinish" -> end;

      "PhpSphinx\n\ndo_run\ngenerate_documentation" -> "DirList\n\nscandir_recursive";
      "DirList\n\nscandir_recursive" -> "Artifact\n\nFileInfos";
      "Artifact\n\nFileInfos" -> "Loop.";
      "Loop." -> "Tokenizer\n\ntokenize_file";
      "Tokenizer\n\ntokenize_file" -> "Artifact\n\nTokenList";
      "Artifact\n\nTokenList" -> "DocblockExtract\n\nget_code_hierarchy";
      "DocblockExtract\n\nget_code_hierarchy" -> "Artifact\n\nCodeHierarchy";
      "Artifact\n\nCodeHierarchy" -> "Formatter\n\nformat";
      "Formatter\n\nformat" -> "Artifact\n\nOutputContent";
      "Artifact\n\nOutputContent" -> "Create filename.";
      "Create filename." -> "Add to index.";
      "Add to index." -> "Save file.";
      "Add to index." -> "FileIndexer\n\nadd";
      "Save file." -> "Loop.";
      "Loop." -> end;

   }