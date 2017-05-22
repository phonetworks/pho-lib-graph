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
        $edge_list = $this->node1->edges()->toArray();
        $new_edge_list = $this->recreateEdgeListForNode1($edge_list, $this->edge);
        $this->assertEquals($this->node1->edges()->toArray(), $new_edge_list->toArray());
    }

    public function testInOutAll() {
        $outs = $this->node1->edges()->out();
        $ins = $this->node2->edges()->in();
        $this->assertEquals($ins[0]->id(), $outs[0]->id());
        $alls = $this->node2->edges()->all();
        $this->assertEquals($ins[0]->id(), $alls[0]->id());
    }

    public function testInOutAllFromSerialized() {
        $edge_list = $this->node1->edges()->toArray();
        $new_edge_list = $this->recreateEdgeListForNode1($edge_list, $this->edge);
        $outs = $new_edge_list->out();
        $ins = $this->node2->edges()->in();
        $this->assertEquals($ins[0]->id(), $outs[0]->id());
        $alls = $new_edge_list->all();
        $this->assertEquals($ins[0]->id(), $alls[0]->id());
    }

    public function testToFromBetween() {
        $to = $this->node1->edges()->to($this->node2->id());
        $from = $this->node2->edges()->from($this->node1->id());
        $this->assertEquals($to[0]->id(), $from[0]->id());
        $between = $this->node2->edges()->between($this->node1->id());
        $this->assertEquals($to[0]->id(), $between[0]->id());
    }

    public function testToFromBetweenSerialized() {
        $edge_list = $this->node1->edges()->toArray();
        //eval(\Psy\sh());
        $new_edge_list = $this->recreateEdgeListForNode1($edge_list, $this->edge);
        $to = $new_edge_list->to($this->node2->id());
        $from = $this->node2->edges()->from($this->node1->id());
        $this->assertEquals($to[0]->id(), $from[0]->id());
        $between = $new_edge_list->between($this->node2->id());
        $this->assertEquals($from[0]->id(), $between[0]->id());
    }

    /**
     * Case of wrong hydration
     * Which shouldn't happen. This is the test the "right" one actually works.
     */
    public function testInOutAllFromSerializedWithFaultyEdge() {
        $edge_list = $this->node1->edges()->toArray();
        //$GLOBALS["test"] = 1;
        $new_edge_list = $this->recreateEdgeListForNode1($edge_list, (new Edge($this->node2, $this->node1)));
        $outs = $new_edge_list->out();
        $ins = $this->node2->edges()->in();
        $this->assertNotEquals($ins[0]->id(), $outs[0]->id());
        $alls = $new_edge_list->all();
        $this->assertNotEquals($ins[0]->id(), $alls[0]->id());
    }

    public function testSerializeThenAdd() {
        $get_id = function($value) {return $value->id();};
        $edge_list = $this->node1->edges()->toArray();
        $new_edge_list = $this->recreateEdgeListForNode1($edge_list, $this->edge);
        $this->assertCount(0, $this->node2->edges()->to($this->node1->id()));
        $this->assertCount(1, $this->node2->edges()->from($this->node1->id()));
        $this->assertCount(0, $this->node1->edges()->from($this->node2->id()));
        $this->assertCount(1, $this->node1->edges()->to($this->node2->id()));
        $new_edge_list->addIncoming((new Edge($this->node2, $this->node1)));
        $node1_outs = $new_edge_list->out();
        $node1_ins = $new_edge_list->in();
        $node2_ins = $this->node2->edges()->in();
        $node2_outs = $this->node2->edges()->out();
        $this->assertEquals($node2_ins[0]->id(), $node1_outs[0]->id());
        $this->assertEquals($node2_outs[0]->id(), $node1_ins[0]->id());
        $this->assertEquals($this->node2->edges()->from($this->node1->id())[0]->id(), $node1_outs[0]->id());
        $this->assertEquals($this->node2->edges()->to($this->node1->id())[0]->id(), $node1_ins[0]->id());
        $this->assertCount(1, $this->node2->edges()->from($this->node1->id()));
        $this->assertCount(1, $this->node2->edges()->to($this->node1->id()));
        $this->assertCount(1, $this->node2->edges()->to($this->node1->id()));
        $this->assertCount(1, $this->node2->edges()->from($this->node1->id()));
        $node1_alls = $new_edge_list->all();
        $node1_all_ids = array_map($get_id, $node1_alls);
        $node2_alls = $this->node2->edges()->all();
        $this->assertContains($node2_ins[0]->id(), $node1_all_ids);
        $this->assertContains($node2_outs[0]->id(), $node1_all_ids);
        $this->assertContains($node2_alls[0]->id(), $node1_all_ids);
        $this->assertContains($node2_alls[1]->id(), $node1_all_ids);
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
        $edge_list = $this->node1->edges()->toArray();
        $new_edge_list = $this->recreateEdgeListForNode1($edge_list, $this->edge);
        $new_predicate = new class extends Predicate {};
        $second_edge = new class($this->node1, $this->node2, $new_predicate) extends Edge {};
        $new_edge_class = get_class($second_edge);
        $new_edge_list->addOutgoing($second_edge);
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
        $this->assertCount(2, $this->node2->edges()->from($this->node1->id()));
        $this->assertCount(2, $this->node2->edges()->between($this->node1->id()));
        $this->assertCount(1, $new_edge_list->to($this->node2->id(),$new_edge_class));
        $this->assertCount(1, $this->node2->edges()->from($this->node1->id(),$new_edge_class));
        $this->assertCount(1, $this->node2->edges()->between($this->node1->id(),$new_edge_class));
        $this->assertCount(2, $new_edge_list->to($this->node2->id(),Edge::class));
        $this->assertCount(2, $this->node2->edges()->from($this->node1->id(),Edge::class));
        $this->assertCount(2, $this->node2->edges()->between($this->node1->id(),Edge::class));
    }

    public function testSerializeThenAddThenSerializeThenAddAgain() {
        $get_id = function($value) {return (string) $value->id();};
        $edge_list = $this->node1->edges()->toArray();
        $newer_edge_list = $this->recreateEdgeListForNode1($edge_list, $this->edge);
        $second_edge = new Edge($this->node2, $this->node1);
        $newer_edge_list->addIncoming($second_edge);
        $newest_edge_list = $this->recreateEdgeListForNode1($newer_edge_list->toArray(), $this->edge, $second_edge);
        $newest_edge_list->addIncoming((new Edge($this->node2, $this->node1)));
        $node1_outs = $newest_edge_list->out();
        $node1_ins = $newest_edge_list->in();
        $node2_ins = $this->node2->edges()->in();
        $node2_outs = $this->node2->edges()->out();
        eval(\Psy\sh());
        $this->assertEquals($node2_ins[0]->id(), $node1_outs[0]->id());
        $node1_in_ids = array_map($get_id, $node1_ins);
        $this->assertContains((string)$node2_outs[0]->id(), $node1_in_ids);
        $this->assertContains((string)$node2_outs[1]->id(), $node1_in_ids);
        $node1_alls = $newest_edge_list->all();
        $node1_all_ids = array_map($get_id, $node1_alls);
        $node2_alls = $this->node2->edges()->all();
        $this->assertContains((string)$node2_ins[0]->id(), $node1_all_ids);
        $this->assertContains((string)$node2_outs[0]->id(), $node1_all_ids);
        $this->assertContains((string)$node2_alls[0]->id(), $node1_all_ids);
        $this->assertContains((string)$node2_alls[1]->id(), $node1_all_ids);
        //eval(\Psy\sh());
        $this->assertContains((string)$node2_ins[0]->id(), array_map($get_id, $newest_edge_list->between($this->node2->id())));
        $this->assertContains((string)$this->node2->edges()->to($this->node1->id())[0]->id(), $node1_all_ids);
        $this->assertCount(2, $this->node2->edges()->to($this->node1->id()));
        $this->assertCount(1, $this->node1->edges()->to($this->node2->id()));
        $this->assertCount(3, $this->node1->edges()->between($this->node2->id()));
        //$this->assertCount(1, $newer_edge_list->to($this->node2->id()));
        //$this->assertCount(2, $newer_edge_list->between($this->node2->id()));
        //eval(\Psy\sh());
        //$this->assertCount(1, $newest_edge_list->to($this->node2->id()));
        //$this->assertCount(2, $newest_edge_list->between($this->node2->id()));
        //$this->assertContains((string)$node2_alls[0]->id(), $node1_all_ids);
        //$this->assertContains((string)$node2_alls[1]->id(), $node1_all_ids);
    }

    public function testSerializeThenAddThenSerializeThenAddAgainForToFromBetween() {
        $get_id = function($value) {return (string) $value->id();};
        $edge_list = $this->node1->edges()->toArray();
        $newer_edge_list = $this->recreateEdgeListForNode1($edge_list, $this->edge);
        $second_edge = new Edge($this->node2, $this->node1);
        $newer_edge_list->addIncoming($second_edge);
        //eval(\Psy\sh());
        $newest_edge_list = $this->recreateEdgeListForNode1($newer_edge_list->toArray(), $this->edge, $second_edge);
        $third_edge = new Edge($this->node2, $this->node1);
        $newest_edge_list->addIncoming($third_edge);
        
        $this->assertCount(1, $newer_edge_list->to($this->node2->id()));
        $this->assertCount(2, $newer_edge_list->between($this->node2->id()));
        
        //$this->assertCount(1, $newest_edge_list->to($this->node2->id()));
        $this->assertEquals($this->edge->id(), $newest_edge_list->to($this->node2->id())[0]->id());
        $this->assertCount(1, $newest_edge_list->to($this->node2->id()));
        $newest_edge_list->to($this->node2->id()); // this is just to even it out, because of mock call.
        $between_node_1_and_2 = array_map($get_id, $newest_edge_list->between($this->node2->id()));
        //$this->assertCount(3,  $between_node_1_and_2);
        
        //$this->assertContains((string)$this->edge->id(), $between_node_1_and_2);
        //$this->assertContains((string)$second_edge->id(), $between_node_1_and_2);
        //$this->assertContains((string)$third_edge->id(), $between_node_1_and_2);
        //$this->assertContains(3, array_map($get_id, $newest_edge_list->between($this->node2->id())));
        $from_node2 = array_map($get_id, $newest_edge_list->from($this->node2->id()));
        $this->assertCount(2, $from_node2);
        //eval(\Psy\sh());
        $this->assertContains((string)$second_edge->id(), $from_node2);
        $this->assertContains((string)$third_edge->id(), $from_node2);
        $this->assertCount(2, $this->node2->edges()->to($this->node1->id()));
    }

    private function recreateEdgeListForNode1(array $edge_list_array, EdgeInterface $master): EdgeList
    {
        foreach($edge_list_array as $direction => $edges) {
            foreach($edges as $id => $edge) {
                foreach($edge as $key => $encapsulated) {
                    if(isset($this->edge_store[$id])) {
                        $edge_list_array[$direction][$id][$key] = unserialize($this->edge_store[$id])->id();
                    }
                }
            }
        }
        $node1_mock = \Mockery::mock($this->node1)->shouldAllowMockingProtectedMethods();
        $node1_mock->shouldReceive("hydratedEdge")->andReturnUsing(function($id) use ($master) {
            $GLOBALS["id_feededgelisttest"] = $id;
            return new class($master->tail(), $master->head(), $master->predicate()) extends Edge {
                public function id(): ID { return ID::fromString($GLOBALS["id_feededgelisttest"]); }
            };
            unset($GLOBALS["id_feededgelisttest"]);
        });
        
        return new EdgeList($node1_mock, $edge_list_array);
    }


}