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

use Sabre\Event;

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
class Node implements 
    EntityInterface, 
    NodeInterface, 
    \SplObserver,  
    \SplSubject, 
    \Serializable, 
    Event\EmitterInterface
{

    use SerializableTrait;
    use SplSubjectTrait;
    use EntityTrait {
        EntityTrait::__construct as onEntityLoad;
    }
    use Event\EmitterTrait;

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
     * A flag that states that the destruction process has begun.
     *
     * @var boolean
     */
    protected $in_deletion = false;

    /**
     * {@inheritdoc}
     */
    public function __construct(GraphInterface $context) 
    {
        $this->onEntityLoad();
        $this->edge_list = new EdgeList($this);
        $context->add($this)->context = $context;
        $this->context_id = (string) $context->id();
        $this->attachGraphObservers($context);
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
    private function attachGraphObservers(GraphInterface $context): void
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
        if(isset($this->context)) {
            return $this->context;
        } else {
            return $this->hyContext();
        }
    }

    /**
     * A protected method that enables higher-level packages
     * to provide persistence for the context() call.
     * 
     * @see context() 
     *
     * @return GraphInterface
     */
    protected function hyContext(): GraphInterface
    {

    }

    /**
     * {@inheritdoc}
     */
    public function changeContext(GraphInterface $context): void
    {
        $this->emit("modified");
        $this->context->remove($this->id());
        $this->context = $context;
        $this->context_id = $context->id();
        $this->context->add($this);
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
        $array["context"] = (string) $this->context_id;
        return $array;
    }

    /**
     * Retrieve Edge objects given its ID.
     *
     * Used in serialization.
     * 
     * @see edge 
     *
     * @param string $id The Edge ID in string format
     *
     * @return EdgeInterface
     */
    public function hyEdge(string $id): EdgeInterface
    {

    }

    /**
     * {@inheritdoc}
     */
    public function destroy(): void 
    {
        $this->emit("deleting");
        $this->in_deletion = true;
        $this->notify();
    }

    /**
     * {@inheritdoc}
     */
    public function inDeletion(): bool
    {
        return $this->in_deletion;
    }

}