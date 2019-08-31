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

class IDBigMathTest  extends TestCase 
{

    public function testXorDistance()
    {
        $node1 =new Node($this->graph);
        $node2 =new Node($this->graph);
        $id1 =  $node1->id();
        $id2 =  $node2->id();
        $this->assertGreaterThan(0, $id1->distance($id2));
        $this->assertGreaterThan(0, $id1->distance((string )$id2));
        $this->assertSame($id1->distance((string )$id2), $id2->distance($id1));
    }
} 