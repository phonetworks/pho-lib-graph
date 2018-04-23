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

class ObserverPatternTest extends TestCase 
{

    /**
     * In this test we create a new anonymous class that extends Node.
     * Form an object, modify its attributes, and check that if a change
     * in the AttributeBag triggers the observeAttributeBagUpdate function
     * in this new class.
     */
    public function testNodeAttributeSet() {
        $node = new class($this->graph) extends Node {
            public $node_updated = false;
            public function __construct($graph)
                {
                    parent::__construct($graph);
                    $this->on("modified", function() {
                        $this->node_updated = true;
                    });
                }
        };
        $node->attributes()->attribute = "value";
        $this->assertTrue($node->node_updated);
    }

    public function testDestroy() {
        $subgraph = new SubGraph($this->graph);
        $node1 = new Node($this->graph);
        $node2 = new Node($subgraph);
        $this->assertCount(3, $this->graph->members());
        $this->assertCount(1, $subgraph->members());
        $node1->destroy();
        $this->assertCount(2, $this->graph->members());
        $this->assertCount(1, $subgraph->members());
    }

}