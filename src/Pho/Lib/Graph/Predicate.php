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
 * Predicates determine Edge capabilities
 * 
 * @see Edge
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class Predicate implements PredicateInterface {

    /**
     * Default Role.
     * 
     * The edge returns itself.
     */
    const R_DEFAULT = 0;

    /**
     * Reflective Role
     * 
     * The edge returns the tail node.
     */
    const R_REFLECTIVE = 1;

    /**
     * Consume Role
     * 
     * The edge returns the head node.
     */
    const R_CONSUMER = 2;

    /**
     * The role of the edge.
     *
     * Determines the edge's return value.
     * Can be R_CONSUMER, R_REFLECTIVE or falls back
     * to the default value (0) which returns the edge itself.
     * 
     * @var int
     */
    protected $role = self::R_DEFAULT;

    /**
     * Holds the binding value.
     * 
     * In this generic implementation, we set it false.
     *
     * @var boolean
     */
    protected $binding = false;

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
    public function role(): int
    {
        switch($this->role) {
            case self::R_REFLECTIVE:
            case self::R_CONSUMER:
                return $this->role;
            default:
                return self::R_DEFAULT;
        }
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
