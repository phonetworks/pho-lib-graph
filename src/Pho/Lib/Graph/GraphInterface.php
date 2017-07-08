<?php declare(strict_types=1);

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
interface GraphInterface
{

    /**
     * Returns the ID of the Graph
     * 
     * Normally the Graph object does not have an ID but it is
     * implemented it here so that the functions that query
     * GraphInterface objects such as SubGraphs, which do have,
     * IDs associated, will be easier to design.
     *
     * @return ID The ID
     */
    public function id(): ID;

    /**
     * Adds a new node to the Graph.
     * 
     * Under normal circumstances, you don't use this function
     * because entities are created with the graph object that
     * they belong to in their constructor function, hence the
     * attachment is committed automatically, including 
     * subgraphs's.
     * 
     * To illustare this, take a look at the following example:
     * ```php
     * $world = new Graph();
     * $google = new SubGraph($world);
     * $mark_zuckerberg = new Node($world); // facebook
     * $larry_page = new Node($google); // google
     * $vincent_cerf = new Node($google); // google
     * print_r($world->toArray()["members]);
     * print_r($google->toArray()["members]);
     * ```
     * The output of the first print_r call, which shows 
     * $world members, will include $google members as well, 
     * even though $google members were created with the
     * $google as their context parameter, and not $world.
     * This is because $google was set to be a subgraph
     * of $world in its constructor function. The link 
     * was made automatically, and this works recursively.
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
     * Retrieves the number of graph's nodes
     *
     * @return int
     */
    public function count(): int;

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


    /**
     * Fills up the graph with members
     * 
     * @param array $nodes An array of NodeInterface objects.
     * 
     * @return void
     */
    public function loadNodesFromArray(array $nodes): void;

    /**
     * Fills up the graph with member IDs
     * 
     * It doesn't actually create objects. Useful for lazy-loading post-unserialization.
     * 
     * @param array $node_ids An array of NodeInterface IDs in string format.
     * 
     * @return void
     * 
     * @throws NodeAlreadyMemberException if the node has already joined the given graph
     */
    public function loadNodesFromIDArray(array $node_ids): void;


}