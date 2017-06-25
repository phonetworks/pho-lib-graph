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
        $this->id = ID::generate();
        $this->attributes = new AttributeBag($this);
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
        return [
           "id" => (string) $this->id,
            "attributes" => $this->attributes->toArray()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->entityToArray();
    }

    /**
     * {@inheritdoc}
     */
    public function update(\SplSubject $subject): void
    {
        if($subject instanceof AttributeBag && $this instanceof NodeInterface) {
            $this->observeAttributeBagUpdate($subject);
        }
        else if($subject instanceof NodeInterface && $this instanceof GraphInterface) {
            $this->observeNodeDeletion($subject);
        }
    }


    /**
     * Attribute Bags use this method to update about setters
     *
     * @param \SplSubject $subject Updater.
     *
     * @return void
     */
    protected function observeAttributeBagUpdate(\SplSubject $subject): void
    {
        
    }


}
