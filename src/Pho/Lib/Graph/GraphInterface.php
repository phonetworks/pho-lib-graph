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
 * An interface for the Graph class
 * 
 * @see Graph
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
interface GraphInterface {

    /**
     * Adds a new node to the Graph.
     *
     * @param NodeInterface $node
     * 
     * @return NodeInterface For chaining.
     */
    public function add(NodeInterface $node): NodeInterface;

    /**
     * Checks if the node with given ID is part of the graph.
     *
     * @param ID $node_id
     * 
     * @return bool
     */
    public function contains(ID $node_id): bool;

    /**
     * Retrieves the node with given ID
     *
     * @param ID $node_id
     * 
     * @return NodeInterface
     */
    public function get(ID $node_id): NodeInterface;

    /**
     * Removes the node with given ID
     *
     * @param ID $node_id
     * 
     * @return NodeInterface
     */
    public function remove(ID $node_id): void;


    /**
     * Retrieves the array of members
     *
     * @return array
     */
    public function members(): array;

    /**
    * Converts the object to array
    *
    * Used for serialization/unserialization. Converts internal 
    * object properties into a simple format to help with
    * reconstruction.
    *
    * @return array The object in array format.
    */
    public function toArray(): array;

}