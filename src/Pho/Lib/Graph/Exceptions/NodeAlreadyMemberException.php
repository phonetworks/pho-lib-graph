<?php

namespace Pho\Lib\Graph\Exceptions;

use Pho\Lib\Graph;

/**
 * Thrown when the node is already a member of the given graph.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class NodeAlreadyMemberException extends \Exception
{
    /**
     * Constructor.
     *
     * @param Graph\NodeInterface  $node
     * @param Graph\GraphInterface $graph
     */
    public function __construct(Graph\NodeInterface $node, Graph\GraphInterface $graph) 
    {
        parent::__construct();
        $this->message = sprintf("The node \"%s\" is already a member of the graph \"%s\".", (string) $node->id(), (string) $graph->id());
    }    
}