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
 * SubGraph class
 * 
 * Subgraphs are child graphs that show both node and graph characteristics.
 * They have an ID but they don't originate edges.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class SubGraph extends Node implements GraphInterface
{

    use GraphTrait {
        GraphTrait::add as __add;
        GraphTrait::init as graphInit;
    }

    /**
     * {@inheritdoc}
     */
    public function __construct(GraphInterface $context) 
    {
        parent::__construct($context);
        $this->graphInit();
    }  

    /**
     * {@inheritdoc}
     */
    public function add(NodeInterface $node): NodeInterface
    {
        $node = $this->__add($node);
        try {
            $this->context()->add($node);
        } catch(Exceptions\NodeAlreadyMemberException $e) { 
            /* ignore, that's fine */ 
        }
        return $node;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy(): void
    {
        foreach($this->node_ids as $node_id) {
            $this->get(ID::fromString($node_id))->destroy();
        }
        parent::destroy();
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), $this->graphToArray());
    }

}