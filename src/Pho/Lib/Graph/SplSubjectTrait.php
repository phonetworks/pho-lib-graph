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
 * A trait that helps implement the \SplSubject
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
trait SplSubjectTrait {

    /**
     * The observers of this object. 
     *
     * @var array
     */
    public $observers = array();

    public $observer_ids = array();

    /**
     * Adds a new observer to the object
     * 
     * @param \SplObserver $observer
     * 
     * @return void
     */
    public function attach(\SplObserver $observer): void 
    {
        $id = (string) $observer->id();
        if(!in_array($id, $this->observer_ids)) {
            $this->observer_ids[] = $id;
            $this->observers[] = $observer;
        }
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
        $id = (string) $observer->id();
        $key = array_search($id, $this->observer_ids, true);
        if($key) {
            unset($this->observers[$key]);
            unset($this->observer_ids[$key]);
        }
    }

    /**
     * Notifies observers about a change
     * 
     * @return void
     */
    public function notify(): void
    {
        foreach ($this->observers as $value) {
            $value->update($this);
        }
    }

}