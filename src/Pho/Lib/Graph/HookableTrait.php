<?php

namespace Pho\Lib\Graph;

trait HookableTrait
{
    protected $hooks;

    protected function hookable() //: mixed
    {
        $caller = debug_backtrace(false);
        $method = $caller[1]["function"];
        $args = $caller[1]["args"];
        if(!isset($this->hooks[$method])) {
            return;
        }
        return $this->hooks[$method]->call($this, ...$args);
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

    public function hooks() { return $this->hooks; }
}