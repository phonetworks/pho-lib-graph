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

    private $warm = false;
    private $data_fridge = [];

    private $out = [];
    private $in = [];
    private $to = [];

    /**
     * Constructor
     * 
     * For performance reasons, the constructor doesn't load the seed data 
     * (if available) but waits for a method to attempt to access.
     * 
     * @param array $data Initial data to seed.
     */
    public function __construct(array $data = [])
    {
        if(count($data)==0)
            $this->warm = true;
        else
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
        $edge_id = function(EdgeInterface $edge): string {
            return (string) $edge->id();
        };
        return array(
            "out" => 
                array_merge(
                    (isset($this->data_fridge["out"]) ? $this->data_fridge["out"] : []), 
                    array_map($edge_id, $this->out /* not function, no warmup!! */)
                ),
            "in"  => 
                array_merge(
                    (isset($this->data_fridge["in"]) ? $this->data_fridge["in"] : []), 
                    array_map($edge_id, $this->in /* not function, no warmup!! */)
                )
        );
    }


    /******************************
     *  REVIEW
     ******************************/ 


       private function warm(): bool
    {
        return (count($this->in) > 0 || count($this->out) > 0);
    }

    /**
     * Fills the object with data (if available)
     * 
     * First, checks if lazy loading is enabled and necessary under current
     * circumstances. Then fills the object with data.
     *
     * @return void
     */
    private function warmup(): void
    {
        if(!$this->warm() && count($this->data_fridge) > 0 ) {
            $this->fromArray($this->data_fridge);
        }
    }

    protected function hydratedWarmup(): bool
    {
        return false;
    }


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
        $edge_class = get_class($edge);
            $this->to[(string) $edge->tail()->id()][] = ["direction"=>Direction::in(), "class"=>$edge_class, "edge"=>$edge];
            $this->in[] = ["class" => $edge_class, "edge" => $edge ];
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
        $edge_class = get_class($edge);
            $this->to[(string) $edge->head()->id()][] = ["direction"=>Direction::out(), "class"=>$edge_class, "edge"=>$edge];
            $this->out[] = ["class" => $edge_class, "edge" => $edge ];
    }

    /**
    * Returns a list of all the edges directed towards
    * this particular node.
    *
    * @param string $class The type of edge (defined in edge class) to return
    *
    * @return array An array of EdgeInterface objects.
    */
    public function in(string $class=""): array 
    {
        $this->warmup();
        return $this->in;
    }

    /**
    * Returns a list of all the edges originating from
    * this particular node.
    *
    * @param string $class The type of edge (defined in edge class) to return
    *
    * @return array An array of EdgeInterface objects.
    */
    public function out(string $class=""): array 
    {
        $this->warmup();
        return $this->out;
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
        $this->warmup();
        return array_merge($this->in, $this->out);
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
        $this->warmup();
        if(!isset($this->to[(string) $node_id]))
            return [];
        return $this->to[(string) $node_id];
    }


}