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
interface NodeInterface {

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
    * Returns an array of contexts this node is a member of.
    *
    * Contexts are GraphInterface objects that contain this node.
    *
    * @return array An array of contexts in no particular order.
    */

   public function contexts(): array;


}