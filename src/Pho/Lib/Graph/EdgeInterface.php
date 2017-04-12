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
 * An interface for the Edge class
 * 
 * Edges (aka lines or arcs in graph theory) are used to
 * represent the relationships between Nodes of a Graph
 * therefore it is a fundamental unit of 
 * which graphs are formed.
 * 
 * @see Edge
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
interface EdgeInterface {

   /**
    * Returns the node (in its object form) that this edge originates from.
    *
    * @return TailNode The source node.
    */
   public function tail(): array;

   /**
    * Returns the node (in its object form) that this edge directed towards.
    *
    * @return HeadNode The head node.
    */
   public function head(): array;


   /**
    * Returns the edge's predicate.
    *
    * Predicates represent the unique characteristics of an edge.
    *
    * @return PredicateInterface The predicate.
    */
   public function predicate(): array;

   /**
    * Checks if the Edge has tail and head.
    *
    * If it fails to possess any of tail or head nodes, returns
    * false.
    *
    * @return bool
    */
   public function orphan(): bool;


}