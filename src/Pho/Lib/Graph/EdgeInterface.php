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
 * An interface for the Edge class
 * 
 * Edges (aka lines or arcs in graph theory) are used to
 * represent the relationships between Nodes of a Graph
 * therefore it is a fundamental unit of 
 * which graphs are formed.
 * 
 * @see Edge
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
interface EdgeInterface
{

    /**
     * Returns the node (in its object form) that this edge originates from.
     *
     * @return NodeInterface The source node.
     */
    public function tail(): NodeInterface;

    /**
     * Returns the ID of the tail node.
     *
     * @return ID The tail node ID.
     */
    public function tailID(): ID;

    /**
     * Returns the node (in its object form) that this edge directed towards.
     *
     * @return NodeInterface The head node.
     */
    public function head(): NodeInterface;

    /**
     * Returns the ID of the head node.
     *
     * @return ID The head node ID.
     */
    public function headID(): ID;


    /**
     * Returns the edge's predicate.
     *
     * Predicates represent the unique characteristics of an edge.
     *
     * @return PredicateInterface The predicate.
     */
    public function predicate(): PredicateInterface;

    /**
     * Checks if the Edge has tail and head.
     *
     * If it fails to possess any of tail or head nodes, returns
     * false.
     *
     * @return bool
     */
    public function orphan(): bool;

    /**
     * Connects the edge with a head node.
     *
     * @param NodeInterface $head Head node.
     *
     * @return void
     */
    public function connect(NodeInterface $head): void;

    /**
     * Returns the value
     *
     * @return EntityInterface
     */
    public function return(): EntityInterface;

}