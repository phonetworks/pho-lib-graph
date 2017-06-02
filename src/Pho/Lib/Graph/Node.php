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
 * Uses Observer Pattern to observe updates from its attribute bags.
 * 
 * Last but not least, this class is declared \Serializable. While it does nothing
 * special within this class, this declaration may be useful for subclasses to override
 * and persist data.
 * 
 * @see EdgeList
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Node implements EntityInterface, NodeInterface, \SplObserver,  \SplSubject, \Serializable {

    use SerializableTrait;

    /**
     * Internal variable that keeps track of edges in and out.
     *
     * @var EdgeList
     */
    protected $edge_list;

    /**
     * The graph context of this node
     *
     * @var GraphInterface
     */
    protected $context;

    /**
     * The ID of the graph context of this node
     *
     * @var string
     */
    protected $context_id;

    /**
     * The observers of this object. 
     * Normally just the owner.
     *
     * @var array
     */
    protected $observers = array();

    use EntityTrait {
        EntityTrait::__construct as onEntityLoad;
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(GraphInterface $context) {
        $this->onEntityLoad();
        $this->edge_list = new EdgeList($this);
        $context->add($this)->context = $context;
        $this->context_id = (string) $context->id();
        $this->populateGraphObservers($context);
        Logger::info("A node with id \"%s\" and label \"%s\" constructed", $this->id(), $this->label());
    }

    /**
     * Adds the context itself and the context's contexts (if available)
     * recursively to the list of observers for deletion.
     *
     * @param GraphInterface $context
     * 
     * @return void
     */
    private function populateGraphObservers(GraphInterface $context): void
    {
        while($context instanceof SubGraph) {
            $this->attach($context);
            $context = $context->context();
        }
        $this->attach($context);
    }

    /**
     * {@inheritdoc}
     */
    public function context(): GraphInterface
    {
        if(isset($this->context))
            return $this->context;
        else
            return $this->hydratedContext();
    }

    /**
     * A protected method that enables higher-level packages
     * to provide persistence for the context() call.
     * 
     * @see context() 
     *
     * @return GraphInterface
     */
    protected function hydratedContext(): GraphInterface
    {

    }

    /**
    * {@inheritdoc}
    */
    public function changeContext(GraphInterface $context): void
    {
        $this->context = $context;
        $this->context_id = $context->id();
    }
    
   /**
    * {@inheritdoc}
    */
   public function edges(): EdgeList
   {
       return $this->edge_list;
   }

   /**
    * {@inheritdoc}
    */
   public function toArray(): array
   {
       $array = $this->entityToArray();
       $array["edge_list"] = $this->edge_list->toArray();
       $array["context"] = $this->context_id;
       return $array;
   }

   /**
    * Retrieve Edge objects given its ID.
    *
    * Used in serialization.
    *
    * @param string $id The Edge ID in string format
    *
    * @return EdgeInterface
    */
   public function hydratedEdge(string $id): EdgeInterface
   {

   }

   public function destroy(): void 
   {
       $this->notify();
   }

   /**
     * Adds a new observer to the object
     * 
     * @param \SplObserver $observer
     * 
     * @return void
     */
    public function attach(\SplObserver $observer): void 
    {
        $this->observers[] = $observer;
    }
    
    /**
     * Removes an observer from the object
     * 
     * @param \SplObserver $observer
     * 
     * @return void
     */
    public function detach(\SplObserver $observer): void 
    {
        $key = array_search($observer, $this->observers, true);
        if($key) {
            unset($this->observers[$key]);
        }
    }

    /**
     * Notifies observers about deletion
     * 
     * @return void
     */
    public function notify(): void
    {
        foreach ($this->observers as $value) {
            $value->update($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    /*public function join(GraphInterface $graph): void
    {
        if($graph->contains($this->id())) {
            throw new Exceptions\NodeAlreadyMemberException($this, $graph);
        }
        $graph->add($this);
    }*/

}