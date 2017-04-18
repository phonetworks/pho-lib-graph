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
 * Immutable, cryptographically secure identifier
 * 
 * Pho IDs are immutable and come in the format of cryptographically secure 
 * {@link https://en.wikipedia.org/wiki/Universally_unique_identifier UUIDv4}
 * 
 * Pho IDs are used to define all graph entities, e.g nodes and edges.
 * 
 * Even at scale of billions of nodes and edges, the chances of collision 
 * is identical to zero.
 * 
 * You can generate a new ID with ```$id_object = ID::generate()``` and fetch its 
 * string representation with PHP type-casting; ```(string) $id_object```.
 * 
 * @author  Emre Sokullu <emre@phonetworks.org>
 */
class ID  {
    
    /**
     * UUIDV4
     *
     * @var string
     */
    private $value;

    /**
     * @internal
     * Constructor. 
     * 
     * Can't be accessed from outside. Use ```ID::generate()```
     * to create a new random ID.
     * 
     * @see ID::generate() To form a new ID object.
     *
     * @param string $id
     */
    private function __construct(string $id) {
        $this->value = $id;
    }

    /**
     * Generates a cryptographically secure random UUID(v4) for internal use.
     *
     * @link  https://en.wikipedia.org/wiki/Universally_unique_identifier UUIDv4 format
     *
     * @return  String  random uuid in guid v4 format
     */
    public static function generate(): ID
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return new ID(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)));
    }

    /**
     * Verifies identicality
     *
     * @return bool
     */
    public function equals(ID $id) {
        return ($this->value == (string) $id);
    }

    /**
     * Stringifies the object.
     * 
     * Returns a string representation of the object for portability.
     * Use with PHP 
     * {@link http://php.net/manual/en/language.types.type-juggling.php#language.types.typecasting type-casting}
     * as follows; ```(string) $ID_object```
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->value;
    }

}