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

class EventsTest extends TestCase 
{
    public function testNodeAdded() {
        $node_ref = null;
        $num_ref = 0;
        $this->graph->on("node.added", function($node) use (&$node_ref) {
                $node_ref = $node;
        });
        $this->graph->on("modified", function() use (&$num_ref) {
                $num_ref++;
        });
        $this->assertEquals(0, $num_ref);
        $node = new Node($this->graph);
        $this->assertEquals($node->id(), $node_ref->id());
        $this->assertEquals(1, $num_ref);
    }

    public function testNodeQuietSet() {
        $num_ref = 0;
        $node = new Node($this->graph);
        $node->on("modified", function() use (&$num_ref) {
                $num_ref++;
        });
        $this->assertEquals(0, $num_ref);
        $node->attributes()->key1 = "value";
        $this->assertEquals(1, $num_ref);
        $this->assertEquals("value", $node->attributes()->key1);
        $node->attributes()->quietSet("key2", "value");
        $this->assertEquals(1, $num_ref);
        $this->assertEquals("value", $node->attributes()->key2);
        $node->attributes()->key3 = "value";
        $this->assertEquals(2, $num_ref);
        $this->assertEquals("value", $node->attributes()->key3);
    }

    public function testEdgeCreated() { 
        $ref = 0;
        $node1 = new Node($this->graph);
        $node1->on("edge.created", function() use (&$ref) {
                $this->assertTrue(true);
                $ref++;
        });
        $node2 = new Node($this->graph);
        $this->assertEquals(0, $ref);
        $edge = new Edge($node1, $node2);
        //eval(\Psy\sh());
        $this->assertEquals(1, $ref);
    } // Edge.php

    public function testEdgeCreatedWithObject() { 
        $ref = "";
        $node1 = new Node($this->graph);
        $node1->on("edge.created", function($edge) use (&$ref) {
                $ref = (string) $edge->id();
        });
        $node2 = new Node($this->graph);
        $edge = new Edge($node1, $node2);
        $this->assertEquals($ref, (string) $edge->id());
    } // Edge.php

    public function testEdgeConnectedWithObject() { 
        $ref1 = "";
        $ref2 = "";
        $node1 = new Node($this->graph);
        $node2 = new Node($this->graph);
        $node2->on("edge.connected", function($edge) use (&$ref2) {
                $ref2 = (string) $edge->id();
        });
        $edge = new Edge($node1, $node2);
        $this->assertEquals($ref2, (string) $edge->id());
    } // Edge.php

    public function testEdgeConnected() { 
        $ref1 = 0;
        $ref2 = 0;
        $node1 = new Node($this->graph);
        $node2 = new Node($this->graph);
        $node1->on("edge.created", function() use (&$ref1) {
                $this->assertTrue(true);
                $ref1++;
        });
        $node2->on("edge.connected", function() use (&$ref2) {
                $this->assertTrue(true);
                $ref2++;
        });
        
        $edge = new Edge($node1);
        $this->assertEquals(1, $ref1);
        $this->assertEquals(0, $ref2);
        $this->assertTrue($edge->orphan());
        $edge->connect($node2);
        $this->assertEquals(1, $ref1);
        $this->assertEquals(1, $ref2);
    } // Edge.php

    public function testNodeModified() { 
        $ref = 0;
        $node = new Node($this->graph);
        $node->on("modified", function() use (&$ref) {
                $this->assertTrue(true);
                $ref++;
        });
        $node->attributes()->color = "red";
        $this->assertEquals(1, $ref);
    } // EntityTrait.php

    public function testEdgeModified() { 
        $ref_node = $ref_edge = 0;
        $node = new Node($this->graph);
        $edge = new Edge($node);
        $node->on("modified", function() use (&$ref_node) {
            $this->assertTrue(true);
            $ref_node++;
        });
        $edge->on("modified", function() use (&$ref_edge, $edge) {
                $this->assertTrue(true);
                $ref_edge++;
        });
        $edge->attributes()->color = "blue";
        $this->assertEquals(0, $ref_node);
        $this->assertEquals(1, $ref_edge);
    } // EntityTrait.php
    
    public function testNodeDeleting() { 
        $ref = $ref2 = 0;
        $node = new Node($this->graph);
        $node->on("deleting", function() use (&$ref) {
                $this->assertTrue(true);
                $ref++;
        });
        $this->graph->on("modified", function() use (&$ref2) {
            $ref2++;
        });
        $node->destroy();
        $this->assertEquals(1, $ref);
        $this->assertEquals(1, $ref2);
    } // Node
}