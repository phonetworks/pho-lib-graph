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
 * Atomic graph entity, Edge
 * 
 * Edges (aka lines or arcs in graph theory) are used to
 * represent the relationships between Nodes of a Graph
 * therefore it is a fundamental unit of 
 * which graphs are formed.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Edge implements EntityInterface, EdgeInterface, \SplObserver {

    use EntityTrait {
        EntityTrait::__construct as onEntityLoad;
    }

    /**
     * Tail node. Where this originates from.
     *
     * @var TailNode
     */
    private $tail;
    
    /**
     * Head node. Where this is directed towards.
     *
     * @var HeadNode
     */
    private $head;

    /**
     * Predicate.
     *
     * @var PredicateInterface
     */
    private $predicate;

    /**
     * Attributes
     *
     * @var AttributeBag
     */
    private $attributes;


    /**
     * Constructor.
     *
     * @param NodeInterface $tail
     * @param PredicateInterface $predicate
     * @param NodeInterface $head
     */
    public function __construct(NodeInterface $tail, PredicateInterface $predicate, NodeInterface $head) 
    {
        $this->onEntityLoad();

        $this->head = new HeadNode();
        $this->head->set($head);
        $this->head->edges()->addIncoming($this);

        $this->tail = new TailNode();
        $this->tail->set($tail);
        $this->tail->edges()->addOutgoing($this);

        $this->predicate = $predicate;
    }

    /**
     * {@inheritdoc}
     */
   public function head(): HeadNode
   {
    return $this->head;
   }

   /**
     * {@inheritdoc}
     */
   public function tail(): TailNode
   {
    return $this->tail;
   }


   /**
     * {@inheritdoc}
     */
   public function predicate(): PredicateInterface
   {
    return $this->predicate;
   }

   /**
     * {@inheritdoc}
     */
   public function orphan(): bool
   {
       // not implemented
       // edges set at construction.
       return false;
   }

   /**
    * Undocumented function
    *
    * @return void
    */
   public function update(): void
   {
    if($this->predicate->binding()) {
        $this->head()->destroy();
    }
    $this->destroy();
   }

    
}