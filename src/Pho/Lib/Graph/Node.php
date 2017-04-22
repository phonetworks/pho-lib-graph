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
 * Atomic graph entity, Node
 * 
 * A graph is made up of nodes (aka. nodes, or points) which are connected by 
 * edges (aka arcs, or lines) therefore node is the fundamental unit of 
 * which graphs are formed.
 * 
 * Nodes are indivisible, yet they share some common characteristics with edges.
 * In Pho context, these commonalities are represented with the EntityInterface.
 * 
 * @see EdgeList
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Node implements EntityInterface, NodeInterface {

    /**
     * Internal variable that keeps track of edges in and out.
     *
     * @var EdgeList
     */
    private $edge_list;

    /**
     * The graph context of this node
     *
     * @var GraphInterface
     */
    private $context;

    use EntityTrait {
        EntityTrait::__construct as onEntityLoad;
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(GraphInterface $context) {
        $this->onEntityLoad();
        $this->edge_list = new EdgeList();
        $context->add($this)->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function context(): GraphInterface
    {
        return $this->context;
    }
    
   /**
    * {@inheritdoc}
    */
   public function edges(): EdgeList
   {
       return $this->edge_list;
   }

   public function toArray(): array
   {
       $array = parent::toArray();
       $array["edge_list"] = $this->edge_list->toArray();
       return $array;
   }

}