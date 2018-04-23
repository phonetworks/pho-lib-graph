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
    protected $observers = array();

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