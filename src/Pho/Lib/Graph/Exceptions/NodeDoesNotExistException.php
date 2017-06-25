<?php

namespace Pho\Lib\Graph\Exceptions;

use Pho\Lib\Graph;

/**
 * Thrown when the given node is not present.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class NodeDoesNotExistException extends \Exception
{
    /**
     * Constructor.
     *
     * @param Graph\ID $node_id
     */
    public function __construct(Graph\ID $node_id) 
    {
        parent::__construct();
        $this->message = sprintf("A node with ID \"%s\" is not present.", (string) $node_id);
    }    
}