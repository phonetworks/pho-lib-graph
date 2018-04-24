<?php declare (strict_types=1);

namespace Pho\Lib\Graph\Event;

/**
 * Event Emitter Trait
 *
 * This trait contains all the basic functions to implement an
 * EventEmitterInterface.
 *
 * Using the trait + interface allows you to add EventEmitter capabilities
 * without having to change your base-class.
 * 
 * Please note, this is a slightly modified version of Sabre/Event
 * to fit with Pho-Lib-Graph's needs.
 *
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @author Emre Sokullu <emre@phonetworks.org>
 * @license http://sabre.io/license/ Modified BSD License
 */
trait EmitterTrait {

    /**
     * The list of listeners
     *
     * @var array
     */
    protected $listeners = [];
    protected $listeners_flat = [];


    /**
     * Subscribe to an event.
     *
     * @return void
     */
    function on(string $eventName, /*mixed*/ $callBack, int $priority = 100) 
    {
        if(!is_callable($callBack)) {
            error_log("callback is not callable");
        }
        if(is_array($callBack)) {
            foreach($this->listeners($eventName, true) as $listener) {
                if($listener[1]==$callBack[1]) {
                    if(is_object($listener[0])) {
                        if($listener[0]->id()->equals($callBack[0]->id()))
                            return;
                    }
                    if($listener[0]==$callBack[0]->id()->toString())
                        return;
                }
            }
        }
        if (!isset($this->listeners[$eventName])) {
            $this->listeners[$eventName] = [
                true,  // If there's only one item, it's sorted
                [$priority],
                [\Closure::fromCallable($callBack)]
            ];
            if(is_array($callBack)) {
                $this->listeners_flat[$eventName] = [
                    true,  // If there's only one item, it's sorted
                    [$priority],
                    [[$callBack[0]->id()->toString(), $callBack[1]]]
                ];
            }
        } else {
            $this->listeners[$eventName][0] = false; // marked as unsorted
            $this->listeners[$eventName][1][] = $priority;
            $this->listeners[$eventName][2][] =  \Closure::fromCallable($callBack);
            if(is_array($callBack)) {
                $this->listeners_flat[$eventName][0] = false;
                $this->listeners_flat[$eventName][1][] = $priority;
                $this->listeners_flat[$eventName][2][] = [$callBack[0]->id()->toString(), $callBack[1]];
            }
        }
    }

    /**
     * Emits an event.
     *
     * This method will return true if 0 or more listeners were succesfully
     * handled. false is returned if one of the events broke the event chain.
     * 
     * **Pho: Please note, continueCallback does not exist with Pho**
     *
     * If the continueCallBack is specified, this callback will be called every
     * time before the next event handler is called.
     *
     * If the continueCallback returns false, event propagation stops. This
     * allows you to use the eventEmitter as a means for listeners to implement
     * functionality in your application, and break the event loop as soon as
     * some condition is fulfilled.
     *
     * Note that returning false from an event subscriber breaks propagation
     * and returns false, but if the continue-callback stops propagation, this
     * is still considered a 'successful' operation and returns true.
     *
     * Lastly, if there are 5 event handlers for an event. The continueCallback
     * will be called at most 4 times.
     */
    function emit(string $eventName, array $arguments = []) : bool {

            foreach ($this->listeners($eventName) as $listener) {

                $result = \call_user_func_array($listener, $arguments);
                if ($result === false) {
                    return false;
                }
            }

        return true;

    }

    /**
     * Returns the list of listeners for an event.
     *
     * The list is returned as an array, and the list of events are sorted by
     * their priority.
     *
     * @return callable[]
     */
    function listeners(string $eventName, bool $flat=false) : array {

        if (!isset($this->listeners[$eventName])) {
            return [];
        }

        // The list is not sorted
        if (!$this->listeners[$eventName][0]) {

            // Sorting
            \array_multisort($this->listeners[$eventName][1], SORT_NUMERIC, $this->listeners[$eventName][2]);
            \array_multisort($this->listeners_flat[$eventName][1], SORT_NUMERIC, $this->listeners_flat[$eventName][2]);

            // Marking the listeners as sorted
            $this->listeners[$eventName][0] = true;
            $this->listeners_flat[$eventName][0] = true;
        }

        if(!$flat)
            return $this->listeners[$eventName][2];
        else
            return $this->listeners_flat[$eventName][2];

    }

    /**
     * Removes all listeners.
     *
     * If the eventName argument is specified, all listeners for that event are
     * removed. If it is not specified, every listener for every event is
     * removed.
     *
     * @return void
     */
    function removeAllListeners(string $eventName = null) {

        if (!\is_null($eventName)) {
            unset($this->listeners[$eventName]);
            unset($this->listeners_flat[$eventName]);
        } else {
            $this->listeners = [];
            $this->listeners_flat = [];
        }

    }

}
