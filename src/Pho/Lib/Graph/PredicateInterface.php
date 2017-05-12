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
 * Interface for the Predicate class.
 * 
 * @see Predicate
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
interface PredicateInterface {

    /**
     * Checks if the predicate is binding.
     *
     * If a predicate is binding, should the edge's tail
     * node is deleted, not only the edge itself gets
     * stripped off, but the head node must be removed 
     * as well.
     * 
     * @return bool
     */
    public function binding(): bool;

    /**
     * Returns the predicate in string format
     * 
     * @return string
     */
    public function __toString(): string;

}