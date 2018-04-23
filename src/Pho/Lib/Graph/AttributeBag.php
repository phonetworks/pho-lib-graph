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

use Sabre\Event;

/**
 * Holds edge and node attributes
 * 
 * All graph entities may hold attributes. AttributeBag class
 * is a common attribute of both edges and nodes.
 * 
 * ```
 * $node = new Node();
 * $node->attributes()->color = "red";
 * if(isset($node->attributes()->color))
 *   echo $node->attributes()->color; // prints "red"
 * unset($this->attributes()->color);
 * echo $this->attributes()->color; // doesn't print
 * ```
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class AttributeBag implements Event\EmitterInterface
{
    use Event\EmitterTrait;

    /**
     * Holds the attributes of a node in an array
     *
     * @var array
     */
    protected $bag = [];

    /**
     * The entity (node or graph) that this bag belongs to.
     *
     * @var EntityInterface
     */
    protected $owner;

    /**
     * Constructor.
     *
     * Parameter optional.
     * 
     * @param array $bag Initial bag. Defaults to an empty array.
     */
    public function __construct(EntityInterface $owner, array $bag = []) 
    {
        $this->owner = $owner;
        if(count($bag)>0) {
            $this->bag = $bag;
        }
    }

    /**
     * Retrieves the bag in array format
     * 
     * Useful for serialization/unserialization.
     *
     * @return array The object in pure array key/value pair form.
     */
    public function toArray(): array
    {
        return $this->bag;
    }

    /**
     * @internal
     * Fetches value
     *
     * @param string $attribute
     * 
     * @return mixed
     */
    public function __get(string $attribute)
    {
        if(!isset($this->$attribute)) {
            return null;
        }
        return $this->bag[$attribute];
    }

    /**
     * @internal
     * Checks if key exists
     *
     * @param string $attribute
     * 
     * @return bool
     */
    public function __isset(string $attribute): bool
    {
        return isset($this->bag[$attribute]);
    }

    /**
     * @internal
     * Sets up a key/value pair
     *
     * @see silent() for a silent version of this.
     * 
     * @param string            $attribute
     * @param string|bool|array $value
     * 
     * @return void
     */
    public function __set(string $attribute, /*string|bool|array*/ $value): void
    {
        $this->bag[$attribute] = $value;
        $this->emit("modified");
    }

    /**
     * Silent setter
     * 
     * Sets a value without notifying the master object.
     * 
     * @param string            $attribute
     * @param string|bool|array $value
     * 
     * @return void
     */
    public function quietSet(string $attribute, /*string|bool|array*/ $value): void
    {
        $this->bag[$attribute] = $value;
    }

    /**
     * @internal
     * Removes a key/value pair
     *
     * @param string $attribute
     * 
     * @return void
     */
    public function __unset(string $attribute): void
    {
        unset($this->bag[$attribute]);
        $this->emit("modified");
    }

}