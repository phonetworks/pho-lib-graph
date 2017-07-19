<?php

namespace Pho\Lib\Graph;

trait HookableTrait
{
    protected $hooks;

    protected function hookable() //: mixed
    {
        $caller = debug_backtrace(1);
        if(!isset($caller["function"])||!isset($caller["args"]))
            return;
        $method = $caller["function"];
        $args = $caller["args"];
        if(!isset($this->hooks[$method])) {
            return;
        }
        $this->hooks[$method]->bindTo($this);
        return $this->hooks[$method](...$args);
    }

    /**
     * {@inheritDoc}
     */
    public function hook(string $method, \Closure $call): void
    {
        $this->hooks[$method] = $call;
    }

    /**
     * {@inheritDoc}
     */
    public function unhook(string $method): void
    {
        unset($this->hooks[$method]);
    }
}