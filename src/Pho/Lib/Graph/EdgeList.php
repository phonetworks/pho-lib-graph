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