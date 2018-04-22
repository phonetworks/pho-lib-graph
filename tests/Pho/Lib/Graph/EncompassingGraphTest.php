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

class EncompassingGraph extends TestCase 
{


    public function testGraphContains() {
        $subgraph1 = new SubGraph($this->graph);
        $subgraph2 = new SubGraph($this->graph);
        $node =new Node($subgraph1);
        $this->assertCount(1, $subgraph1->members());
        $this->assertCount(0, $subgraph2->members());
        $this->assertCount(3, $this->graph->members());
        $subgraph2->add($subgraph1);
        $this->assertCount(1, $subgraph1->members());
        $this->assertCount(2, $subgraph2->members());
        $this->assertCount(3, $this->graph->members());
        $subgraph3 = new SubGraph($this->graph);
        $subgraph3->add($subgraph2);
        $this->assertCount(1, $subgraph1->members());
        $this->assertCount(2, $subgraph2->members());
        $this->assertCount(3, $subgraph3->members());
        $this->assertCount(4, $this->graph->members());
        $subgraph4 = new SubGraph($subgraph1);
        $node2 =new Node($subgraph1);
        $this->assertCount(0, $subgraph4->members());
        $this->assertCount(3, $subgraph1->members());
        $this->assertCount(4, $subgraph2->members());
        $this->assertCount(5, $subgraph3->members());
        $this->assertCount(6, $this->graph->members());
        return [
            $node,
            $node2,
            $subgraph1,
            $subgraph2,
            $subgraph3,
            $subgraph4,
            $this->graph
        ];
    }

    /**
     * @depends testGraphContains
     */
    public function testGraphRemove(array $graph_elements)
    {
        list(
            $node, 
            $node2, 
            $subgraph1, 
            $subgraph2, 
            $subgraph3, 
            $subgraph4,
            $supgraph
        ) = $graph_elements;
        $subgraph1->remove($node2->id());
        $this->assertCount(0, $subgraph4->members());
        $this->assertCount(2, $subgraph1->members());
        //eval(\Psy\sh());
        $this->assertCount(3, $subgraph2->members());
        $this->assertCount(4, $subgraph3->members());
        $this->assertCount(5, $supgraph->members());
    }
}