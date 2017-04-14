<?php

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
 * Cluster is an important trait of graphs.
 * 
 * @see Graph For full implementation.
 * @see SubGraph For partial implementation.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
trait ClusterTrait {

    /**
     * Holds nodes in ID => NodeInterface format
     *
     * @var array
     */
    private $nodes = [];

    /**
     * {@inheritdoc}
     */
    public function add(NodeInterface $node): NodeInterface
    {
        $this->nodes[(string) $node->id()] = $node;
        if($this instanceof SubGraph) {
            $this->context()->add($node);
        }
        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function get(ID $node_id): NodeInterface 
    {
        if(!$this->contains($node_id))
            throw new Exceptions\NodeDoesNotExistException($node_id);
        return $this->nodes[(string) $node_id];
    }

    /**
     * {@inheritdoc}
     */
    public function contains(ID $node_id): bool
    {
        return isset($this->nodes[(string) $node_id]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function remove(ID $node_id): void
    {
        if($this->contains($node_id)) {
            $this->get($node_id)->destroy();
            unset($this->nodes[(string) $node_id]);
        }
    }

    /**
     * {@inheritdoc}
     */        
    public function members(): array
    {
        return $this->nodes;
    }

}