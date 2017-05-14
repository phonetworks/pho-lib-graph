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

class FeedEdgeListTest extends \PHPUnit\Framework\TestCase 
{
    private $graph, $node1, $node2, $edge;
    private $edge_store;

    public function setUp() {
        $this->graph = new Graph();
        $this->node1 = new Node($this->graph);
        $this->node2 = new Node($this->graph);
        $this->edge = new Edge($this->node1, $this->node2);
        $this->edge_store = array( 
            ((string) $this->edge->id()) => (serialize($this->edge))
        );
    }

    public function tearDown() {
        unset($this->graph);
        unset($this->node1);
        unset($this->node2);
        unset($this->edge);
    }

    public function testEdgeToArray() {
        
        $this->edge->attributes()->name = "emre";
        $array = $this->edge->toArray();
        
        $this->assertArrayHasKey("id", $array);
        $this->assertEquals((string)$this->edge->id(), $array["id"]);
        
        $this->assertArrayHasKey("attributes", $array);
        $this->assertEquals($this->edge->attributes()->name, $array["attributes"]["name"]);
        $this->assertEquals("emre", $array["attributes"]["name"]);

        $this->assertArrayHasKey("tail", $array);
        $this->assertEquals((string)$this->edge->tail()->id(), $array["tail"]);
        $this->assertEquals((string)$this->node1->id(), $array["tail"]);

        $this->assertArrayHasKey("head", $array);
        $this->assertEquals((string)$this->edge->head()->id(), $array["head"]);
        $this->assertEquals((string)$this->node2->id(), $array["head"]);
        
        $this->assertArrayHasKey("predicate", $array);
        $this->assertEquals((string)$this->edge->predicate(), $array["predicate"]);
        
    }

    public function testFeedEdgeList() {
        $edge_list = $this->node1->edges()->toArray();
        // recreating the objects from serialized format
        foreach($edge_list as $direction => $edges) {
            foreach($edges as $key => $edge) {
                if(isset($this->edge_store[$edge])) {
                    $edge_list[$direction][$key] = unserialize($this->edge_store[$edge]);
                }
            }
        }
        $new_edge_list = new EdgeList($edge_list);
        $this->assertEquals($this->node1->edges()->toArray(), $new_edge_list->toArray());
    }
}