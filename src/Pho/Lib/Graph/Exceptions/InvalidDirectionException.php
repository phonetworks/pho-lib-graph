<?php

namespace Pho\Lib\Graph\Exceptions;

use Pho\Lib\Graph;

/**
 * Thrown when the given Direction is not valid.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class InvalidDirectionException extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $id The maleformed ID in string format
     */
    public function __construct(string $direction) {
        parent::__construct();
        $this->message = sprintf("%s is not a valid Direction.", $direction);
    }    
}