<?php

    /**
     * @file
     * Simple Regression tester main library.
     *
     * @package simpletestframework
     * @license GPLv2 (see LICENCE.txt).
     * @author Marcus Povey <marcus@marcus-povey.co.uk>
     * @copyright Marcus Povey 2011
     * @link http://www.marcus-povey.co.uk
     */


     /**
      * Test class.
      *
      * You should implement this class in order to execute your test, and place
      * the file containing it in a /tests directory - these will be loaded at runtime
      * so there is no need to have unnecessary classes kicking about during normal
      * operation.
      */
     abstract class Test
     {
	 /**
	  * Keep track of the current test for logging purposes.
	  */
	 private static $__current_test;

	 /**
	  * Initialise the test, create any objects/database entries etc.
	  */
	 abstract public function init();

	 /**
	  * Run the test.
	  *
	  * This function should return:
	  *	    - TESTER_PASS - for success
	  *	    - TESTER_WARNING - for a warning (non fatal error)
	  *	    - TESTER_FAIL - for a fatal error.
	  *
	  * The test may also throw any kind of Exception, and this will
	  * be caught by the framework.
	  */
	 abstract public function run();

	 /**
	  * Tidy up after the test.
	  * Remove any test objects, database entries etc.
	  */
	 abstract public function destroy();

	 /**
	  * Write a log message.
	  * @param string $message The message
	  */
	 public static function log($message) { self::__log($message, 'log'); }

	 /**
	  * Write a warning log message.
	  * @param string $message The message
	  */
	 public static  function warning($message) { self::__log($message, 'warning'); }

	 /**
	  * Write an error log message.
	  * @param string $message The message
	  */
	 public static function error($message) { self::__log($message, 'error'); }



	 /**
	  * Write a log message to the console.
	  * @param string $message Message
	  * @param string $level Log level, usually 'log', 'warning' or 'error'
	  */
	 private static function __log($message, $level = 'log')
	 {
	    $lines = explode("\n", $message);
	    foreach ($lines as $message)
	    {
		$now = microtime(true);
		$level = strtoupper($level);
		$test = '';

		$escon = '';
		$escoff = '';

		switch ($level) {

		    case 'ERROR' :
			$escon = "\033[31m";
			$escoff = "\033[0m";
		    break;

		    case 'WARNING' :
			$escon = "\033[36m";
			$escoff = "\033[0m";
		    break;
		}

		if (self::$__current_test) $test = ' '.self::$__current_test;
		if (($test) && ($test!= ' TEST RESULTS'))
		    $test = "\033[36;1m$test\033[0m";

		echo "$now{$test}: {$escon}$level{$escoff} - \033[37m$message\033[0m\n";
	    }
	 }

	 /**
	  * Execute a test and catch the result.
	  * @param class $test The test class
	  */
	 public static function execute($test)
	 {
	    $r_test = new ReflectionClass($test);

	    if (
		(class_exists($test)) &&
		(!$r_test->isAbstract()) &&
		($r_test->isSubclassOf('Test'))
	    )
	    {
		self::$__current_test = $test;

		$testclass = new $test();
		try {
		    $testclass->init();
		    $result = $testclass->run();
		} catch (Exception $e) {
		    self::error($e->getMessage());

		    $result = TEST_FAIL;
		}

		// Try and tidy up, this may also result in errors, but we dont
		// want an error in the test preventing things from being tidied
		try {
		    $testclass->destroy();
		} catch (Exception $e) {
		    self::error($e->getMessage());

		    $result = TEST_FAIL;
		}

		self::$__current_test = '';
		return $result;
	    }
	 }

	 /**
	  * Run all declared tests.
	  * @return bool True if ok, false if not.
	  */
	 public static function executeAll()
	 {
	    $ok = true;
	    $test_results = array();
	    $start = microtime(true);

	    $classes = get_declared_classes();

	    foreach ($classes as $class)
	    {
		self::$__current_test = '';
		if (
		    (is_subclass_of($class, 'Test')) &&
		    (
			($r_class = new ReflectionClass($class)) &&
			(!$r_class->isAbstract())
		    )
		   )
		{
		    self::log("Executing test '$class'");
		    $test_results[$class] = self::execute($class);
		    if ($test_results[$class]!=TEST_PASS)
			$ok = false;
		}
	    }

	    $end = microtime(true);

	    self::$__current_test = 'TEST RESULTS';
	    self::log("Test run took " . ($end-$start) . "ms to execute.");
	    if ($ok)
		self::log('All tests PASSED!');
	    else
	    {
		self::log('Some tests reported errors or warnings, see below for a report:');
		self::log('--------- REPORT ---------');

		foreach ($test_results as $test => $result)
		{
		    $level = 'log';

		    switch ($result)
		    {
			case TEST_PASS: $result = "\033[32;1mPASS\033[0m"; break;
			case TEST_WARN: $result = "\033[36mWARNING\033[0m"; $level = 'warning'; break;
			case TEST_FAIL:
			default : $result = "\033[31mFAILURE\033[0m"; $level = 'error'; break;
		    }

		    self::log("$result in test '$test'", $level);
		}

		tester_log('------- END REPORT -------');

		return 1; // return error to be caught by any scripts
	    }

	    self::$__current_test = '';

	    return 0; // return ok
	 }

	 /**
	  * Load tests from a given path.
	  * @param string $path Path to load tests from.
	  */
	 public static function loadTests($path)
	 {
	    $path = rtrim($path, ' /') . "/";

	    if ($handle = opendir($path))
	    {
		while ($test = readdir($handle))
		{
		    if (strpos($test, '.php')!==false)
			    include_once($path . $test);
		}
	    }
	 }
     }

     define('TEST_PASS', 2, true);
     define('TEST_WARN', 1, true);
     define('TEST_FAIL', 0, true);