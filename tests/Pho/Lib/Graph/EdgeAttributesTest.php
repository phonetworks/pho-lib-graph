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

class EdgeAttributesTest extends TestCase 
{
    public function testOrphanEdge() {
        $node1 = new Node($this->graph);
        $node2 = new Node($this->graph);
        $edge = new Edge($node1);
        $this->assertEquals(0, $node1->edges()->all()->count());
        $this->assertTrue($edge->orphan());
        $edge->connect($node2);
        $this->assertEquals(1, $node1->edges()->all()->count());
        $this->assertFalse($edge->orphan());
    }

    public function testOrphanEdgeMultiConnection() {
        $node1 = new Node($this->graph);
        $node2 = new Node($this->graph);
        $node3 = new Node($this->graph);
        $edge = new Edge($node1);
        $edge->connect($node2);
        $this->expectException(Exceptions\EdgeAlreadyConnectedException::class);
        $edge->connect($node3);
    }

    public function testMultiplicableEdge() {
        $unmultiplicable_predicate = new class() extends Predicate {
            protected $multiplicable = false;
        };
        $node1 = new Node($this->graph);
        $node2 = new Node($this->graph);
        $edge = new Edge($node1, $node2, $unmultiplicable_predicate);
        $this->assertEquals(1, $node1->edges()->all()->count());
        $this->assertFalse($edge->orphan());
        $new_edge = new Edge($node2, $node1, $unmultiplicable_predicate);
        $this->assertEquals(2, $node1->edges()->all()->count());
        $this->expectException(Exceptions\DuplicateEdgeException::class);
        $duplicate_edge = new Edge($node1, $node2, $unmultiplicable_predicate);
    }

    public function testMultiplicableWithOrphanEdge() {
        $unmultiplicable_predicate = new class() extends Predicate {
            protected $multiplicable = false;
        };
        $node1 = new Node($this->graph);
        $node2 = new Node($this->graph);
        $edge = new Edge($node1, $node2, $unmultiplicable_predicate);
        $this->assertEquals(1, $node1->edges()->all()->count());
        $this->assertFalse($edge->orphan());
        $duplicate_edge = new Edge($node1, null, $unmultiplicable_predicate);
        $this->assertInstanceOf(Edge::class, $duplicate_edge);
        $this->assertTrue($duplicate_edge->orphan());
        $this->expectException(Exceptions\DuplicateEdgeException::class);
        $duplicate_edge->connect($node2);
    }

}