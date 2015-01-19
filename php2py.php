#!/usr/bin/env php
<?php

/*
 * (c) Alexandre Quercia <alquerci@email.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/vendor/autoload.php';

use Instinct\Php2py\Console\Application;

$application = new Application();
$application->run();
