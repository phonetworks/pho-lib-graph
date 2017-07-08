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
class TailNode extends AdjacentNode implements \SplSubject
{

    /**
     * An array of Observer objects. Used for edges that observe this node.
     *
     * @var array
     */
    private $observers = [];

    /**
     * Attaches an observer to this subject.
     * 
     * In Pho context, edges observe tail nodes to see
     * if they get deleted. 
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
     * Detaches an observer fromto this subject.
     * 
     * In Pho context, edges observe tail nodes to see
     * if they get deleted. 
     * 
     * @param \SplObserver $observer
     * 
     * @return void
     */
    public function detach(\SplObserver $observer): void
    {
        $pos = array_search($observer, $this->observers);
        if($pos!==false) {
            unset($this->observers[$pos]);
        }
    }

    /**
     * Notifies the observer.
     * 
     * In Pho context, edges observe tail nodes to see
     * if they get deleted. 
     * 
     * @return void
     */
    public function notify(): void
    {
        foreach($this->observers as $observer) {
            $observer->update($this);
        }
    }

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
        parent::destroy();
        $this->notify();
    }

}