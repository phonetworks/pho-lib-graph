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
    protected $nodes = [];

    /**
     * Holds node IDs only in string formt
     *
     * @var array
     */
    protected $node_ids = [];

    /**
     * {@inheritdoc}
     */
    public function add(NodeInterface $node): NodeInterface
    {
        $this->node_ids[] = (string) $node->id();
        $this->nodes[(string) $node->id()] = $node;
        if($this instanceof SubGraph) {
            $this->context()->add($node);
        }
        $this->onAdd($node);
        return $node;
    }

    protected function onAdd(NodeInterface $node): void
    {

    }

    /**
     * {@inheritdoc}
     */
    public function get(ID $node_id): NodeInterface 
    {
        if(!$this->contains($node_id))
            throw new Exceptions\NodeDoesNotExistException($node_id);
        if(isset($this->nodes[(string) $node_id]))
            return $this->nodes[(string) $node_id];
        else
            return $this->hydratedGet($node_id);
    }


    
    protected function hydratedGet(ID $node_id): NodeInterface
    {

    }

    /**
     * {@inheritdoc}
     */
    public function contains(ID $node_id): bool
    {
        return array_search((string)$node_id, $this->node_ids) !== false; 
    }
    
    /**
     * {@inheritdoc}
     */
    public function remove(ID $node_id): void
    {
        if($this->contains($node_id)) {
            $this->get($node_id)->destroy();
            unset($this->nodes[(string) $node_id]);
            unset($this->node_ids[array_search((string)$node_id, $this->node_ids)]);
            $this->onRemove($node_id);
        }
    }


    protected function onRemove(ID $node_id): void
    {

    }


    /**
     * {@inheritdoc}
     */        
    public function members(): array
    {
        if(count($this->nodes)>0 || count($this->node_ids) == 0)
            return $this->nodes;
        else
            return $this->hydratedMembers();
    }

    protected function hydratedMembers(): array
    {

    }

   
   /**
    * {@inheritdoc}
    */
   public function toArray(): array
   {
       return $this->clusterToArray();
   }

   /**
    * Converts the object to array
    *
    * Used for serialization/unserialization. Converts internal 
    * object properties into a simple format to help with
    * reconstruction.
    *
    * @see toArray for actual implementation of this method by subclasses.
    *
    * @return array The object in array format.
    */  
   protected function clusterToArray(): array
   {
    return ["members"=>$this->node_ids];
   }

}