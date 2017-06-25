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

class EdgeRolesTest extends \PHPUnit\Framework\TestCase 
{
    private $graph;

    public function setUp() {
        $this->graph = new Graph();
    }

    public function tearDown() {
        unset($this->graph);
    }

    public function testDefaultEdgeRole() {
        $node1 = new Node($this->graph);
        $node2 = new Node($this->graph);
        $edge = new Edge($node1, $node2);
        $this->assertEquals(Predicate::R_DEFAULT, $edge->predicate()->role());
        $this->assertInstanceOf(Edge::class, $edge->return());
    }

    public function testReflectiveEdgeRole() {
        $new_predicate = \Mockery::mock(new Predicate())->allows(
            [
                "role" => Predicate::R_REFLECTIVE
            ]
        );
        $node1 = new Node($this->graph);
        $node2 = new Node($this->graph);
        $edge = new Edge($node1, $node2, $new_predicate);
        $this->assertEquals(Predicate::R_REFLECTIVE, $edge->predicate()->role());
        $this->assertInstanceOf(Node::class, $edge->return());
        $this->assertEquals($node1->id(), $edge->return()->id());

    }

    public function testConsumerEdgeRole() {
        $new_predicate = \Mockery::mock(new Predicate())->allows(
            [
                "role" => Predicate::R_CONSUMER
            ]
        );
        $node1 = new Node($this->graph);
        $node2 = new Node($this->graph);
        $edge = new Edge($node1, $node2, $new_predicate);
        $this->assertEquals(Predicate::R_CONSUMER, $edge->predicate()->role());
        $this->assertInstanceOf(Node::class, $edge->return());
        $this->assertEquals($node2->id(), $edge->return()->id());
    }

    

}