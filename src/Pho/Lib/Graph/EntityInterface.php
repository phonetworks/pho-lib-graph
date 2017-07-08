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
 * An interface for EntityTrait class.
 * 
 * Graphs are mathematical structures used to model pairwise relations between objects. 
 * Entities is a Pho concept used to represent the commonalities between the most 
 * atomic graph elements, Nodes and Edges.
 * 
 * @see EntityTrait
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
interface EntityInterface
{

    /**
     * Returns the unique id of the entity.
     * 
     * The IDs in Pho Kernel are in the form of cryptographically secure UUIDv4.
     * Even at scale of billions of nodes and edges, the chances of collision 
     * is identical to zero. The IDs are immutable, therefore there is not a 
     * setter method provided.
     *
     * @return ID The ID
     */
    public function id(): ID;


    /**
     * Returns the label of the entity.
     * 
     * Provides a more developer-friendly way of getting class name. 
     * Labels are immutable.
     *
     * @return string The label in all lowercase.
     */
    public function label(): string;


    /**
     * A boolean method that verifies if the entity extends or is the given class.
     * 
     * This method is a helper and works identical to using 
     * ```$node instanceof  $class_name``` or ```$edge instanceof  $class_name``
     * 
     * Make sure the $class_name parameter is namespace-safe with ```get_class()``` 
     * method orthe special **::class** constant 
     * ({@link http://php.net/manual/en/language.oop5.constants.php}).
     * 
     * @param string $class_name 
     *
     * @return bool
     */
    public function isA(string $class_name): bool;

    /**
     * Retrieves the attribute bag associated with this entity.
     * 
     * Once you fetch the bag, you can add new values by 
     * assigning values to the object;
     * ```$entity->attributes()->my_value = 2```
     *
     * Similarly you can fetch values by accessing the properties 
     * of the bag object directly. You can use the PHP ```isset()``` 
     * function to check if they exist or ```unset()``` if the attribute
     * needs to be deleted.
     *
     * @param string $attribute
     *
     * @return string|array|null|bool
     */
    public function attributes(): AttributeBag;



    /**
     * If the purpose is just to free up memory by getting rid of unused entities, 
     * you can use PHP's built-in ```unset()``` method.
     *
     * @return void
     */
    public function destroy(): void;


    /**
     * Converts the object to array
     *
     * Used for serialization/unserialization. Converts internal 
     * object properties into a simple format to help with
     * reconstruction.
     *
     * @return array The object in array format.
     */
    public function toArray(): array;


    /**
     * @internal
     *
     * Observed entities use this method to update the entity.
     *
     * @param \SplSubject $subject Updater.
     *
     * @return void
     */
    public function update(\SplSubject $subject): void;



}