<?php

namespace Pho\Lib\Graph\Exceptions;

use Pho\Lib\Graph;

/**
 * Thrown when an orphan edge has called for an operation that requires a head node to exist.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class OrphanEdgeException extends \Exception
{
    /**
     * Constructor.
     *
     * @param Graph\EdgeInterface $edge
     */
    public function __construct(Graph\EdgeInterface $edge) {
        parent::__construct();
        $this->message = sprintf("Orphan edge %s has no head node.", (string) $edge->id());
    }    
}