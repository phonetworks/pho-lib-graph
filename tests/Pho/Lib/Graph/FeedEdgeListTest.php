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

    // this proves, toArray and fromArray work fine. 
    public function testFeedEdgeList() {
        //$edge_list = $this->node1->edges()->toArray();
        //$new_edge_list = $this->recreateEdgeListForNode1($edge_list, $this->edge);
        $node1 = $this->recreateNode($this->node1);
        $this->assertEquals($this->node1->edges()->toArray(), $node1->edges()->toArray());
    }

    public function testInOutAll() {
        $outs = $this->node1->edges()->out();
        $ins = $this->node2->edges()->in();
        $this->assertEquals($ins->current()->id(), $outs->current()->id());
        $alls = $this->node2->edges()->all();
        $this->assertEquals($ins->current()->id(), $alls->current()->id());
    }    

    public function testInOutAllFromSerialized() {
        $node1 = $this->recreateNode($this->node1);
        $outs = $node1->edges()->out();
        $ins = $this->node2->edges()->in();
        $this->assertEquals($ins->current()->id(), $outs->current()->id());
        $alls = $node1->edges()->all();
        $this->assertEquals($ins->current()->id(), $alls->current()->id());
    }

    public function testToFromBetween() {
        $to = $this->node1->edges()->to($this->node2->id());
        $from = $this->node2->edges()->from($this->node1->id());
        $this->assertEquals($to->current()->id(), $from->current()->id());
        $between = $this->node2->edges()->between($this->node1->id());
        $this->assertEquals($to->current()->id(), $between->current()->id());
    }

    public function testToFromBetweenSerialized() {
        $node1 = $this->recreateNode($this->node1);
        $to = $node1->edges()->to($this->node2->id());
        $from = $this->node2->edges()->from($node1->id());
        $this->assertEquals($to->current()->id(), $from->current()->id());
        $between = $node1->edges()->between($this->node2->id());
        $this->assertEquals($from->current()->id(), $between->current()->id());
    }

    public function testSerializeThenAdd() {
        $get_id = function($value) {return (string) $value->id();};
        $node1 = $this->recreateNode($this->node1);
        $this->assertCount(0, $this->node2->edges()->to($node1->id()));
        $this->assertCount(1, $this->node2->edges()->from($node1->id()));
        $this->assertCount(0, $node1->edges()->from($this->node2->id()));
        $this->assertCount(1, $node1->edges()->to($this->node2->id()));
        $node1->edges()->addIncoming((new Edge($this->node2, $node1)));
        $node1_outs = $node1->edges()->out();
        $node1_ins = $node1->edges()->in();
        $node2_ins = $this->node2->edges()->in();
        $node2_outs = $this->node2->edges()->out();
        $this->assertEquals($node2_ins->current()->id(), $node1_outs->current()->id());
        $this->assertEquals($node2_outs->current()->id(), $node1_ins->current()->id());
        $this->assertEquals($this->node2->edges()->from($node1->id())->current()->id(), $node1_outs->current()->id());
        $this->assertEquals($this->node2->edges()->to($node1->id())->current()->id(), $node1_ins->current()->id());
        $this->assertCount(1, $this->node2->edges()->from($node1->id()));
        $this->assertCount(1, $this->node2->edges()->to($node1->id()));
        $this->assertCount(1, $this->node2->edges()->to($node1->id()));
        $this->assertCount(1, $this->node2->edges()->from($node1->id()));
        $node1_alls = $node1->edges()->all();
        $node1_all_ids = array_map($get_id, $node1_alls->getArrayCopy());
        $node2_alls = $this->node2->edges()->all();
        $this->assertContains((string) $node2_ins->current()->id(), $node1_all_ids);
        $this->assertContains((string) $node2_outs->current()->id(), $node1_all_ids);
        $this->assertContains((string) $node2_alls->current()->id(), $node1_all_ids);
        $node2_alls->next();
        $this->assertContains((string) $node2_alls->current()->id(), $node1_all_ids);
    }

    public function testNewPredicateType() {
        $predicate_name = "NewPredicate"; 
        $class_name = "Pho\\Lib\\Graph\\{$predicate_name}";
        $new_predicate = \Mockery::mock(new Predicate())->allows(
            [
                "binding" => true,
                "label" => $predicate_name,
                "__toString" => $class_name,
            ]
        );
        $second_edge = new Edge($this->node2, $this->node1, $new_predicate);
        $this->assertEquals($predicate_name, $second_edge->predicate()->label());
        $this->assertTrue($second_edge->predicate()->binding());
        $this->assertEquals($class_name, (string) $second_edge->predicate());
    }

    public function testInOutAllWithEdgeType() {
        $this->assertCount(1, $this->node1->edges()->out(Edge::class));
        $this->assertCount(1, $this->node2->edges()->in(Edge::class));
        $this->assertCount(1, $this->node1->edges()->all(Edge::class));
    }

    public function testWithNewEdgeType() {
        $new_predicate = new class extends Predicate {};
        $second_edge = new class($this->node1, $this->node2, $new_predicate) extends Edge {};
        $new_edge_class = get_class($second_edge);
        $this->assertCount(0, $this->node2->edges()->out());
        $this->assertCount(0, $this->node1->edges()->in());
        $this->assertCount(2, $this->node1->edges()->out(Edge::class));
        $this->assertCount(2, $this->node2->edges()->in(Edge::class));
        $this->assertCount(2, $this->node1->edges()->all(Edge::class));
        $this->assertCount(1, $this->node1->edges()->out($new_edge_class));
        $this->assertCount(1, $this->node2->edges()->in($new_edge_class));
        $this->assertCount(1, $this->node1->edges()->all($new_edge_class));
        $this->assertCount(2, $this->node1->edges()->out());
        $this->assertCount(2, $this->node2->edges()->in());
        $this->assertCount(2, $this->node1->edges()->all());
        $this->assertCount(0, $this->node1->edges()->from($this->node2->id()));
        $this->assertCount(0, $this->node2->edges()->to($this->node1->id()));
        $this->assertCount(2, $this->node1->edges()->to($this->node2->id()));
        $this->assertCount(2, $this->node2->edges()->from($this->node1->id()));
        $this->assertCount(2, $this->node2->edges()->between($this->node1->id()));
        $this->assertCount(1, $this->node1->edges()->to($this->node2->id(),$new_edge_class));
        $this->assertCount(1, $this->node2->edges()->from($this->node1->id(),$new_edge_class));
        $this->assertCount(1, $this->node2->edges()->between($this->node1->id(),$new_edge_class));
        $this->assertCount(2, $this->node1->edges()->to($this->node2->id(),Edge::class));
        $this->assertCount(2, $this->node2->edges()->from($this->node1->id(),Edge::class));
        $this->assertCount(2, $this->node2->edges()->between($this->node1->id(),Edge::class));
    }

    public function testSerializeThenAddWithNewEdgeType() {
        $get_id = function($value) {return $value->id();};
        $node1 = $this->recreateNode($this->node1);
        $new_predicate = new class extends Predicate {};
        $second_edge = new class($node1, $this->node2, $new_predicate) extends Edge {};
        $new_edge_class = get_class($second_edge);
        $new_edge_list = $node1->edges();
        $this->assertCount(0, $this->node2->edges()->out());
        $this->assertCount(0, $new_edge_list->in());
        $this->assertCount(2, $new_edge_list->out(Edge::class));
        $this->assertCount(2, $this->node2->edges()->in(Edge::class));
        $this->assertCount(2, $new_edge_list->all(Edge::class));
        $this->assertCount(1, $new_edge_list->out($new_edge_class));
        $this->assertCount(1, $this->node2->edges()->in($new_edge_class));
        $this->assertCount(1, $new_edge_list->all($new_edge_class));
        $this->assertCount(2, $new_edge_list->out());
        $this->assertCount(2, $this->node2->edges()->in());
        $this->assertCount(2, $new_edge_list->all());
        $this->assertCount(0, $new_edge_list->from($this->node2->id()));
        $this->assertCount(0, $this->node2->edges()->to($this->node1->id()));
        $this->assertCount(2, $new_edge_list->to($this->node2->id()));
        $this->assertCount(2, $this->node2->edges()->from($node1->id()));
        $this->assertCount(2, $this->node2->edges()->between($node1->id()));
        $this->assertCount(1, $new_edge_list->to($this->node2->id(),$new_edge_class));
        $this->assertCount(1, $this->node2->edges()->from($node1->id(),$new_edge_class));
        $this->assertCount(1, $this->node2->edges()->between($node1->id(),$new_edge_class));
        $this->assertCount(2, $new_edge_list->to($this->node2->id(),Edge::class));
        $this->assertCount(2, $this->node2->edges()->from($node1->id(),Edge::class));
        $this->assertCount(2, $this->node2->edges()->between($node1->id(),Edge::class));
    }

    public function testSerializeThenAddThenSerializeThenAddAgain() {
        $get_id = function($value) {return (string) $value->id();};
        $edge_list = $this->node1->edges()->toArray();
        $node1 = $this->recreateNode($this->node1, $edge_list);
        $second_edge = new Edge($this->node2, $node1);
        $newer_edge_list = $node1->edges();
        $node1_2 = $this->recreateNode($this->node1, $newer_edge_list->toArray());
        $third_edge = new Edge($this->node2, $node1_2);
        $newest_edge_list = $node1_2->edges();
        $node1_outs = $newest_edge_list->out();
        $node1_ins = $newest_edge_list->in();
        $node2_ins = $this->node2->edges()->in();
        $node2_outs = $this->node2->edges()->out();
        //eval(\Psy\sh());
        $this->assertEquals($node2_ins->current()->id(), $node1_outs->current()->id());
        $node1_in_ids = array_map($get_id, $node1_ins->getArrayCopy());
        $this->assertContains((string)$node2_outs->current()->id(), $node1_in_ids);
        $node2_outs->next();
        $this->assertContains((string)$node2_outs->current()->id(), $node1_in_ids);
        $node2_outs->rewind();
        $node1_alls = $newest_edge_list->all();
        $node1_all_ids = array_map($get_id, $node1_alls->getArrayCopy());
        $node2_alls = $this->node2->edges()->all();
        //eval(\Psy\sh());
        $this->assertContains((string) $node2_ins->current()->id(), $node1_all_ids);
        $this->assertContains((string) $node2_outs->current()->id(), $node1_all_ids);
        $this->assertContains((string) $node2_alls->current()->id(), $node1_all_ids);
        $node2_alls->next();
        $this->assertContains((string)$node2_alls->current()->id(), $node1_all_ids);
        $node2_alls->rewind();
        //eval(\Psy\sh());
        $this->assertContains((string)$node2_ins->current()->id(), array_map($get_id, $newest_edge_list->between($this->node2->id())->getArrayCopy()));
        $this->assertContains((string)$this->node2->edges()->to($this->node1->id())->current()->id(), $node1_all_ids);
        $this->assertCount(2, $this->node2->edges()->to($node1->id()));
        $this->assertCount(1, $node1_2->edges()->to($this->node2->id()));
        $this->assertCount(3, $node1_2->edges()->between($this->node2->id()));
        $this->assertCount(1, $newer_edge_list->to($this->node2->id()));
        $this->assertCount(2, $newer_edge_list->between($this->node2->id()));
        //eval(\Psy\sh());
        $this->assertCount(1, $newest_edge_list->to($this->node2->id()));
        $this->assertCount(3, $newest_edge_list->between($this->node2->id()));
        $this->assertContains((string)$node2_alls->current()->id(), $node1_all_ids);
        $node2_alls->next();
        $this->assertContains((string)$node2_alls->current()->id(), $node1_all_ids);
    }

    public function testSerializeThenAddThenSerializeThenAddAgainForToFromBetween() {
        $get_id = function($value) {return (string) $value->id();};
        $edge_list = $this->node1->edges();
        $node1 = $this->recreateNode($this->node1);
        $second_edge = new Edge($this->node2, $node1);
        $newer_edge_list = $node1->edges();
        //eval(\Psy\sh());
        $node1_new = $this->recreateNode($this->node1, $newer_edge_list->toArray());
        $third_edge = new Edge($this->node2, $node1_new);
        $newest_edge_list = $node1_new->edges();

        //eval(\Psy\sh());
        $this->assertCount(1, $newer_edge_list->to($this->node2->id()));
        $this->assertCount(2, $newer_edge_list->between($this->node2->id()));
        
        $this->assertCount(1, $newest_edge_list->to($this->node2->id()));
        $this->assertEquals($this->edge->id(), $newest_edge_list->to($this->node2->id())->current()->id());
        $this->assertCount(1, $newest_edge_list->to($this->node2->id()));
        $newest_edge_list->to($this->node2->id()); // this is just to even it out, because of mock call.
        $between_node_1_and_2 = array_map($get_id, $newest_edge_list->between($this->node2->id())->getArrayCopy());
        $this->assertCount(3,  $between_node_1_and_2);
        
        $this->assertContains((string)$this->edge->id(), $between_node_1_and_2);
        $this->assertContains((string)$second_edge->id(), $between_node_1_and_2);
        $this->assertContains((string)$third_edge->id(), $between_node_1_and_2);
        $this->assertCount(3, array_map($get_id, $newest_edge_list->between($this->node2->id())->getArrayCopy()));
        $from_node2 = array_map($get_id, $newest_edge_list->from($this->node2->id())->getArrayCopy());
        $this->assertCount(2, $from_node2);
        //eval(\Psy\sh());
        $this->assertContains((string)$second_edge->id(), $from_node2);
        $this->assertContains((string)$third_edge->id(), $from_node2);
        $this->assertCount(2, $this->node2->edges()->to($this->node1->id()));
    }

    private function recreateNode(NodeInterface $node, /*?array*/ $list = null) {
        $node1_mock = \Mockery::mock($node)->shouldAllowMockingProtectedMethods();
        $node1_mock->shouldReceive("hydratedEdge")->andReturnUsing(function($id) {
            $random_edge = new Edge(new Node($this->graph), new Node($this->graph));
            $edge = \Mockery::mock($random_edge);
            $edge->shouldReceive("id")->andReturn(ID::fromString($id));
            return $edge;
        });
        //$node1_mock = $this->recreatedEdgeList($node1_mock, true, (is_null($list) ? $node->edges()->toArray() : $list));
        $node1_mock->shouldReceive("edges")->andReturn(new EdgeList($node1_mock, (!is_null($list) ? $list : $node->edges()->toArray())));
        return $node1_mock;
    }

}