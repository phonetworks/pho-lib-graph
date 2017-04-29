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

    private $out = [];
    private $in = [];
    private $to = [];


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
        $edge_id = function(EdgeInterface $edge): array {
            return (string) $edge->id();
        };
        return array(
            "out" => array_map($edge_id, $this->out),
            "in"  => array_map($edge_id, $this->in)
        );
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
            $this->to[(string) $edge->tail()->id()][] = ["direction"=>Direction::in(), "edge"=>$edge];
            $this->in[] = $edge;
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
            $this->to[(string) $edge->head()->id()][] = ["direction"=>Direction::out(), "edge"=>$edge];
            $this->out[] = $edge;
    }

    /**
    * Returns a list of all the edges directed towards
    * this particular node.
    *
    * @return array An array of EdgeInterface objects.
    */
    public function in(): array 
    {
        return $this->in;
    }

    /**
    * Returns a list of all the edges originating from
    * this particular node.
    *
    * @return array An array of EdgeInterface objects.
    */
    public function out(): array 
    {
        return $this->out;
    }

    /**
    * Returns a list of all the edges (both in and out) pertaining to
    * this particular node.
    *
    * @return array An array of EdgeInterface objects.
    */
    public function all(): array
    {
        return array_merge($this->in, $this->out);
    }

    /**
    * Retrieves a list of edges between this list's owner node to the given 
    * target node.
    *
    * @param NodeInterface  $node Target node.
    *
    * @return array An array of edge objects in between. Returns an empty array if there is no connections in between.
    */
    public function to(ID $node_id): array 
    {
        if(!isset($this->to[(string) $node_id]))
            return [];
        return $this->to[(string) $node_id];
    }


}