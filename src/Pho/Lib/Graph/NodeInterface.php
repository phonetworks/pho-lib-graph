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
 * An interface for the Node class
 * 
 * A graph is made up of nodes (aka. nodes, or points) which are connected by 
 * edges (aka arcs, or lines) therefore node is the fundamental unit of 
 * which graphs are formed.
 * 
 * Nodes are indivisible, yet they share some common characteristics with edges.
 * In Pho context, these commonalities are represented with the EntityInterface.
 * 
 * @see Node
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
interface NodeInterface
{

    /**
     * Gives access to this node's EdgeList property.
     *
     * EdgeList contains all the edges in and out from 
     * this node. It is also used to add new edges.
     * 
     * @see EdgeList For the full list of this method's capabilities.
     * 
     * @return EdgeList 
     */
    public function edges(): EdgeList;

    /**
     * Returns the context that this node is a member of.
     *
     * Contexts are GraphInterface objects that contain nodes.
     *
     * @return ArrayObject An ArrayObject of contexts in no particular order.
     */
    public function context(): GraphInterface;

    /**
     * Changes the fundamental context of a node.
     *
     * Rarely, a node may need its fundamental context to change 
     * after its construction. This method enables setting a new
     * context for the node.
     *
     * @param GraphInterface $context
     *
     * @return void
     */
    public function changeContext(GraphInterface $context): void;


    /**
    * Adds the node to the given graph
    *
    * This is a replica of {graph}->add
    *
    * Do not confuse this with changeContext. This method does not
    * modify the context of the object, it only adds the node to
    * a graph as a member.
    *
    * @param GraphInterface $graph
    *
    * @return void
    *
    * @throws NodeAlreadyMemberException if the node has already joined the given graph
    */
    //public function join(GraphInterface $graph): void;

    /**
     * Determines whether the node is in self-destruction process.
     * 
     * Used by observers.
     * 
     * @return bool Yes or no.
     */
    public function inDeletion(): bool;


}