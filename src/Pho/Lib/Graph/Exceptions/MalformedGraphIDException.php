<?php

namespace Pho\Lib\Graph\Exceptions;

use Pho\Lib\Graph;

/**
 * Thrown when the given ID is not a valid UUIDv4.
 * 
 * @see https://en.wikipedia.org/wiki/Universally_unique_identifier For more information about the UUID format.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class MalformedGraphIDException extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $id The maleformed ID in string format
     */
    public function __construct(string $id) 
    {
        parent::__construct();
        $this->message = sprintf("The ID \"%s\" is malformed and does not comply with the UUID format.", $id);
    }    
}