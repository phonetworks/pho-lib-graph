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

class OrphanEdgeTest extends TestCase 
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

}