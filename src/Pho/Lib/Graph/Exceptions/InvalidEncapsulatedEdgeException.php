<?php

namespace Pho\Lib\Graph\Exceptions;

use Pho\Lib\Graph;

/**
 * Thrown when the data source does not conform with EncapsulatedEdge standards.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class InvalidEncapsulatedEdgeException extends \Exception
{
    /**
     * Constructor.
     *
     * @param array $array The malformed EncapsulatedEdge array.
     */
    public function __construct(array $array) 
    {
        parent::__construct();
        $this->message = sprintf("An EncapsulatedEdge must have valid 'id' and 'classes' keys. 'id' with \Pho\Lib\Graph\ID and 'classes' with an array of string objects that represent the classes that the edge belongs to. Given: %s", print_r($array, true));
    }    
}