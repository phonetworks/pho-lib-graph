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
     * {@inheritdoc}
     */
    public function id(): ID
    {
        return ID::root();
    }

    public function update(\SplSubject $node): void
    {
        $this->observeNodeDeletion($node);
    }
    
}
