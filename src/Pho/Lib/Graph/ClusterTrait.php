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
        // return isset($this->nodes[(string) $node_id]);
        return array_search((string)$node_id, $this->node_ids) !== false; // 
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
        }
    }

    /**
     * {@inheritdoc}
     */        
    public function members(): array
    {
        return $this->nodes;
    }

   
   /**
    * Returns Graph members in serialized format
    *
    * @return array A list of member IDs in string format
    */
   protected function membersSerialized(): array
   {
       /*$array = [];
       foreach($this->nodes as $node) {
            $array[] = (string) $node->id();
       }
       return $array;*/
       return $this->node_ids;
   }

   /**
     * {@inheritdoc}
     */  
   public function toArray(): array
   {
    return ["members"=>$this->membersSerialized()];
   }

}