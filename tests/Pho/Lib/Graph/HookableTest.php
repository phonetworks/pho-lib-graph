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

use DeepCopy\DeepCopy;

/**
 * Please note,
 * 
 * a very complex hook test is also available in: FeedEdgeListTest
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class HookableTest extends TestCase 
{
    private $node1, $node2, $edge;

    public function setUp() {
        parent::setUp();
        $copier = new DeepCopy();
        $this->node1 = new Node($this->graph);
        $this->node2 = new Node($this->graph);
        $this->edge = new Edge($this->node1, $this->node2);
    }

    public function tearDown() {
        parent::tearDown();
        unset($this->node1);
        unset($this->node2);
        unset($this->edge);
    }

    public function testGraphHookables() {
        $ref = 0;
        $this->assertCount(2, $this->graph->members());
        $nodes = $this->killProperty($this->graph, "nodes");
        $this->assertCount(2, $nodes);
        $this->graph->hook("get", function(ID $id) use ($nodes, &$ref): NodeInterface {
            $ref++;
            return $nodes[$id->toString()];
        });
        $this->assertEquals(0, $ref);
        $this->assertEquals($this->node1->id(), $this->graph->get($this->node1->id())->id());
        $this->assertEquals(1, $ref);
    }

    public function testEdgeHookables() {
        $ref = 0;
        $tail = $this->killProperty($this->edge, "tail");
        $this->edge->hook("tail", function() use ($tail, &$ref) {
            $ref++;
            return $tail;
        });
        $this->assertEquals(0, $ref);
        $this->assertEquals($tail->id(), $this->edge->tail()->id());
        $this->assertEquals(1, $ref);
    }

    private function killProperty(&$obj, $prop) {
        $ref = new \ReflectionObject($obj);
        $p = $ref->getProperty($prop);
        $p->setAccessible(true);
        $val = $p->getValue($obj);
        $p->setValue($obj, null);
        return $val;
    }

}