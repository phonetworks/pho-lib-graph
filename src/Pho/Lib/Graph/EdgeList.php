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

    public $master;

    private $out = [];
    private $in = [];

    private $from = [];
    private $to = [];

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
        $this->import($data);
    }

    private function import(array $data): void
    {
        if(!$this->isDataSetProperly($data))
            return;

        $wakeup = function(string $serialized): EncapsulatedEdge
        {
            return unserialize($serialized);
        };

        $this->out = array_map($wakeup, $data["out"]);
        $this->in = array_map($wakeup, $data["in"]);
        foreach($data["from"] as $from => $frozen) {
            $this->from[$from] = array_map($wakeup, $frozen);
        }
        foreach($data["to"] as $to => $frozen) {
            $this->to[$to] = array_map($wakeup, $frozen);
        }
    }

    private function isDataSetProperly(array $data): bool
    {
        return (isset($data["in"]) && isset($data["out"]) && isset($data["from"]) && isset($data["to"]));
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
        $deobject = function(EncapsulatedEdge $encapsulated): string  {
            return serialize($encapsulated);
        };

        $returner = function(array $array): array { return $array; };

        $array = [];

        foreach($this->to as $to => $encapsulated) {
            $array["to"][$to] = array_map($deobject, $encapsulated);
        }
        foreach($this->from as $from => $encapsulated) {
            $array["from"][$from] = array_map($deobject, $encapsulated);
        }

        $array["in"] = array_map($deobject, $this->in);
        $array["out"] = array_map($deobject, $this->out);

        return $array;
    }
    

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
            $edge_encapsulated = EncapsulatedEdge::create($edge);
            $this->from[(string) $edge->tail()->id()][(string) $edge->id()] = $edge_encapsulated;
            $this->in[(string) $edge->id()] = $edge_encapsulated;
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
            $edge_encapsulated = EncapsulatedEdge::create($edge);
            $this->to[(string) $edge->head()->id()][(string) $edge->id()] = $edge_encapsulated;
            $this->out[(string) $edge->id()] = $edge_encapsulated;
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

        $hydrate = function(EncapsulatedEdge $encapsulated): EdgeInterface
        {
            if(!$encapsulated->hydrated())
                return $this->master->hydratedEdge($encapsulated->id());
            else
                return $encapsulated->edge();
        };

        $filter_classes = function(EncapsulatedEdge $encapsulated) use($class): bool
        {
            return in_array($class, $encapsulated->classes());
        };

        if(empty($class)) {
            return array_map($hydrate, $this->$d);
        }
        else {
            return array_map($hydrate, array_filter($this->$d, $filter_classes));
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
        return $this->_retrieveDirected(Direction::out(), $node_id, $class);
    }

    public function from(ID $node_id, string $class=""): array
    {
        return $this->_retrieveDirected(Direction::in(), $node_id, $class);
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
        
        $direction = (string) $direction;
        $key = $direction->equals(Direction::in()) ? "from" : "to";

        $hydrate = function(EncapsulatedEdge $encapsulated): EdgeInterface
        {
            if(!$encapsulated->hydrated())
                return $this->master->hydratedEdge($encapsulated->id());
            else
                return $encapsulated->edge();
        };

        $filter_classes = function(EncapsulatedEdge $encapsulated) use($class): bool
        {
            return in_array($class, $encapsulated->classes());
        };

        if(!isset($this->$key[(string) $node_id])) 
            return [];
        
        if(empty($class)) {
            return array_map($hydrate, $this->$key[(string) $node_id]);
        }
        else {
            return array_map($hydrate, array_filter($this->$key[(string) $node_id], $filter_classes));
        }

    }


}