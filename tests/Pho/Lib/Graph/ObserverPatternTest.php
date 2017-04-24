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

class ObserverPatternTest extends \PHPUnit\Framework\TestCase 
{
    private $graph;

    public function setUp() {
        $this->graph = new Graph();
    }

    public function tearDown() {
        unset($this->graph);
    }

    public function testNodeAttributeSet() {
        $node = new class($this->graph) extends Node {
            public $node_updated = false;
            protected function observeAttributeBagUpdate(\SplSubject $subject): void
                {
                    $this->node_updated = true;
                }
        };
        $node->attributes()->attribute = "value";
        $this->assertTrue($node->node_updated);
    }
}