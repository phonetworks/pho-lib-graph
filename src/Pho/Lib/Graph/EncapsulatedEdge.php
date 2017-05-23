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
class EncapsulatedEdge implements \Serializable {

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
     *
     * @param EdgeInterface $edge
     */
    private function __construct(EdgeInterface $edge) 
    {
        $this->id = $edge->id();
        $this->object = $edge;
        $this->classes = $this->findClasses(get_class($edge));
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
    public static function create(EdgeInterface $edge): EncapsulatedEdge
    {
        return new EncapsulatedEdge($edge);
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

    /**
    * @internal
    *
    * Used for serialization. 
    *
    * @return string in PHP serialized format.
    */
   public function serialize(): string 
   {
        return serialize(array(
            "id" => $this->id,
            "classes" => $this->classes
        ));
    }

    /**
    * @internal
    *
    * Used for deserialization. 
    *
    * @param string $data 
    *
    * @return void
    */
    public function unserialize(/* mixed */ $data): void 
    {
        $values = unserialize($data);
        foreach ($values as $key=>$value) {
            $this->$key = $value;
        }
    }

}