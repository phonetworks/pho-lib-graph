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
        if($this->contains($node->id())) {
            throw new Exceptions\NodeAlreadyMemberException($node, $this);
        }
        $this->node_ids[] = (string) $node->id();
        $this->nodes[(string) $node->id()] = $node;
        if($this instanceof SubGraph) {
            try {
                $this->context()->add($node);
            } catch(Exceptions\NodeAlreadyMemberException $e) { /* ignore, that's fine */ }
        }
        $this->onAdd($node);
        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function loadNodesFromArray(array $nodes): void
    {
        foreach($nodes as $node)
            $this->add($node);
    }

    /**
     * {@inheritdoc}
     */
    public function loadNodesFromIDArray(array $node_ids): void
    {
        $this->node_ids = array_unique(array_merge($this->node_ids, $node_ids));
        if($this instanceof SubGraph) {
            $this->context()->loadNodesFromIDArray($node_ids);
        }
    }

    /**
     * A protected method that enables higher-level packages
     * to extend the functionality of the add() call.
     * 
     * @see add() 
     *
     * @param NodeInterface $node
     * 
     * @return void
     */
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


    /**
     * A protected method that enables higher-level packages
     * to provide persistence for the get() call.
     * 
     * @see get() 
     *
     * @return NodeInterface
     */
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
            unset($this->nodes[(string) $node_id]);
            unset($this->node_ids[array_search((string)$node_id, $this->node_ids)]);
            $this->onRemove($node_id);
        }
    }

    /**
     * A protected method that enables higher-level packages
     * to extend the functionality of the remove() call.
     * 
     * @see remove() 
     *
     * @param ID $node_id
     * 
     * @return void
     */
    protected function onRemove(ID $node_id): void
    {

    }


    /**
     * {@inheritdoc}
     */        
    public function members(): array
    {
        if(count($this->node_ids)<1)
            return [];
        else if(count($this->nodes) == count($this->node_ids))
            return $this->nodes;
        else
            return $this->hydratedMembers();
    }

    /**
     * {@inheritdoc}
     */   
    public function count(): int
    {
        return count($this->node_ids);
    }

    /**
     * A protected method that enables higher-level packages
     * to provide persistence for the members() call.
     * 
     * @see members() 
     *
     * @return array
     */
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

   /**
    * \SplObserver method that observes nodes and crosses them off of the 
    * nodes list when they are destroyed.
    *
    * @param \SplSubject $node
    *
    * @return void
    */
   protected function observeNodeDeletion(\SplSubject $node): void
   {
       $this->remove($node->id());
   }

}