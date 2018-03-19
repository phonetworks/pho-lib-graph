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
 * Holds the relationship between nodes and edges.
 * 
 * EdgeList objects are attached to all Node objects, they are
 * created at object initialization. They contain edge objects
 * categorized by their direction. 
 * 
 * @see ImmutableEdgeList For a list that doesn't accept new values.
 * 
 * @author Emre Sokullu <emre@phonetworks.org>
 */
class EdgeList
{

    /**
     * The node thay this edgelist belongs to
     *
     * @var NodeInterface
     */
    private $master;

    /**
     * An internal pointer of outgoing nodes in [ID=>EncapsulatedEdge] format 
     * where ID belongs to the edge.
     *
     * @var array
     */
    private $out = [];

    /**
     * An internal pointer of incoming nodes in [ID=>EncapsulatedEdge] format
     * where ID belongs to the edge
     *
     * @var array
     */
    private $in = [];

    /**
     * An internal pointer of incoming nodes in [ID=>[ID=>EncapsulatedEdge]] format
     * where first ID belongs to the node, and second to the edge.
     *
     * @var array
     */
    private $from = [];

    /**
     * An internal pointer of outgoing nodes in [ID=>[ID=>EncapsulatedEdge]] format
     * where first ID belongs to the node, and second to the edge.
     *
     * @var array
     */
    private $to = [];

    /**
     * Constructor
     * 
     * For performance reasons, the constructor doesn't load the seed data 
     * (if available) but waits for a method to attempt to access.
     * 
     * @param NodeInterface $node The master, the owner of this EdgeList object.
     * @param array         $data Initial data to seed.
     */
    public function __construct(NodeInterface $node, array $data = [])
    {
        $this->master = $node;
        $this->import($data);
    }

    public function delete(ID $id): void
    {
        $id = (string) $id;
        foreach($this->from as $key=>$val) {
            unset($this->from[$key][$id]);
        }
        foreach($this->to as $key=>$val) {
            unset($this->to[$key][$id]);
        }
        unset($this->in[$id]);
        unset($this->out[$id]);
        $this->master->emit("modified");
    }

    /**
     * Imports data from given array source
     *
     * @param array $data Data source.
     * 
     * @return void
     */
    public function import(array $data): void
    {
        if(!$this->isDataSetProperly($data)) {
            return;
        }

        $wakeup = function (array $array): EncapsulatedEdge {
            return EncapsulatedEdge::fromArray($array);
        };

        $this->out = array_map($wakeup, $data["out"]); 
        $this->in = array_map($wakeup, $data["in"]); 
        foreach($data["from"] as $from => $frozen) {
            $this->from[$from] = array_map($wakeup, $frozen); 
        }
        foreach($data["to"] as $to => $frozen) {
            $this->to[$to] = array_map($wakeup, $frozen); 
        }
    }

    /**
     * Checks if the data source for import is valid.
     *
     * @param array $data
     * 
     * @return bool
     */
    private function isDataSetProperly(array $data): bool
    {
        return (isset($data["in"]) && isset($data["out"]) && isset($data["from"]) && isset($data["to"]));
    }

    /**
     * Retrieves this object in array format
     *
     * With all "in" and "out" values in simple string format.
     * The "to" array can be reconstructed.
     * 
     * @return array
     */
    public function toArray(): array 
    {

        $to_array = function (EncapsulatedEdge $encapsulated): array {
            return $encapsulated->toArray();
        };

        $array = [];

        $array["to"] = [];
        foreach($this->to as $to => $encapsulated) {
            $array["to"][$to] = array_map($to_array, $encapsulated);
        }

        $array["from"] = [];
        foreach($this->from as $from => $encapsulated) {
            $array["from"][$from] = array_map($to_array, $encapsulated);
        }

        $array["in"] = array_map($to_array, $this->in);
        $array["out"] = array_map($to_array, $this->out);

        return $array;
    }

    /**
     * Adds an incoming edge to the list.
     * 
     * The edge must be already initialized.
     *
     * @param EdgeInterface $edge
     * 
     * @return void
     */
    public function addIncoming(EdgeInterface $edge): void
    {
            $edge_encapsulated = EncapsulatedEdge::fromEdge($edge);
            $this->from[(string) $edge->tail()->id()][(string) $edge->id()] = $edge_encapsulated;
            $this->in[(string) $edge->id()] = $edge_encapsulated;
            $this->master->emit("modified");
    }

    /**
     * Adds an outgoing edge to the list.
     * 
     * The edge must be already initialized.
     *
     * @param EdgeInterface $edge
     * 
     * @return void
     */
    public function addOutgoing(EdgeInterface $edge): void
    {
            $edge_encapsulated = EncapsulatedEdge::fromEdge($edge);
            $this->to[(string) $edge->head()->id()][(string) $edge->id()] = $edge_encapsulated;
            $this->out[(string) $edge->id()] = $edge_encapsulated;
            $this->master->emit("modified");
    }


    /**
     * Returns a list of all the edges directed towards
     * this particular node.
     *
     * @see retrieve Used by this method to fetch objects.
     *
     * @param string $class The type of edge (defined in edge class) to return
     *
     * @return \ArrayIterator An array of EdgeInterface objects.
     */
    public function in(string $class=""): \ArrayIterator 
    {
        return $this->retrieve(Direction::in(), $class);
    }

    /**
     * Returns a list of all the edges originating from
     * this particular node.
     *
     * @see retrieve Used by this method to fetch objects.
     *
     * @param string $class The type of edge (defined in edge class) to return
     *
     * @return \ArrayIterator An array of EdgeInterface objects.
     */
    public function out(string $class=""): \ArrayIterator 
    {
        return $this->retrieve(Direction::out(), $class);
    }


    /**
     * A helper method to retrieve edges.
     * 
     * @see out A method that uses this function
     * @see in A method that uses this function
     *
     * @param Direction $direction Lets you choose to fetch incoming or outgoing edges.
     * @param string    $class     The type of edge (defined in edge class) to return
     * 
     * @return \ArrayIterator An array of EdgeInterface objects.
     */
    protected function retrieve(Direction $direction, string $class): \ArrayIterator
    {
        $d = (string) $direction;

        $hydrate = function (EncapsulatedEdge $encapsulated): EdgeInterface {
            if(!$encapsulated->hydrated())
                return $this->master->edge($encapsulated->id());
            return $encapsulated->edge();
        };

        $filter_classes = function (EncapsulatedEdge $encapsulated) use ($class): bool {
            return in_array($class, $encapsulated->classes());
        };

        if(empty($class)) {
            return new \ArrayIterator(
                array_map($hydrate, $this->$d)
            );
        }

        return new \ArrayIterator(
            array_map($hydrate, 
                array_filter($this->$d, $filter_classes)
            )
        );
    }

    /**
     * Returns a list of all the edges (both in and out) pertaining to
     * this particular node.
     *
     * @param string $class The type of edge (defined in edge class) to return
     *
     * @return \ArrayIterator An array of EdgeInterface objects.
     */
    public function all(string $class=""): \ArrayIterator
    {
        return new \ArrayIterator(
            array_merge(
                $this->in($class)->getArrayCopy(), 
                $this->out($class)->getArrayCopy()
            )
        );
    }

    /**
     * Retrieves a list of edges from the list's owner node to the given 
     * target node.
     *
     * @param ID $node_id   Target (head) node.
     * @param string        $class The type of edge (defined in edge class) to return
     *
     * @return \ArrayIterator An array of edge objects to. Returns an empty array if there is no such connections.
     */
    public function to(ID $node_id, string $class=""): \ArrayIterator 
    {
        return $this->retrieveDirected(Direction::out(), $node_id, $class);
    }

    /**
     * Retrieves a list of edges to the list's owner node from the given 
     * source node.
     *
     * @param ID $node_id   Source (tail) node.
     * @param string        $class The type of edge (defined in edge class) to return
     *
     * @return \ArrayIterator An array of edge objects from. Returns an empty array if there is no such connections.
     */
    public function from(ID $node_id, string $class=""): \ArrayIterator
    {
        return $this->retrieveDirected(Direction::in(), $node_id, $class);
    }

    /**
     * Retrieves a list of edges between the list's owner node and the given 
     * node.
     *
     * @param ID $node_id      The other node.
     * @param string        $class The type of edge (defined in edge class) to return
     *
     * @return \ArrayIterator An array of edge objects in between. Returns an empty array if there is no such connections.
     */
    public function between(ID $node_id, string $class=""): \ArrayIterator
    {
        return new \ArrayIterator(
            array_merge(
                $this->from($node_id, $class)->getArrayCopy(), 
                $this->to($node_id, $class)->getArrayCopy()
            )
        );
    }

    /**
     * A helper method to retrieve directed edges.
     * 
     * @see from A method that uses this function
     * @see to A method that uses this function
     *
     * @param Direction $direction Lets you choose to fetch incoming or outgoing edges.
     * @param ID        $node_id   Directed towards which node.
     * @param string    $class     The type of edge (defined in edge class) to return.
     * 
     * @return \ArrayIterator An array of EdgeInterface objects.
     */
    protected function retrieveDirected(Direction $direction, ID $node_id, string $class): \ArrayIterator
    {
        $key = $direction->equals(Direction::in()) ? "from" : "to";
        $direction = (string) $direction;

        $hydrate = function (EncapsulatedEdge $encapsulated): EdgeInterface {
            if(!$encapsulated->hydrated())
                return $this->master->edge($encapsulated->id());
            return $encapsulated->edge();
        };

        $filter_classes = function (EncapsulatedEdge $encapsulated) use ($class): bool {
            return in_array($class, $encapsulated->classes());
        };

        if(!isset($this->$key[(string) $node_id])) { 
            return new \ArrayIterator();
        }
        
        if(empty($class)) {
            return new \ArrayIterator(
                array_map($hydrate, $this->$key[(string) $node_id])
            );
        }
        
        return new \ArrayIterator(
            array_map($hydrate, array_filter($this->$key[(string) $node_id], $filter_classes))
        );
    }

}