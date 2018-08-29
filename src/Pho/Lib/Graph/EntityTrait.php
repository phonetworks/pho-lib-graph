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
 * Implements EntityInterface.
 * 
 * This trait is used by Node and Edge classes.
 * 
 * Graphs are mathematical structures used to model pairwise relations between objects. 
 * Entities is a Pho concept used to represent the commonalities between the most 
 * atomic graph elements, Nodes and Edges.
 * 
 * All entities use Observer Pattern to observe the updates from their attribute bags.
 * 
 * @see Edge
 * @see Node
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
trait EntityTrait
{

        /**
         * The entity ID.
         *
         * @var ID
         */
    protected $id;

    /**
     * Attributes of the entity.
     * 
     * Both nodes and edges may hold attributes.
     *
     * @var AttributeBag $attributes;
     */
    protected $attributes;

    /**
     * Constructor.
     * 
     * Assigns a random ID and initializes the object.
     */
    public function __construct()
    {
        $this->id = ID::generate($this);
        $this->attributes = new AttributeBag($this);
    }

    /**
     * {@inheritDoc}
     */
    public function init(): void
    {
        $this->attributes->on("modified", [$this, "emitModified"]);
    }

    /**
     * Emits modified signal
     * 
     * Needed for Closure::fromCallable function
     *
     * @return void
     */
    public function emitModified(): void
    {
        $this->emit("modified");
    }

    /**
     * {@inheritdoc}
     */    
    public function id(): ID
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */    
    public function label(): string
    {
        return strtolower((new \ReflectionObject($this))->getShortName());
    }

    /**
     * {@inheritdoc}
     */    
    public function isA(string $class_name): bool
    {
        return $this instanceof $class_name;
    }

    /**
     * {@inheritdoc}
     */    
    public function attributes(): AttributeBag
    {
        return $this->attributes;
    }

    /**
     * {@inheritdoc}
     */    
    public function destroy(): void
    {
        $this->emit("deleting", [$this->id()]);
    }

    /**
     * Converts the object to array
     *
     * Used for serialization/unserialization. Converts internal 
     * object properties into a simple format to help with
     * reconstruction.
     *
     * @see toArray for actual implementation of this method by subclasses.
     *
     * @return array The object in array format.
     */
    protected function entityToArray(): array
    {
        $array = [
                "id" => (string) $this->id,
                "label" => $this->label(),
                "attributes" => $this->attributes->toArray()
        ];
        if(isset($this->listeners)&&isset($this->listeners_flat)) {
            $array["listeners"] = $this->listeners_flat;
        }
        return $array;
    }
        
   /**
     * {@inheritdoc}
     */
   public function equals(EntityInterface $entity): bool
   {
        return $this->id()->equals($entity->id());       
   }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->entityToArray();
    }

    /**
     * Retrieves the entity type 
     *
     * Current allowed values are "node" or "edge"
     * Can be extended for more types.
     * If the type is unknown, it returns "entity"
     * 
     * @return string "node", "edge" or "entity".
     */
    public function type(): string
    {
        if($this instanceof NodeInterface)
            return "node";
        else if($this instanceof EdgeInterface)
            return "edge";
        else 
            return "entity";
    }

    /**
     * Turns the object into string
     *
     * Returns its ID in string format
     * 
     * @return string The ID
     */
    public function __toString(): string
    {
        return (string) $this->id();
    }

}
