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
 * An worker interface for the Node class
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
interface NodeWorkerInterface
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
     * Retrieve Edge objects given its ID.
     *
     * Used in serialization. This function must be implemented for a higher level
     * package with persistence. Otherwise it has no use and no function within
     * pho-lib-graph.
     * 
     * @see edge 
     *
     * @param string $id The Edge ID in string format
     *
     * @return EdgeInterface
     */
    public function edge(string $id): EdgeInterface;

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


}