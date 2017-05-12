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
     * Holds the binding value.
     * 
     * In this generic implementation, we set it false.
     *
     * @var boolean
     */
    private $binding = false;

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
    public function __toString(): string
    {
        return strtolower((new \ReflectionClass($this))->getShortName());
    }

}