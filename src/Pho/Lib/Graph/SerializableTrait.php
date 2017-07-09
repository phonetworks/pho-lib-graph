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
 * This trait is used to add demonstrational serialization functionality
 * to package elements.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
trait SerializableTrait
{

    /**
     * @internal
     *
     * Used for serialization. Nothing special here. Declared for 
     * subclasses.
     *
     * @return string in PHP serialized format.
     */
    public function serialize(): string 
    {
        return serialize(get_object_vars($this));
    }

    /**
     * @internal
     *
     * Used for deserialization. Nothing special here. Declared for 
     * subclasses.
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