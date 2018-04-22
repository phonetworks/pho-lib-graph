<?php declare(strict_types=1);

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
 * The fundamental traits of graphs and subgraphs
 * 
 * @see Graph For full implementation.
 * @see SubGraph For partial implementation.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
trait GraphTrait
{

    use HookableTrait;

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
     * 
     * should be able to change context of  $node if $node's context is 
     */
    public function add(NodeInterface $node, bool $skip_signals = false): NodeInterface
    {
        if($this->contains($node->id())) {
            throw new Exceptions\NodeAlreadyMemberException($node, $this);
        }
        $this->node_ids[] = (string) $node->id();
        $this->nodes[(string) $node->id()] = $node;
        if($node instanceof GraphInterface) {
            foreach($node->members() as $member) {
                try {
                    $this->add($member, true);
                }
                catch(Exceptions\NodeAlreadyMemberException $e) { /* ignore */ }
            }
            $node->on("node.added", function(NodeInterface $subnode) {
                try {
                    $this->add($subnode);
                }
                catch(Exceptions\NodeAlreadyMemberException $e) { /* ignore */ }
            });
            $node->on("node.removed", function(ID $id) {
                $this->remove($id);
            });
        }
        if(!$this instanceof Graph) {
            try {
                $this->context()->add($node);
            }
            catch(Exceptions\NodeAlreadyMemberException $e) { /* ignore */ }
        }
        if(!$skip_signals) {
            if($this->canEmitNodeAddSignals()) // sometimes this is not the preferred way
                $this->emit("node.added", [$node]);
            $this->emit("modified");
        }
        return $node;
    }

    /**
     * {@inheritDoc}
     */
    public function canEmitNodeAddSignals(): bool
    {
        return $this->context()->canEmitNodeAddSignals();
    }

    /**
     * {@inheritdoc}
     */
    public function loadNodesFromArray(array $nodes): void
    {
        foreach($nodes as $node) {
            $this->add($node);
        }
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
     * {@inheritdoc}
     */
    public function get(ID $node_id): NodeInterface 
    {
        if(!$this->contains($node_id)) {
            throw new Exceptions\NodeDoesNotExistException($node_id);
        }
        if(isset($this->nodes[(string) $node_id])) {
            return $this->nodes[(string) $node_id];
        }
        return $this->hookable();
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
            if($this instanceof SubGraph) {
                try {
                    $this->context()->remove($node_id);
                } catch(\Exception $e) { /* ignore, that's fine */ }
            }
            $this->emit("modified");
            $this->emit("node.removed", [$node_id]);
        }
    }


    /**
     * {@inheritdoc}
     */        
    public function members(): array
    {
        if(count($this->node_ids)<1) {
            return [];
        } else if(count($this->nodes) == count($this->node_ids)) {
            return $this->nodes;
        }
        return $this->hookable();
    }

    /**
     * {@inheritdoc}
     */   
    public function count(): int
    {
        return count($this->node_ids);
    }
   
    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return $this->graphToArray();
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
    protected function graphToArray(): array
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
