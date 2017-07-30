<?php

namespace Pho\Lib\Graph\Exceptions;

use Pho\Lib\Graph\NodeInterface;
use Pho\Lib\Graph\Edge;

/**
 * Thrown when the Edge is not orphan and there's an attempt to 
 * reconnect it via the ```connect``` method.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class EdgeAlreadyConnectedException extends \Exception
{
    /**
     * Constructor.
     *
     * @param string $id The maleformed ID in string format
     */
    public function __construct(Edge $edge, NodeInterface $head) 
    {
        parent::__construct();
        $this->message = sprintf("%s is already connected to %s", 
                            (string) $edge->id(),
                            (string) $head->id()
        );
    }    
}