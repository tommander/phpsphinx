<?php
/**
 * Main entrypoint to PhpSphinx application.
 *
 * @package TMD
 * @subpackage Documentation
 */

declare(strict_types=1);

// Hi.
require_once __DIR__ . '/vendor/autoload.php';

$cls = new TMD\Documentation\PhpSphinx();
$cls->do_run();
