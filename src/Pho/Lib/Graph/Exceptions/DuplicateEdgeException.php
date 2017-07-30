<?php

namespace Pho\Lib\Graph\Exceptions;

use Pho\Lib\Graph\NodeInterface;

/**
 * Thrown when the Edge is not multiplicable, and there's an attempt to 
 * create it multiple times.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class DuplicateEdgeException extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $id The maleformed ID in string format
     */
    public function __construct(NodeInterface $tail, NodeInterface $head, string $class_name) 
    {
        parent::__construct();
        $this->message = sprintf("%s is not multiplicable and there's already an edge from %s to %s", 
                            $class_name,
                            (string) $tail->id(),
                            (string) $head->id()
        );
    }    
}