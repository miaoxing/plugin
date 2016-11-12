<?php

namespace Miaoxing\Plugin\Test;

use PHPUnit_Framework_AssertionFailedError;
use PHPUnit_Framework_Test;
use PHPUnit_Framework_TestCase;
use PHPUnit_Framework_TestSuite;
use PHPUnit_Framework_Warning;
use PHPUnit_Framework_WarningTestCase;
use PHPUnit_TextUI_ResultPrinter;
use ReflectionClass;
use ReflectionMethod;

class ResultPrinter extends PHPUnit_TextUI_ResultPrinter
{
    /**
     * @var array
     */
    protected static $messages = [];

    /**
     * {@inheritdoc}
     */
    public function startTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        parent::startTestSuite($suite);

        $name = $suite->getName();
        if (!class_exists($name)) {
            return;
        }

        $class = new ReflectionClass($name);
        $doc = $class->getDocComment();
        $title = $this->parseTitle($doc);
        if ($title) {
            $this->write($title . "\n");
        } else {
            $this->writeWithColor('fg-yellow', $name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function endTestSuite(PHPUnit_Framework_TestSuite $suite)
    {
        parent::endTestSuite($suite);
        $this->write("\n");
    }

    /**
     * {@inheritdoc}
     */
    public function startTest(PHPUnit_Framework_Test $test)
    {
        parent::startTest($test);

        if ($test instanceof PHPUnit_Framework_Warning) {
            return;
        }

        if ($test instanceof PHPUnit_Framework_WarningTestCase) {
            return;
        }

        if (!$test instanceof PHPUnit_Framework_TestCase) {
            return;
        }

        $name = $test->getName(false);
        $method = new ReflectionMethod($test, $name);
        $doc = $method->getDocComment();
        $title = $this->parseTitle($doc);
        if ($title) {
            $this->write('  ' . $title . "\n");
        } else {
            $this->writeWithColor('fg-yellow', '  ' . $name);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addFailure(PHPUnit_Framework_Test $test, PHPUnit_Framework_AssertionFailedError $e, $time)
    {
        // Get last message which is failure
        $lastMessage = array_pop(static::$messages);

        // Print previous pass messages
        $this->printPassMessages();

        $this->writeWithColor('fg-red', '    ✗ ' . $lastMessage . ' ' . $e->getMessage());

        $this->lastTestFailed = true;
    }

    /**
     * {@inheritdoc}
     */
    public function endTest(PHPUnit_Framework_Test $test, $time)
    {
        if (!$this->lastTestFailed) {
            $this->printPassMessages();
        }
        parent::endTest($test, $time);
    }

    /**
     * {@inheritdoc}
     */
    protected function writeProgress($progress)
    {
        // Hack: ignore passed progress
        if ($progress == '.') {
            return;
        }

        parent::writeProgress($progress);
    }

    /**
     * @param string $doc
     * @return null|string
     */
    protected function parseTitle($doc)
    {
        if (!$doc) {
            return null;
        }

        preg_match('/\* (.+?)\n/is', $doc, $match);
        if (isset($match[1])) {
            return trim($match[1]);
        } else {
            return null;
        }
    }

    protected function printPassMessages()
    {
        foreach (static::$messages as $message) {
            $mark = $this->formatWithColor('fg-green', '    ✓ ');
            $this->write($mark);
            $this->writeWithColor('fg-white', $message);
        }
        static::$messages = [];
    }

    /**
     * @param string $message
     */
    public static function addMessage($message)
    {
        static::$messages[] = $message;
    }
}
