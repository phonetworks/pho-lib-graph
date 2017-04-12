<?php

/*
 * This file is part of the Pho package.
 *
 * (c) Emre Sokullu <emre@phonetworks.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Pho\Lib\Graph;

class SimpleTest extends \PHPUnit\Framework\TestCase 
{
    private $graph;

    public function setUp() {
        $this->graph = new Graph\Graph();
    }

    public function tearDown() {
        unset($this->graph);
    }

    public function testGraphAddGet() {
        $node = $this->graph->add(new Graph\Node());
        $node_expected_to_be_identical = $this->graph->get($node->id());
        $this->assertEquals($node->id(), $node_expected_to_be_identical->id());
    }

    public function testGraphContains() {
        $node = $this->graph->add(new Graph\Node());
        $this->assertTrue($this->graph->contains($node->id()));
        /*$node2 = $graph->add(new Graph\Node());
        $subgraph = $graph->add(new Graph\SubGraph());
        $node3 = $subgraph->add(new Graph\Node());
        $edge1 = new Graph\Edge($node1, new Graph\Predicate(), $node2);*/
    }

    public function testSubgraph() {
        $subgraph = $this->graph->add(new Graph\SubGraph());
        $this->assertTrue($this->graph->contains($subgraph->id()));
    }

    public function testSubgraphContains() {
        $subgraph = $this->graph->add(new Graph\SubGraph());
        $node = $subgraph->add(new Graph\Node());
        $this->assertTrue($subgraph->contains($node->id()));
        $this->assertFalse($this->graph->contains($node->id()));
        $this->assertTrue($this->graph->contains($subgraph->id()));
    }
}