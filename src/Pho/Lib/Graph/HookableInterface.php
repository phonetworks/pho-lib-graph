<?php

namespace Pho\Lib\Graph;

interface HookableInterface
{
    //public function execHook(string $method, array $args) //: mixed
    //;

    /**
     * Attaches a closure function to a method.
     *
     * Method must be available to hooks.
     * 
     * @param string $method Method to hook.
     * @param \Closure $call The call to add to the method in question.
     * 
     * @return void
     */
    public function hook(string $method, \Closure $call): void
    ;
    /**
     * Removes a hook.
     * 
     * @param string $method Method to unhook.
     * 
     * @return void
     */
    public function unhook(string $method): void;
}