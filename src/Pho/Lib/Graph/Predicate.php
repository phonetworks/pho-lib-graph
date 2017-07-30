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
 * Predicates determine Edge capabilities
 * 
 * @see Edge
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Predicate implements PredicateInterface
{
    /**
     * Holds the binding value.
     * 
     * In this generic implementation, it is set false.
     * An edge with a binding predicate would have its 
     * head deleted when the edge itself is destroyed.
     *
     * @var boolean
     */
    protected $binding = false;

    /**
     * Holds the multiplicable value.
     * 
     * In this generic implementation, it is set false.
     * An edge with a multiplicable predicate can be
     * created multiple times from a particular tail
     * to a particular head.
     *
     * @var boolean
     */
    protected $multiplicable = true;

    /**
     * {@inheritdoc}
     */
    public function binding(): bool
    {
        return $this->binding; 
    }

    /**
     * {@inheritdoc}
     */
    public function multiplicable(): bool
    {
        return $this->multiplicable; 
    }

    /**
     * {@inheritdoc}
     */
    public function __toString(): string
    {
        return get_class($this);
    }
    
     /**
      * {@inheritdoc}
      */
    public function label(): string
    {
        return strtolower((new \ReflectionClass($this))->getShortName());
    }

}
