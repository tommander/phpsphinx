Technical guide
===============

The actual workflow of the application is in the method :php:meth:`TMD\\Documentation\\PhpSphinx::generate_documentation`.

1. Find all PHP files and with each file:

   a. **Tokenize file**
   b. Transform to **code hierarchy**
   c. Remember names of namespaces encountered in the file
   d. **Format code hierarchy** with selected formatter
   e. Add file to index

2. Save index files and for root index, add references to namespace indexes.

:php:class:`TMD\\Documentation\\PhpSphinx` brings the functionality together and acts as application interface.

.. raw:: html

   <svg width="280pt" height="1567pt"
   viewBox="0.00 0.00 279.50 1566.93" xmlns="http://www.w3.org/2000/svg"
   xmlns:xlink="http://www.w3.org/1999/xlink">
   <g id="graph0" class="graph" transform="scale(1 1) rotate(0) translate(4 1562.93)">
   	<!-- start -->
   	<a href="../api/tmd_documentation/phpsphinx.html"><g id="node1" class="node">
   		<title>start</title>
   		<ellipse fill="#ffcccc" stroke="black" cx="164" cy="-1532.28" rx="26.8" ry="26.8" />
   		<polyline fill="none" stroke="black" points="181.62,-1552.27 146.38,-1552.27 " />
   		<polyline fill="none" stroke="black" points="181.62,-1512.3 146.38,-1512.3 " />
   		<text text-anchor="middle" x="164" y="-1528.58" font-family="Times,serif"
   			font-size="14.00">start</text>
   	</g></a>
   	<!-- PhpSphinx\n\ndo_run\ngenerate_documentation -->
   	<a href="../api/tmd_documentation/phpsphinx.html"><g id="node3" class="node">
   		<title>PhpSphinx<br /><br />do_run<br />generate_documentation</title>
   		<polygon fill="#bbeeff" stroke="black"
   			points="239,-1469.63 89,-1469.63 89,-1400.63 239,-1400.63 239,-1469.63" />
   		<text text-anchor="middle" x="164" y="-1454.43" font-family="Times,serif"
   			font-size="14.00">PhpSphinx</text>
   		<text text-anchor="middle" x="164" y="-1423.43" font-family="Times,serif"
   			font-size="14.00">do_run</text>
   		<text text-anchor="middle" x="164" y="-1408.43" font-family="Times,serif"
   			font-size="14.00">generate_documentation</text>
   	</g></a>
   	<!-- start&#45;&gt;PhpSphinx\n\ndo_run\ngenerate_documentation -->
   	<g id="edge1" class="edge">
   		<title>start&#45;&gt;PhpSphinx\n\ndo_run\ngenerate_documentation</title>
   		<path fill="none" stroke="black"
   			d="M164,-1505.28C164,-1497.45 164,-1488.63 164,-1479.98" />
   		<polygon fill="black" stroke="black"
   			points="167.5,-1479.96 164,-1469.96 160.5,-1479.96 167.5,-1479.96" />
   	</g>
   	<!-- end -->
   	<a href="../api/tmd_documentation/phpsphinx.html"><g id="node2" class="node">
   		<title>end</title>
   		<ellipse fill="#ffcccc" stroke="black" cx="34" cy="-23.4" rx="23.3" ry="23.3" />
   		<polyline fill="none" stroke="black" points="49.48,-40.95 18.52,-40.95 " />
   		<polyline fill="none" stroke="black" points="49.48,-5.85 18.52,-5.85 " />
   		<text text-anchor="middle" x="34" y="-19.7" font-family="Times,serif" font-size="14.00">
   			end</text>
   	</g></a>
   	<!-- DirList\n\nscandir_recursive -->
   	<a href="../api/tmd_documentation/dirlist.html"><g id="node4" class="node">
   		<title>DirList\n\nscandir_recursive</title>
   		<polygon fill="#bbeeff" stroke="black"
   			points="178,-1364.63 66,-1364.63 66,-1310.63 178,-1310.63 178,-1364.63" />
   		<text text-anchor="middle" x="122" y="-1349.43" font-family="Times,serif"
   			font-size="14.00">DirList</text>
   		<text text-anchor="middle" x="122" y="-1318.43" font-family="Times,serif"
   			font-size="14.00">scandir_recursive</text>
   	</g></a>
   	<!-- PhpSphinx\n\ndo_run\ngenerate_documentation&#45;&gt;DirList\n\nscandir_recursive -->
   	<g id="edge7" class="edge">
   		<title>PhpSphinx\n\ndo_run\ngenerate_documentation&#45;&gt;DirList\n\nscandir_recursive</title>
   		<path fill="none" stroke="black"
   			d="M149.19,-1400.46C145.45,-1391.96 141.42,-1382.8 137.64,-1374.2" />
   		<polygon fill="black" stroke="black"
   			points="140.74,-1372.55 133.51,-1364.8 134.33,-1375.36 140.74,-1372.55" />
   	</g>
   	<!-- FileIndexer\n\nstart -->
   	<a href="../api/tmd_documentation/fileindexer.html"><g id="node8" class="node">
   		<title>FileIndexer\n\nstart</title>
   		<polygon fill="#bbeeff" stroke="black"
   			points="271.5,-1263.45 192.5,-1263.45 192.5,-1209.45 271.5,-1209.45 271.5,-1263.45" />
   		<text text-anchor="middle" x="232" y="-1248.25" font-family="Times,serif"
   			font-size="14.00">FileIndexer</text>
   		<text text-anchor="middle" x="232" y="-1217.25" font-family="Times,serif"
   			font-size="14.00">start</text>
   	</g></a>
   	<!-- PhpSphinx\n\ndo_run\ngenerate_documentation&#45;&gt;FileIndexer\n\nstart -->
   	<g id="edge2" class="edge">
   		<title>PhpSphinx\n\ndo_run\ngenerate_documentation&#45;&gt;FileIndexer\n\nstart</title>
   		<path fill="none" stroke="black"
   			d="M175.69,-1400.32C187.94,-1364.88 207.12,-1309.43 219.62,-1273.26" />
   		<polygon fill="black" stroke="black"
   			points="223,-1274.19 222.96,-1263.6 216.38,-1271.91 223,-1274.19" />
   	</g>
   	<!-- Artifact\n\nFileInfos -->
   	<a href="../artifacts/fileinfos.html"><g id="node16" class="node">
   		<title>Artifact\n\nFileInfos</title>
   		<ellipse fill="#eeffaa" stroke="black" cx="113" cy="-1236.45" rx="45.92" ry="38.37" />
   		<text text-anchor="middle" x="113" y="-1248.25" font-family="Times,serif"
   			font-size="14.00">Artifact</text>
   		<text text-anchor="middle" x="113" y="-1217.25" font-family="Times,serif"
   			font-size="14.00">FileInfos</text>
   	</g></a>
   	<!-- DirList\n\nscandir_recursive&#45;&gt;Artifact\n\nFileInfos -->
   	<g id="edge8" class="edge">
   		<title>DirList\n\nscandir_recursive&#45;&gt;Artifact\n\nFileInfos</title>
   		<path fill="none" stroke="black"
   			d="M119.64,-1310.57C118.92,-1302.74 118.12,-1293.88 117.32,-1285.1" />
   		<polygon fill="black" stroke="black"
   			points="120.79,-1284.54 116.4,-1274.9 113.82,-1285.17 120.79,-1284.54" />
   	</g>
   	<!-- Tokenizer\n\ntokenize_file -->
   	<a href="../api/tmd_documentation/tokenizer.html"><g id="node5" class="node">
   		<title>Tokenizer\n\ntokenize_file</title>
   		<polygon fill="#bbeeff" stroke="black"
   			points="147,-1090.27 61,-1090.27 61,-1036.27 147,-1036.27 147,-1090.27" />
   		<text text-anchor="middle" x="104" y="-1075.07" font-family="Times,serif"
   			font-size="14.00">Tokenizer</text>
   		<text text-anchor="middle" x="104" y="-1044.07" font-family="Times,serif"
   			font-size="14.00">tokenize_file</text>
   	</g></a>
   	<!-- Artifact\n\nTokenList -->
   	<a href="../artifacts/tokenlist.html"><g id="node17" class="node">
   		<title>Artifact\n\nTokenList</title>
   		<ellipse fill="#eeffaa" stroke="black" cx="100" cy="-962.08" rx="50.41" ry="38.37" />
   		<text text-anchor="middle" x="100" y="-973.88" font-family="Times,serif"
   			font-size="14.00">Artifact</text>
   		<text text-anchor="middle" x="100" y="-942.88" font-family="Times,serif"
   			font-size="14.00">TokenList</text>
   	</g></a>
   	<!-- Tokenizer\n\ntokenize_file&#45;&gt;Artifact\n\nTokenList -->
   	<g id="edge11" class="edge">
   		<title>Tokenizer\n\ntokenize_file&#45;&gt;Artifact\n\nTokenList</title>
   		<path fill="none" stroke="black"
   			d="M102.95,-1036.21C102.63,-1028.37 102.28,-1019.51 101.92,-1010.73" />
   		<polygon fill="black" stroke="black"
   			points="105.41,-1010.38 101.51,-1000.53 98.42,-1010.66 105.41,-1010.38" />
   	</g>
   	<!-- DocblockExtract\n\nget_code_hierarchy -->
   	<a href="../api/tmd_documentation/docblockextract.html"><g id="node6" class="node">
   		<title>DocblockExtract\n\nget_code_hierarchy</title>
   		<polygon fill="#bbeeff" stroke="black"
   			points="160.5,-887.9 37.5,-887.9 37.5,-833.9 160.5,-833.9 160.5,-887.9" />
   		<text text-anchor="middle" x="99" y="-872.7" font-family="Times,serif" font-size="14.00">
   			DocblockExtract</text>
   		<text text-anchor="middle" x="99" y="-841.7" font-family="Times,serif" font-size="14.00">
   			get_code_hierarchy</text>
   	</g></a>
   	<!-- Artifact\n\nCodeHierarchy -->
   	<a href="../artifacts/codehierarchy.html"><g id="node18" class="node">
   		<title>Artifact\n\nCodeHierarchy</title>
   		<ellipse fill="#eeffaa" stroke="black" cx="98" cy="-759.71" rx="70.01" ry="38.37" />
   		<text text-anchor="middle" x="98" y="-771.51" font-family="Times,serif"
   			font-size="14.00">Artifact</text>
   		<text text-anchor="middle" x="98" y="-740.51" font-family="Times,serif"
   			font-size="14.00">CodeHierarchy</text>
   	</g></a>
   	<!-- DocblockExtract\n\nget_code_hierarchy&#45;&gt;Artifact\n\nCodeHierarchy -->
   	<g id="edge13" class="edge">
   		<title>DocblockExtract\n\nget_code_hierarchy&#45;&gt;Artifact\n\nCodeHierarchy</title>
   		<path fill="none" stroke="black"
   			d="M98.74,-833.84C98.66,-826 98.57,-817.14 98.48,-808.36" />
   		<polygon fill="black" stroke="black"
   			points="101.98,-808.13 98.38,-798.16 94.98,-808.2 101.98,-808.13" />
   	</g>
   	<!-- Formatter\n\nformat -->
   	<a href="../api/tmd_documentation_interfaces/formatterinterface.html"><g id="node7" class="node">
   		<title>Formatter\n\nformat</title>
   		<polygon fill="#bbeeff" stroke="black"
   			points="133.5,-685.53 62.5,-685.53 62.5,-631.53 133.5,-631.53 133.5,-685.53" />
   		<text text-anchor="middle" x="98" y="-670.33" font-family="Times,serif"
   			font-size="14.00">Formatter</text>
   		<text text-anchor="middle" x="98" y="-639.33" font-family="Times,serif"
   			font-size="14.00">format</text>
   	</g></a>
   	<!-- Artifact\n\nOutputContent -->
   	<a href="../artifacts/outputcontent.html"><g id="node19" class="node">
   		<title>Artifact\n\nOutputContent</title>
   		<ellipse fill="#eeffaa" stroke="black" cx="98" cy="-557.35" rx="68.68" ry="38.37" />
   		<text text-anchor="middle" x="98" y="-569.15" font-family="Times,serif"
   			font-size="14.00">Artifact</text>
   		<text text-anchor="middle" x="98" y="-538.15" font-family="Times,serif"
   			font-size="14.00">OutputContent</text>
   	</g></a>
   	<!-- Formatter\n\nformat&#45;&gt;Artifact\n\nOutputContent -->
   	<g id="edge15" class="edge">
   		<title>Formatter\n\nformat&#45;&gt;Artifact\n\nOutputContent</title>
   		<path fill="none" stroke="black" d="M98,-631.47C98,-623.63 98,-614.77 98,-606" />
   		<polygon fill="black" stroke="black"
   			points="101.5,-605.8 98,-595.8 94.5,-605.8 101.5,-605.8" />
   	</g>
   	<!-- Artifact\n\nFilesIndex -->
   	<a href="../artifacts/filesindex.html"><g id="node15" class="node">
   		<title>Artifact\n\nFilesIndex</title>
   		<ellipse fill="#eeffaa" stroke="black" cx="80" cy="-210.98" rx="52.15" ry="38.37" />
   		<text text-anchor="middle" x="80" y="-222.78" font-family="Times,serif"
   			font-size="14.00">Artifact</text>
   		<text text-anchor="middle" x="80" y="-191.78" font-family="Times,serif"
   			font-size="14.00">FilesIndex</text>
   	</g></a>
   	<!-- FileIndexer\n\nstart&#45;&gt;Artifact\n\nFilesIndex -->
   	<g id="edge3" class="edge">
   		<title>FileIndexer\n\nstart&#45;&gt;Artifact\n\nFilesIndex</title>
   		<path fill="none" stroke="black"
   			d="M232.49,-1209.35C233.09,-1175.78 234,-1115.68 234,-1064.27 234,-1064.27 234,-1064.27 234,-392.16 234,-343.86 243.26,-324.34 215,-285.16 196.24,-259.15 165.29,-241.42 137.76,-229.94" />
   		<polygon fill="black" stroke="black"
   			points="138.77,-226.58 128.19,-226.13 136.19,-233.08 138.77,-226.58" />
   	</g>
   	<!-- FileIndexer\n\nadd -->
   	<a href="../api/tmd_documentation/fileindexer.html"><g id="node9" class="node">
   		<title>FileIndexer\n\nadd</title>
   		<polygon fill="#bbeeff" stroke="black"
   			points="119.5,-339.16 40.5,-339.16 40.5,-285.16 119.5,-285.16 119.5,-339.16" />
   		<text text-anchor="middle" x="80" y="-323.96" font-family="Times,serif"
   			font-size="14.00">FileIndexer</text>
   		<text text-anchor="middle" x="80" y="-292.96" font-family="Times,serif"
   			font-size="14.00">add</text>
   	</g></a>
   	<!-- FileIndexer\n\nadd&#45;&gt;Artifact\n\nFilesIndex -->
   	<g id="edge4" class="edge">
   		<title>FileIndexer\n\nadd&#45;&gt;Artifact\n\nFilesIndex</title>
   		<path fill="none" stroke="black" d="M80,-285.1C80,-277.27 80,-268.41 80,-259.63" />
   		<polygon fill="black" stroke="black"
   			points="83.5,-259.43 80,-249.43 76.5,-259.43 83.5,-259.43" />
   	</g>
   	<!-- FileIndexer\n\nfinish -->
   	<a href="../api/tmd_documentation/fileindexer.html"><g id="node10" class="node">
   		<title>FileIndexer\n\nfinish</title>
   		<polygon fill="#bbeeff" stroke="black"
   			points="113.5,-136.8 34.5,-136.8 34.5,-82.8 113.5,-82.8 113.5,-136.8" />
   		<text text-anchor="middle" x="74" y="-121.6" font-family="Times,serif" font-size="14.00">
   			FileIndexer</text>
   		<text text-anchor="middle" x="74" y="-90.6" font-family="Times,serif" font-size="14.00">
   			finish</text>
   	</g></a>
   	<!-- FileIndexer\n\nfinish&#45;&gt;end -->
   	<g id="edge6" class="edge">
   		<title>FileIndexer\n\nfinish&#45;&gt;end</title>
   		<path fill="none" stroke="black" d="M61.56,-82.55C57.27,-73.5 52.44,-63.31 48.03,-54.01" />
   		<polygon fill="black" stroke="black"
   			points="51.08,-52.26 43.63,-44.72 44.75,-55.26 51.08,-52.26" />
   	</g>
   	<!-- Create filename. -->
   	<a href="../api/tmd_documentation/phpsphinx.html"><g id="node11" class="node">
   		<title>Create filename.</title>
   		<polygon fill="#bbeeff" stroke="black"
   			points="151.5,-483.16 44.5,-483.16 44.5,-447.16 151.5,-447.16 151.5,-483.16" />
   		<text text-anchor="middle" x="98" y="-461.46" font-family="Times,serif"
   			font-size="14.00">Create filename.</text>
   	</g></a>
   	<!-- Add to index. -->
   	<a href="../api/tmd_documentation/phpsphinx.html"><g id="node12" class="node">
   		<title>Add to index.</title>
   		<polygon fill="#bbeeff" stroke="black"
   			points="144,-411.16 52,-411.16 52,-375.16 144,-375.16 144,-411.16" />
   		<text text-anchor="middle" x="98" y="-389.46" font-family="Times,serif"
   			font-size="14.00">Add to index.</text>
   	</g></a>
   	<!-- Create filename.&#45;&gt;Add to index. -->
   	<g id="edge17" class="edge">
   		<title>Create filename.&#45;&gt;Add to index.</title>
   		<path fill="none" stroke="black" d="M98,-446.86C98,-439.15 98,-429.88 98,-421.28" />
   		<polygon fill="black" stroke="black"
   			points="101.5,-421.27 98,-411.27 94.5,-421.27 101.5,-421.27" />
   	</g>
   	<!-- Add to index.&#45;&gt;FileIndexer\n\nadd -->
   	<g id="edge19" class="edge">
   		<title>Add to index.&#45;&gt;FileIndexer\n\nadd</title>
   		<path fill="none" stroke="black"
   			d="M94.09,-375.02C92.37,-367.44 90.27,-358.22 88.22,-349.23" />
   		<polygon fill="black" stroke="black"
   			points="91.59,-348.26 85.95,-339.29 84.76,-349.82 91.59,-348.26" />
   	</g>
   	<!-- Save file. -->
   	<a href="../api/tmd_documentation/phpsphinx.html"><g id="node13" class="node">
   		<title>Save file.</title>
   		<polygon fill="#bbeeff" stroke="black"
   			points="206,-330.16 138,-330.16 138,-294.16 206,-294.16 206,-330.16" />
   		<text text-anchor="middle" x="172" y="-308.46" font-family="Times,serif"
   			font-size="14.00">Save file.</text>
   	</g></a>
   	<!-- Add to index.&#45;&gt;Save file. -->
   	<g id="edge18" class="edge">
   		<title>Add to index.&#45;&gt;Save file.</title>
   		<path fill="none" stroke="black"
   			d="M114.06,-375.02C124.24,-364.15 137.58,-349.91 148.89,-337.84" />
   		<polygon fill="black" stroke="black"
   			points="151.69,-339.96 155.97,-330.27 146.58,-335.18 151.69,-339.96" />
   	</g>
   	<!-- Loop. -->
   	<a href="../api/tmd_documentation/phpsphinx.html"><g id="node14" class="node">
   		<title>Loop.</title>
   		<polygon fill="#ffeeaa" stroke="black"
   			points="112,-1162.27 66.92,-1144.27 112,-1126.27 157.08,-1144.27 112,-1162.27" />
   		<text text-anchor="middle" x="112" y="-1140.57" font-family="Times,serif"
   			font-size="14.00">Loop.</text>
   	</g></a>
   	<!-- Save file.&#45;&gt;Loop. -->
   	<g id="edge20" class="edge">
   		<title>Save file.&#45;&gt;Loop.</title>
   		<path fill="none" stroke="black"
   			d="M176.51,-330.23C183.47,-358.07 196,-415.06 196,-464.16 196,-963.08 196,-963.08 196,-963.08 196,-1022.34 184.35,-1038.23 156,-1090.27 149.54,-1102.12 140.22,-1113.83 131.82,-1123.22" />
   		<polygon fill="black" stroke="black"
   			points="129,-1121.1 124.76,-1130.81 134.13,-1125.87 129,-1121.1" />
   	</g>
   	<!-- Loop.&#45;&gt;end -->
   	<g id="edge21" class="edge">
   		<title>Loop.&#45;&gt;end</title>
   		<path fill="none" stroke="black"
   			d="M96.13,-1132.47C82.85,-1122.74 64.15,-1107.37 52,-1090.27 16.63,-1040.48 0,-1024.15 0,-963.08 0,-963.08 0,-963.08 0,-209.98 0,-155.12 14.55,-92.62 24.61,-55.95" />
   		<polygon fill="black" stroke="black"
   			points="28.11,-56.45 27.44,-45.88 21.37,-54.56 28.11,-56.45" />
   	</g>
   	<!-- Loop.&#45;&gt;Tokenizer\n\ntokenize_file -->
   	<g id="edge10" class="edge">
   		<title>Loop.&#45;&gt;Tokenizer\n\ntokenize_file</title>
   		<path fill="none" stroke="black"
   			d="M110.34,-1126.9C109.57,-1119.28 108.62,-1109.89 107.69,-1100.72" />
   		<polygon fill="black" stroke="black"
   			points="111.15,-1100.16 106.66,-1090.56 104.19,-1100.87 111.15,-1100.16" />
   	</g>
   	<!-- Artifact\n\nFilesIndex&#45;&gt;FileIndexer\n\nfinish -->
   	<g id="edge5" class="edge">
   		<title>Artifact\n\nFilesIndex&#45;&gt;FileIndexer\n\nfinish</title>
   		<path fill="none" stroke="black"
   			d="M77.75,-172.77C77.24,-164.42 76.71,-155.57 76.21,-147.25" />
   		<polygon fill="black" stroke="black"
   			points="79.69,-146.85 75.59,-137.08 72.7,-147.27 79.69,-146.85" />
   	</g>
   	<!-- Artifact\n\nFileInfos&#45;&gt;Loop. -->
   	<g id="edge9" class="edge">
   		<title>Artifact\n\nFileInfos&#45;&gt;Loop.</title>
   		<path fill="none" stroke="black"
   			d="M112.59,-1198.25C112.49,-1189.61 112.39,-1180.58 112.3,-1172.46" />
   		<polygon fill="black" stroke="black"
   			points="115.8,-1172.4 112.19,-1162.44 108.8,-1172.48 115.8,-1172.4" />
   	</g>
   	<!-- Artifact\n\nTokenList&#45;&gt;DocblockExtract\n\nget_code_hierarchy -->
   	<g id="edge12" class="edge">
   		<title>Artifact\n\nTokenList&#45;&gt;DocblockExtract\n\nget_code_hierarchy</title>
   		<path fill="none" stroke="black"
   			d="M99.62,-923.87C99.54,-915.52 99.45,-906.67 99.37,-898.35" />
   		<polygon fill="black" stroke="black"
   			points="102.87,-898.15 99.26,-888.18 95.87,-898.22 102.87,-898.15" />
   	</g>
   	<!-- Artifact\n\nCodeHierarchy&#45;&gt;Formatter\n\nformat -->
   	<g id="edge14" class="edge">
   		<title>Artifact\n\nCodeHierarchy&#45;&gt;Formatter\n\nformat</title>
   		<path fill="none" stroke="black" d="M98,-721.5C98,-713.15 98,-704.3 98,-695.99" />
   		<polygon fill="black" stroke="black"
   			points="101.5,-695.81 98,-685.81 94.5,-695.81 101.5,-695.81" />
   	</g>
   	<!-- Artifact\n\nOutputContent&#45;&gt;Create filename. -->
   	<g id="edge16" class="edge">
   		<title>Artifact\n\nOutputContent&#45;&gt;Create filename.</title>
   		<path fill="none" stroke="black" d="M98,-519.15C98,-510.51 98,-501.47 98,-493.36" />
   		<polygon fill="black" stroke="black"
   			points="101.5,-493.34 98,-483.34 94.5,-493.34 101.5,-493.34" />
   	</g>
   </g>
   </svg>
