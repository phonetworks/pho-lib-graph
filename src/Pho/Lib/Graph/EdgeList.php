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

/**
 * Holds the relationship between nodes and edges.
 * 
 * EdgeList objects are attached to all Node objects, they are
 * created at object initialization. They contain edge objects
 * categorized by their direction. 
 * 
 * @see ImmutableEdgeList For a list that doesn't accept new values.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 * 
 */
class EdgeList {

    private $master;

    private $data_fridge = [];

    private $out = [];
    private $in = [];
    private $between = [];

    /**
     * Constructor
     * 
     * For performance reasons, the constructor doesn't load the seed data 
     * (if available) but waits for a method to attempt to access.
     * 
     * @param NodeInterface $node The master, the owner of this EdgeList object.
     * @param array $data Initial data to seed.
     */
    public function __construct(NodeInterface $node, array $data = [])
    {
        $this->master = $node;
        $this->data_fridge = $data;
    }

    /**
     * Retrieves this object in array format
     *
     * With all "in" and "out" values in simple string format.
     * The "to" array can be reconstructed.
     * 
     * @return array
     */
    public function toArray(): array 
    {
        
        $strip_object = function(array $encapsulated): array {
            return $this->decapsulate($encapsulated);
        };

        $stripped_between = [];
        foreach($this->between as $id => $between) {
            foreach($between as $direction => $encapsulated) {
                $stripped_between[$id][$direction] = $this->decapsulate($encapsulated);
            }
        }

        return array(
            "out" => 
                array_merge(
                    (isset($this->data_fridge["out"]) ? $this->data_fridge["out"] : []), 
                    array_map($strip_object, $this->out)
                ),
            "in"  => 
                array_merge(
                    (isset($this->data_fridge["in"]) ? $this->data_fridge["in"] : []), 
                    array_map($strip_object, $this->in)
                ),
            "between" =>
                array_merge(
                    (isset($this->data_fridge["between"]) ? $this->data_fridge["between"] : []), 
                    $stripped_between
                )

        );
    }


    /******************************
     *  REVIEW
     ******************************/ 


    /**
     * Fills the list with edges from array
     *
     * @param array $data
     * 
     * @return void
     */
    protected function fromArray(array $data): void
    {
        foreach($data as $direction => $edges) {
            $this->_processArray(Direction::fromString($direction), $edges);
        }
    }

/**
 * Internal helper method to feed the object with data
 *
 * Helps the constructor recreated the object from raw data.
 * 
 * @param Direction $direction Direction of the edge.
 * @param array $edges An array that consists of EdgeInterface objects.
 * 
 * @return void
 */
    private function _processArray(Direction $direction, array $edges): void 
    {
        //eval(\Psy\sh());
        foreach($edges as $edge) {
            if($edge instanceof EdgeInterface)
                $this->add($direction, $edge);
        }
    }

 
/******************************
     *  REVIEW ENDS
     ******************************/
 

    

    /**
     * Adds a new edge to the list.
     * 
     * The edge must be already initialized.
     *
     * @param Direction direction
     * @param EdgeInterface $edge
     * 
     * @return void
     */
    public function add(Direction $direction, EdgeInterface $edge): void 
    {   
        if($direction->equals(Direction::in())) {
            $this->addIncoming($edge);
        }
        else {
            $this->addOutgoing($edge);
        }
    }

    /**
     * Removes the given edge from the list.
     *
     * @param ID $edge_id
     * @return void
     */
    // what about the other end? observer pattern.
    /*public function remove(ID $edge_id): void
    {
        $array_remove = function(string $id, array &$haystack) {
            if(($key = array_search($id, $haystack)) !== false) {
                unset($haystack[$key]);
            }
        };
        $array_remove((string) $edge_id, $this->in);
        $array_remove((string) $edge_id, $this->out);
        foreach($this->to as $node=>$edges) {
            foreach($edges as $key=>$edge) {
                if($edge["edge"]->id()->equals($edge_id))
                    unset($this->to[$node][$key]);
            }
        }
    }*/

    /**
     * Adds an incoming edge to the list.
     * 
     * The edge must be already initialized.
     *
     * @param EdgeInterface $edge
     * 
     * @return void
     */
    public function addIncoming(EdgeInterface $edge): void
    {
            $edge_encapsulated = $this->encapsulate($edge);
            $this->between[(string) $edge->tail()->id()][Direction::in()][] = $edge_encapsulated;
            $this->in[] = $edge_encapsulated;
    }

    /**
     * Adds an outgoing edge to the list.
     * 
     * The edge must be already initialized.
     *
     * @param EdgeInterface $edge
     * 
     * @return void
     */
    public function addOutgoing(EdgeInterface $edge): void
    {
            $edge_encapsulated = $this->encapsulate($edge);
            $this->between[(string) $edge->head()->id()][Direction::out()][] = $edge_encapsulated;
            $this->out[] = $edge_encapsulated;
    }

    private function encapsulate(EdgeInterface $edge): array
    {
        $get_ancestors = function(string $class): array
        {
            for ($classes[] = $class; $class = get_parent_class ($class); $classes[] = $class); 
                return $classes;
        };
        return ["id" => (string) $edge->id(), "classes"=>$get_ancestors(get_class($edge)), "object"=>$edge];
    }

    private function decapsulate(array $encapsulated): array
    {
        return ["id"=>$encapsulated["id"], "classes"=>$encapsulated["classes"]];
    }

    /**
    * Returns a list of all the edges directed towards
    * this particular node.
    *
    * @see _retrieve Used by this method to fetch objects.
    *
    * @param string $class The type of edge (defined in edge class) to return
    *
    * @return array An array of EdgeInterface objects.
    */
    public function in(string $class=""): array 
    {
        return $this->_retrieve(Direction::in(), $class);
    }

    /**
    * Returns a list of all the edges originating from
    * this particular node.
    *
    * @see _retrieve Used by this method to fetch objects.
    *
    * @param string $class The type of edge (defined in edge class) to return
    *
    * @return array An array of EdgeInterface objects.
    */
    public function out(string $class=""): array 
    {
        return $this->_retrieve(Direction::out(), $class);
    }


    /**
     * A helper method to retrieve edges.
     * 
     * @see out A method that uses this function
     * @see in A method that uses this function
     *
     * @param Direction $direction Lets you choose to fetch incoming or outgoing edges.
     * @param string $class The type of edge (defined in edge class) to return
     * 
     * @return array An array of EdgeInterface objects.
     */
    protected function _retrieve(Direction $direction, string $class): array
    {
        $d = (string) $direction;

        $hydrate = function(array $encapsulated): EdgeInterface
        {
            return $this->master->hydratedEdge($encapsulated["id"]);
        };

        $filter_classes = function(array $encapsulated) use($class): EdgeInterface
        {
            return in_array($class, $encapsulated["classes"]);
        };

        if(empty($class)) {
            return array_merge(
                    array_column($this->$d, "object"),
                    array_map($hydrate, $this->data_fridge[$d])
            );
        }
        else {
            return array_merge(
                array_column(array_filter($this->$d, $filter_classes), "object"),
                array_map($hydrate, array_filter($this->data_fridge[$d], $filter_classes))
            );
        }
    }

    /**
    * Returns a list of all the edges (both in and out) pertaining to
    * this particular node.
    *
    * @param string $class The type of edge (defined in edge class) to return
    *
    * @return array An array of EdgeInterface objects.
    */
    public function all(string $class=""): array
    {
        return array_merge($this->in($class), $this->out($class));
    }

    /**
    * Retrieves a list of edges between this list's owner node to the given 
    * target node.
    *
    * @param string $class The type of edge (defined in edge class) to return
    * @param NodeInterface  $node Target node.
    *
    * @return array An array of edge objects in between. Returns an empty array if there is no connections in between.
    */
    public function to(ID $node_id, string $class=""): array 
    {
        return $this->_returnDirected(Direction::out(), $node_id, $class);
    }

    public function from(ID $node_id, string $class=""): array
    {
        return $this->_returnDirected(Direction::in(), $node_id, $class);
    }

    public function between(ID $node_id, string $class=""): array
    {
        return array_merge($this->from($node_id, $class), $this->to($node_id, $class));
    }

    /**
     * A helper method to retrieve directed edges.
     * 
     * @see from A method that uses this function
     * @see to A method that uses this function
     *
     * @param Direction $direction Lets you choose to fetch incoming or outgoing edges.
     * @param ID $node_id Directed towards which node.
     * @param string $class The type of edge (defined in edge class) to return.
     * 
     * @return array An array of EdgeInterface objects.
     */
    protected function _retrieveDirected(Direction $direction, ID $node_id, string $class): array
    {
        $hydrate = function(array $encapsulated): EdgeInterface
        {
            return $this->master->hydratedEdge($encapsulated["id"]);
        };

        $filter_classes = function(array $encapsulated) use($class): EdgeInterface
        {
            return in_array($class, $encapsulated["classes"]);
        };

        $return = [];

        if(isset($this->between[(string) $node_id][$direction])) 
        {
            if(empty($class)) 
                $return =  array_column($this->between[(string) $node_id][$direction], "object");
            else {
                $return = array_column(array_filter($this->between[(string) $node_id][$direction], $filter_classes), "object");
            }
        }

        if(isset($this->data_fridge["between"][(string) $node_id][$direction]))
        {
            if(empty($class))
                $return = array_merge($return, array_map($hydrate, $this->data_fridge["between"][(string) $node_id][$direction]));
            else {
                $return = array_merge($return, array_map($hydrate, array_filter($this->data_fridge["between"][(string) $node_id][$direction], $filter_classes)));
            }
        }

        return $return;
    }


}