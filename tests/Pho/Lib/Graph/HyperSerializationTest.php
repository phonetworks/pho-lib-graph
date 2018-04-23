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

class HyperSerializationTest extends TestCase 
{

    public function testObserverSerialization() {
        $subgraph = new SubGraph($this->graph);
        $node =new Node($subgraph);
        $subgraph_serialized = $subgraph->toArray();
        $node_serialized = $node->toArray();
        $this->assertArrayHasKey("observers", $subgraph_serialized);
        $this->assertArrayHasKey("observers", $node_serialized);
        $this->assertCount(1, $subgraph_serialized["observers"]);
        $this->assertEquals($this->graph->id()->toString(), $subgraph_serialized["observers"][0]);
        $this->assertContains($subgraph->id()->toString(), $node_serialized["observers"]);
    }

    public function testGraphRemove()
    {
        eval(\Psy\sh());
        $this->assertTrue(true);
    }
}