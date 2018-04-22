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
 * Graph contains nodes
 * 
 * Graph contains objects that implement NodeInterface
 * interface, such as Node and Subgraph objects, but not
 * Edges.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Graph implements 
    GraphInterface,
    HookableInterface, 
    \SplObserver, 
    \Serializable, 
    Event\EmitterInterface
{

    use SerializableTrait;
    use Event\EmitterTrait;
    use GraphTrait;

    /**
     * Whether the graph and its subgraphs emit node addition.
     *
     * @var boolean
     */
    protected $emit_node_add_signal = true;

    /**
     * Constructor.
     *
     * @param bool $defer_signals
     */
    public function __construct(bool $emit_node_add_signal = true)
    {
        $this->emit_node_add_signal  = $emit_node_add_signal; 
    }

    /**
     * {@inheritdoc}
     */
    public function id(): ID
    {
        return ID::root();
    }

    public function update(\SplSubject $node): void
    {
        if($subject->inDeletion())
            $this->observeNodeDeletion($node);
        else 
            $this->observeNodeAddition($node);
    }
    
    /**
     * {@inheritDoc}
     */
    public function canEmitNodeAddSignals(): bool
    {
        return (bool) $this->emit_node_add_signal;
    }

}
