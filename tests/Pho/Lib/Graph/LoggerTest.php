<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Pho\Lib\Graph;

class LoggerTest extends \PHPUnit\Framework\TestCase 
{
    private $graph;

    public function setUp() {
        $this->graph = new Graph();
        Logger::setVerbosity(2);
    }

    public function tearDown() {
        Logger::setVerbosity(0);
        unset($this->graph);
    }

    public function testLog() {
        ob_start();
        $node = new Node($this->graph);
        $output = ob_get_clean();
        $expected = "A node with id \"";
        $this->assertEquals($expected, substr($output,0,strlen($expected)));
    }
}