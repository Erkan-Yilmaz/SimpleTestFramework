Simple Test Framework - An example PHP Regression tester
by Marcus Povey <marcus@marcus-povey.co.uk>
================================================================================

Introduction:
-------------

This is a simple regression testing tool which can be used to execute tests
against programs written in PHP.

It is primarily an example of how you might write a regression tester, but
hopefully it will be of use to someone out there.

Inspired by this blog post:
http://www.marcus-povey.co.uk/2011/12/05/regression-testing-if-you-dont-do-it-youre-a-fscking-idiot/

Adapted from the tester module in the BCT Platform which is (C) Marcus Povey 2010-11

Requirements:
-------------

This is a PHP command line application so you will require php5-cli and its
various dependancies.

On Debian based systems this is a simple matter of:

    sudo apt-get install php5-cli

Usage:
------

php test.php [-p path/to/tests] [-t HelloWorldTest]

Example:
--------

To run all the tests in the sub directory /tests/

    php test.php

To specify another directory to look in for tests

    php test.php -p /path/to/my/tests

To run a specific test only, specify it by its CLASS NAME

    php test.php -t MyClassTest

You can specify multiple tests to run...

    php test.php -t MyClassTest -t AnotherTest

Writing your own tests:
-----------------------

Take a look at ExampleTest.test.php for an example test, but in a nutshell your
test should extend and implement the Test class, e.g.

    class FooTest extends Test
    {
	public function init() {

	    // Place your test initialisation code here
	}

	public function run() {

	    if (!sometest1()) // Execute a test
		return TEST_FAIL; // One way of returning an error

	    if (!sometest2()) // Execute a test
		throw Exception("All uncaught exceptions are caught by the tester, and their message echoed!"); // Another way to return an error.

	    if (!sometest3()) // Execute a test
		return TEST_WARN; // A non fatal error, but none the less we want to let the user know about it.

	    return TEST_PASS; // Important! The engine will assume a failure unless you say it all went ok!
	}

	public function destroy() {
	    // Place your test cleanup code here.. close/delete files, destroy objects, shut sockets etc
	}
    }

Tip: Code using good OO techniques. Group common test functions into a class hierachy!

Log Entries
-----------

The Test class provides you with a number of methods to help you keep a log, these are

Test::log('message'); // Send a notice level message
Test::warning('message'); // Send a warning message
Test::error('message'); // Send an error message


Happy hacking!
