#!/usr/bin/php
<?php

    /**
     * @file
     * Simple Regression tester tool.
     *
     * Please read the README.txt!
     *
     * @see Test
     *
     * @package simpletestframework
     * @license GPLv2 (see LICENCE.txt).
     * @author Marcus Povey <marcus@marcus-povey.co.uk>
     * @copyright Marcus Povey 2011
     * @link http://www.marcus-povey.co.uk
     */

    require_once(dirname(__FILE__). '/include/libtest.php');

    $path = dirname(__FILE__) . '/tests/';
    $tests = array();

    $n = 0;
    do {
	switch ($argv[$n])
	{
	    case '-p': $n++; $path = $argv[$n]; break;
	    case '-t': $n++; $tests[] = $argv[$n]; break;

	    default: $n++;
	}
    } while ($n < $argc);

    Test::log("Simple Regression Test Tool v1.0 by Marcus Povey <marcus@marcus-povey.co.uk>");


    // Load tests
    Test::log("Loading tests from '$path' (to modify pass -p path)");
    Test::loadTests($path);

    // Execute
    if (count($tests) == 0) {
	Test::log("Executing all loaded tests (pass -t to execute specific tests)");
	exit(Test::executeAll());
    }
    else
    {
	$return = 0;
	foreach ($tests as $test)
	    if (Test::execute($test)!=TEST_PASS) $return = 1;

	return $return;
    }
