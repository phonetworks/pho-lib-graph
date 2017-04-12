<?php

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
 * For easy maintenance of TailNode and HeadNode classes.
 * 
 * Edges must have both source and target nodes that behave exactly the same.
 * This class is created to make the portability of TailNode and HeadNode
 * classes easy.
 * 
 * @see TailNode
 * @see HeadNode
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class AdjacentNode implements NodeInterface, EntityInterface {

    private $instance;

    /**
     * Awra rgw 
     *
     * @param NodeInterface $node
     * @return void
     */
    public function set(NodeInterface $node): void
    {
        $this->instance = $node;
    }

    /**
     * @internal
     *
     * @param string $method
     * @param array $arguments
     * @return void
     */
    public function __call(string $method, array $arguments) //: mixed 
    {
        $this->instance->$method(...$arguments);
        $returns = call_user_func_array([$this->instance, $method], $arguments);
        return $returns; // will return null if void.
    }

    /*******************************************************
     * The methods below are achievable with __call()       
     * but must be implemented for Interface compatibility. 
     *******************************************************/ 

     /**
      * {@inheritdoc}
      */
    public function edges(): EdgeList
    {
        return $this->instance->edges();
    }

    /**
      * {@inheritdoc}
      */
    public function context(): GraphInterface
    {
        return $this->instance->context();
    }

    /**
      * {@inheritdoc}
      */
    public function id(): ID
    {
        return $this->instance->id();
    }

    /**
      * {@inheritdoc}
      */
    public function isA(string $class_name): bool
    {
        return $this->instance->isA();
    }

    /**
      * {@inheritdoc}
      */
    public function label(): string
    {
        return $this->instance->label();
    }

    /**
      * {@inheritdoc}
      */
    public function attributes(): AttributeBag
    {
        return $this->instance->attributes();
    }

    /**
      * {@inheritdoc}
      */
    public function destroy(): void
    {
        $this->instance->destroy();
    }

}