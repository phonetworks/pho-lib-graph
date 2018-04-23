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
 * Edge's Tail
 * 
 * Identifies where an edge originates from. Differently from
 * head nodes, the TailNode class implements the Observer
 * pattern as a Subject to observe their edge for deletion.
 * 
 * @see AdjacentNode The parent.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class TailNode extends AdjacentNode
{

    /**
     * {@inheritdoc}
     *
     * Differently from all other entities in the graph, when destroyed,
     * tail nodes notify their outgoing edges.
     * 
     * @return void
     */
    public function destroy(): void
    {
        $this->emit("deleting");
        parent::destroy();
    }

}