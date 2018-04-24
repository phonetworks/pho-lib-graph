<?php declare (strict_types=1);

namespace Pho\Lib\Graph\Event;

/**
 * Event Emitter Interface
 *
 * Anything that accepts listeners and emits events should implement this
 * interface.
 *
 * Please note, this is a slightly modified version of Sabre/Event
 * to fit with Pho-Lib-Graph's needs.
 * 
 * @copyright Copyright (C) fruux GmbH (https://fruux.com/)
 * @author Evert Pot (http://evertpot.com/)
 * @author Emre Sokullu <emre@phonetworks.org>
 * @license http://sabre.io/license/ Modified BSD License
 */
interface EmitterInterface {

    /**
     * Subscribe to an event.
     *
     * @return void
     */
    function on(string $eventName, /*mixed*/ $callBack, int $priority = 100);

    /**
     * Emits an event.
     *
     * This method will return true if 0 or more listeners were succesfully
     * handled. false is returned if one of the events broke the event chain.
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
    function emit(string $eventName, array $arguments = []) : bool;

    /**
     * Returns the list of listeners for an event.
     *
     * The list is returned as an array, and the list of events are sorted by
     * their priority.
     *
     * @return callable[]
     */
    function listeners(string $eventName, bool $flat=false) : array;

    /**
     * Removes all listeners.
     *
     * If the eventName argument is specified, all listeners for that event are
     * removed. If it is not specified, every listener for every event is
     * removed.
     *
     * @return void
     */
    function removeAllListeners(string $eventName = null);

}
