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
 * Encapsulated Edge used inside EdgeList
 * 
 * Encapsulated edges are array-like objects that encapsulate the actual edge
 * for easy manipulation inside EdgeList. 
 * 
 * They consist of:
 * 
 * * id: The edge id.
 * * object: The edge itself
 * * classes: All the ancestor classes of the edge in an array
 * 
 * The encapsulated edges may or may not have the object implemented. Whether 
 * they have it or not, can be learned by calling the ```hydrated()``` function
 * that returns a boolean value.
 * 
 * A dehydrated encapsulated edge is how edges are stored in the database. The edge
 * object can be recaptured from the ID.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class EncapsulatedEdge {

    /**
     * @var EdgeInterface
     */
    private $object;

    /**
     * @var array
     */
    private $classes = [];

    /**
     * @var ID
     */
    private $id;

    /**
     * Constructor.
     */
    private function __construct() 
    {
        
    }

    /**
     * The ID of the edge
     *
     * @return ID
     */
    public function id(): ID
    {
        return $this->id;
    }

    /**
     * Returns the classes that the edge inherits from.
     *
     * @return array
     */
    public function classes(): array
    {
        return $this->classes;
    }

    /**
     * Retrieves ancestor classes of a given edge.
     *
     * @param string $class The class name of the edge.
     * 
     * @return array Ancestors
     */
    private function findClasses(string $class): array
    {
        for ($classes[] = $class; $class = get_parent_class ($class); $classes[] = $class); 
            return $classes;
    }
    
    /**
     * Creates a new capsule from an edge.
     *
     * @param EdgeInterface $edge
     * 
     * @return EncapsulatedEdge A hydrated encapsulated edge object.
     */
    public static function fromEdge(EdgeInterface $edge): EncapsulatedEdge
    {
        $encapsulated =  new EncapsulatedEdge();
        $encapsulated->id = $edge->id();
        $encapsulated->object = $edge;
        $encapsulated->classes = $encapsulated->findClasses(get_class($edge));
        return $encapsulated;
    }

    public static function fromArray(array $array): EncapsulatedEdge
    {
        if(!isset($array["id"])
            || !isset($array["classes"])
            || (!$array["id"] instanceof ID && !is_string($array["id"])) /* added fault-tolerance with is_string check */
            || !is_array($array["classes"])
        ) {
            throw new Exceptions\InvalidEncapsulatedEdgeException($array);
        }
        $encapsulated =  new EncapsulatedEdge();
        $encapsulated->id = is_string($array["id"]) ? ID::fromString($array["id"]) : $array["id"];
        $encapsulated->classes = $array["classes"];
        return $encapsulated;
    }

    /**
     * Returns the capsule without the edge object inside.
     *
     * Used in serialization.
     * 
     * @return array
     */
    private function deobject(): array
    {
        unset($this->object);
    }

    /**
     * Whether the edge object is present or not
     *
     * @return bool
     */
    public function hydrated(): bool
    {
        return (isset($this->object) && $this->object instanceof EdgeInterface);
    }

    /**
     * Retrieves the edge object
     *
     * @return EdgeInterface
     */
    public function edge(): EdgeInterface
    {
        return $this->object;
    }

    public function toArray(): array
    {
        return array(
            "id" => $this->id,
            "classes" => $this->classes
        );
    }

}