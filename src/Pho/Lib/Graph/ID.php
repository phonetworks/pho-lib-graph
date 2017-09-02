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
 * Immutable, cryptographically secure identifier
 * 
 * Pho IDs are immutable and come in the format of cryptographically secure,
 * similarly to 
 * {@link https://en.wikipedia.org/wiki/Universally_unique_identifier UUIDv4},
 * though not the same.
 * 
 * Pho IDs are used to define all graph entities, e.g nodes and edges.
 * It is 16 bytes (128 bits) long similar to UUID, but the first byte is
 * reserved to determine entity type, while the UUID variants are omitted.
 * Hence, Pho ID provides 15 bytes of randomness.
 * 
 * The Graph ID defaults to nil (00000000000000000000000000000000), or 32 chars
 * of 0. It may may be called with ```ID::root()```
 * 
 * Even at scale of billions of nodes and edges, the chances of collision 
 * is identical to zero.
 * 
 * You can generate a new ID with ```$id_object = ID::generate($entity)```, 
 * where $entity is any Pho entity, and fetch its  string representation with 
 * PHP type-casting; ```(string) $id_object```.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class ID
{
    
    /**
     * Pho ID in string.
     *
     * @var string
     */
    protected $value;

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
    private function __construct(string $id) 
    {
        $this->value = $id;
    }

    /**
     * Generates a cryptographically secure random ID for internal use.
     *
     * Pho ID does not conform to UUID standards. It is similar to UUID v4, 
     * however it does not use the same variants at same locations. Instead,
     * the first byte is reserved for entity type, and the remaining 15 is 
     * used for randomness.
     * 
     * @link https://en.wikipedia.org/wiki/Universally_unique_identifier UUIDv4 format
     *
     * @return ID  Random ID in object format.
     */
    public static function generate(EntityInterface $entity): ID
    {
        return new ID(
            sprintf("%x%x%s", 
                self::header($entity), 
                rand(0,15),
                bin2hex(
                    random_bytes(15)
                )
            )
        );
    }

    /**
     * Fetches entity header.
     * 
     * Entity headers will be as follows:
     * 
     * * 0: Graph
     * * 1: Unidentified Node
     * * 2: SubGraph Node
     * * 3: Framework\Graph Node
     * * 4: Actor Node
     * * 5: Object Node
     * * 6: Unidentified Edge
     * * 7: Read Edge
     * * 8: Write Edge
     * * 9: Subscribe Edge
     * * 10: Mention Edge
     * * 11: Unidentified
     * 
     * This method may be overriden by packages at higher levels.
     * The purpose of headers is to enable easy/fast classification
     * of entitities by looking up the first byte of the UUID.
     * 
     * @param EntityInterface $entity
     * 
     * @return int 1-11 depending on entity type
     */
    protected static function header(EntityInterface $entity): int
    {
        if($entity instanceof Node) {
            if($entity instanceof \Pho\Framework\Object)
                return 5;
            elseif($entity instanceof \Pho\Framework\Actor)
                return 4;
            elseif($entity instanceof \Pho\Framework\Graph)
                return 3;
            elseif($entity instanceof SubGraph)
                return 2;
            return 1;
        }
        elseif($entity instanceof Edge) {
            // order is important
            if($entity instanceof \Pho\Framework\ObjectOut\Mention)
                return 10;
            elseif($entity instanceof \Pho\Framework\ActorOut\Write)
                return 8;
            elseif($entity instanceof \Pho\Framework\ActorOut\Subscribe)
                return 9;
            elseif($entity instanceof \Pho\Framework\ActorOut\Read)
                return 7;
            return 6;
        }
        return 11;
    }

    /**
     * Loads a Pho ID with the given string
     * 
     * Checks the validity of the string and throws an exception if it is not valid.
     *
     * @param string $id Must consist of 32 hexadecimal characters.
     * 
     * @return ID The ID in object format
     * 
     * @throws Exceptions\MalformedIDException thrown when the given ID is not a valid UUIDv4
     */
    public static function fromString(string $id): ID
    {
        $uuid_format = '/^[0-9A-F]{32}$/i';
        if(!preg_match($uuid_format, $id)) {
            throw new Exceptions\MalformedIDException($id);
        }
        return new ID($id);
    }

    /**
     * Retrieves the root ID
     * 
     * Root ID is the ID of the Graph. It doesn't conform with regular
     * ID requirements (namely UUID) and it is just a period (.)
     *
     * @return ID
     */
    public static function root(): ID
    {
        return new ID("00000000000000000000000000000000");
    }

    /**
     * Verifies identicality
     *
     * @return bool
     */
    public function equals(ID $id) 
    {
        return ($this->value == (string) $id);
    }

    /**
     * {@internal}
     * 
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
    public function toString(): string
    {
        return $this->value;
    }

}