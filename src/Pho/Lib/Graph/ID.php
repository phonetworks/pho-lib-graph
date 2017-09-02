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
 * Pho IDs are immutable and come in the format of cryptographically secure 
 * {@link https://en.wikipedia.org/wiki/Universally_unique_identifier UUIDv4}
 * 
 * Pho IDs are used to define all graph entities, e.g nodes and edges.
 * 
 * The only ID that doesn't conform with the UUID format is the Graph ID
 * which is by default set to be a period (.) and it may be called as 
 * ```ID::root()```
 * 
 * Even at scale of billions of nodes and edges, the chances of collision 
 * is identical to zero.
 * 
 * You can generate a new ID with ```$id_object = ID::generate()``` and fetch its 
 * string representation with PHP type-casting; ```(string) $id_object```.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class ID
{
    
    /**
     * UUIDV4
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
     * Generates a cryptographically secure random UUID(v4) for internal use.
     *
     * @link https://en.wikipedia.org/wiki/Universally_unique_identifier UUIDv4 format
     *
     * @return ID  Random uuid in guid v4 format in ID object format.
     */
    public static function generate(EntityInterface $entity): ID
    {
        /*
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return new ID(vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)));
        */
        return new ID(
            sprintf("%s%s", 
                self::header($entity), 
                bin2hex(
                    random_bytes(15)
                )
            )
        );
    }

    protected static function header(EntityInterface $entity): string
    {
        if($entity instanceof Edge)
            return "80";
        elseif($entity instanceof SubGraph)
            return "01";
        // Node //0: graph, 1-43 subgraph, 43-86-128 node (actor, object), 128-256 edge 
        return "2b";
    }

    /**
     * Loads a UUIDv4 with the given string
     * 
     * Checks the validity of the string and throws an exception if it is not valid.
     *
     * @param string $id Must be in UUIDv4 format.
     * 
     * @return void Given UUIDv4 in ID object format.
     * 
     * @throws Exceptions\InvalidGraphIDException thrown when the given ID is not a valid UUIDv4
     */
    public static function fromString(string $id): ID
    {
        //$uuid_format = '/^[0-9A-F]{8}-[0-9A-F]{4}-4[0-9A-F]{3}-[89AB][0-9A-F]{3}-[0-9A-F]{12}$/i';
        $uuid_format = '/^[0-9A-F]{32}$/i';
        if(!preg_match($uuid_format, $id)) {
            throw new Exceptions\MalformedGraphIDException($id);
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
        //return new ID(".");
        // for($i=0;$i<32;$i++) echo 0;
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