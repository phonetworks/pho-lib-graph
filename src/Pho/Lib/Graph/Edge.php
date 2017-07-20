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
 * Atomic graph entity, Edge
 * 
 * Edges (aka lines or arcs in graph theory) are used to
 * represent the relationships between Nodes of a Graph
 * therefore it is a fundamental unit of 
 * which graphs are formed.
 * 
 * Uses Observer Pattern to observe updates from its attribute bags,
 * as well as tail nodes.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Edge implements 
    EntityInterface, 
    EntityWorkerInterface,
    EdgeInterface, 
    HookableInterface,
    \SplObserver, 
    \Serializable,
    Event\EmitterInterface
{
    
    use SerializableTrait;
    use Event\EmitterTrait;
    use EntityTrait {
        EntityTrait::__construct as ____construct;
    }
    use HookableTrait;

    /**
     * Tail node. Where this originates from.
     *
     * @var TailNode
     */
    protected $tail;

    /**
     * The ID of the tail node.
     *
     * @var string
     */
    protected $tail_id;
    
    /**
     * Head node. Where this is directed towards.
     *
     * @var HeadNode
     */
    protected $head;

    /**
     * The ID of the head node.
     *
     * @var string
     */
    protected $head_id = "";

    /**
     * Predicate.
     *
     * @var PredicateInterface
     */
    protected $predicate;

    /**
     * Predicate's label
     *
     * @var string
     */
    protected $predicate_label;



    /**
     * Constructor.
     *
     * @param NodeInterface      $tail
     * @param PredicateInterface $predicate
     * @param NodeInterface      $head
     */
    public function __construct(NodeInterface $tail, ?NodeInterface $head = null, ?PredicateInterface $predicate = null) 
    {
        $this->____construct();

        if(!is_null($head)) {
            $this->head = new HeadNode();
            $this->head->set($head);
            $this->head_id = (string) $head->id();
        }

        $this->tail = new TailNode();
        $this->tail->set($tail);
        $this->tail_id = (string) $tail->id();

        if(!is_null($head)) {
            $this->head->edges()->addIncoming($this);
            $this->tail->edges()->addOutgoing($this);
        }
        $this->predicate = $this->resolvePredicate($predicate, Predicate::class);
        $this->predicate_label = (string) $this->predicate;
        $this->tail->emit("edge.created", [$this]);
    }

    protected function resolvePredicate(?PredicateInterface $predicate, string $fallback): PredicateInterface
    {
        $is_a_predicate = function(string $class_name): bool
        {
            if(!class_exists($class_name))
                return false;
            $reflector = new \ReflectionClass($class_name);
            return $reflector->implementsInterface(PredicateInterface::class);
        };

        if(!is_null($predicate) && $is_a_predicate(get_class($predicate)) ) {
            return $predicate;
        }
        else {
            $predicate_class = get_class($this)."Predicate";
            if($is_a_predicate($predicate_class)) {
                return new $predicate_class;
            }
            else if($is_a_predicate($fallback)) {
                return new $fallback;
            }
            else {
                return new Predicate();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function connect(NodeInterface $head): void
    {
        $this->head = new HeadNode();
        $this->head->set($head);
        $this->head_id = (string) $head->id();
        $this->head()->edges()->addIncoming($this);
        $this->tail()->edges()->addOutgoing($this);
        $this->tail->emit("edge.connected", [$this]);
    }

    /**
     * {@inheritdoc}
     */
    public function head(): NodeInterface
    {
        if($this->orphan()) {
            throw new Exceptions\OrphanEdgeException($this);
        }

        if(isset($this->head)) {
            return $this->head;
        } 
        return $this->hookable();
    }

    /**
     * {@inheritdoc}
     */
    public function headID(): ID
    {
        if($this->orphan()) {
            throw new Exceptions\OrphanEdgeException($this);
        }
        return ID::fromString($this->head_id);
    }

    /**
     * {@inheritdoc}
     */
    public function tail(): NodeInterface
    {
        if(isset($this->tail)) {
            return $this->tail;
        } 
        return $this->hookable();
    }

    /**
     * {@inheritdoc}
     */
    public function tailID(): ID
    {
        return ID::fromString($this->tail_id);
    }

    /**
     * {@inheritdoc}
     */
    public function predicate(): PredicateInterface
    {
        if(isset($this->predicate)) {
            return $this->predicate;
        } 
        return $this->hookable();
    }

    /**
     * {@inheritdoc}
     */
    public function orphan(): bool
    {
        return empty($this->head_id);
    }

    /**
     * Tail Nodes use this method to update about deletion
     *
     * @param \SplSubject $subject Updater.
     *
     * @return void
     */
    protected function observeTailUpdate(\SplSubject $subject): void
    {
        if($this->predicate()->binding()) {
            $this->head()->destroy();
        }
        $this->destroy();
    }
    

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        $array = $this->entityToArray();
        $array["tail"] = $this->tail_id;
        $array["head"] = $this->head_id;
        $array["predicate"] = $this->predicate_label;
        return $array;
    }

    /**
     * {@inheritdoc}
     */
    public function return(): EntityInterface
    {
        switch($this->predicate()->role()) {
        case Predicate::R_CONSUMER:
            return $this->head()->node();
        case Predicate::R_REFLECTIVE:
            return $this->tail()->node();
        default:
            return $this;
        }
    }

}