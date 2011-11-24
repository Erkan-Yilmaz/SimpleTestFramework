<?php

    class ExampleTest extends Test {

	public function init() {
	    $this->log("Initialising example");
	}

	public function run() {
	    $this->log("This is an example test");

	    return TEST_PASS;
	}

	public function destroy() {
	    $this->log("Cleaning up example");
	}
    }