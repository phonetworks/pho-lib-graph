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
 * Interface for the Predicate class.
 * 
 * @todo directed()
 * 
 * @see Predicate
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
interface PredicateInterface
{

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
     * Checks if the predicate is multiplicable.
     *
     * If a predicate is multiplicable, its edge may be
     * created multiple times between a particular tail
     * and a particular head.
     * 
     * @return bool
     */
    public function multiplicable(): bool;

    /**
     * Returns the full class name
     * 
     * @return string
     */
    public function __toString(): string;
    
    /**
     * Returns the predicate in string format
     *
     * Label is actually the short-form class name. For
     * full class name, use ```(string) $predicate```
     *
     * @return string
     */
    public function label(): string;

}
