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
        $node = new Graph\Node($this->graph);
        $node_expected_to_be_identical = $this->graph->get($node->id());
        $this->assertEquals($node->id(), $node_expected_to_be_identical->id());
    }

    public function testGraphContains() {
        $node =new Graph\Node($this->graph);
        $this->assertTrue($this->graph->contains($node->id()));
    }

    public function testSubgraph() {
        $subgraph = new Graph\SubGraph($this->graph);
        $this->assertTrue($this->graph->contains($subgraph->id()));
    }

    public function testSubgraphRecursiveness() {
        $subgraph = new Graph\SubGraph($this->graph);
        $node = new Graph\Node($subgraph);
        $this->assertTrue($subgraph->contains($node->id()));
        $this->assertTrue($this->graph->contains($node->id()));
        $this->assertTrue($this->graph->contains($subgraph->id()));
    }

    public function testEdge() {
        $node1 = new Graph\Node($this->graph);
        $node2 = new Graph\Node($this->graph);
        $edge = new Graph\Edge($node1, $node2);
        $this->assertEquals($edge->id(), $node1->edges()->out()[0]->id());
        $this->assertEquals($edge->id(), $node2->edges()->in()[0]->id());
        $this->assertEquals($edge->id(), $node2->edges()->all()[0]->id());
    }

    public function testAttributes() {
        $faker = \Faker\Factory::create();
        $node1 = new Graph\Node($this->graph);
        $node2 = new Graph\Node($this->graph);
        $edge = new Graph\Edge($node1, $node2);
        $node1->attributes()->username = ($username1 = $faker->username);
        $node2->attributes()->username = ($username2 = $faker->username);
        $edge->attributes()->address = ($address = $faker->address);
        $this->assertEquals($username1, $node1->attributes()->username);
        $this->assertEquals($username2, $node2->attributes()->username);
        $this->assertEquals($address, $edge->attributes()->address);
    }

    public function testPredicateAssignment() {
        $new_predicate = new class extends Graph\Predicate { public function test() { return "works"; }};
        $node1 = new Graph\Node($this->graph);
        $node2 = new Graph\Node($this->graph);
        $edge1 = new Graph\Edge($node1, $node2);
        $this->assertFalse(method_exists($edge1->predicate(), "test"));
        $edge2 = new Graph\Edge($node1, $node2, $new_predicate);
        $this->assertEquals("works", $edge2->predicate()->test());
    }
}