# pho-lib-graph [![Build Status](https://travis-ci.org/phonetworks/pho-lib-graph.svg?branch=master)](https://travis-ci.org/phonetworks/pho-lib-graph) [![Code Climate](https://img.shields.io/codeclimate/github/phonetworks/pho-lib-graph.svg)](https://codeclimate.com/github/phonetworks/pho-lib-cli)

A general purpose [graph](http://en.wikipedia.org/wiki/Graph_theory) library written in PHP 7+

## Getting Started

The recommended way to install pho-lib-graph is [through composer](https://getcomposer.org/).

```bash
composer require phonetworks/pho-lib-graph
```

Once you install, you can play with the library using the example application provided in the ```playground``` folder, named [bootstrap.php](https://github.com/phonetworks/pho-lib-graph/blob/master/playground/bootstrap.php)

## Architecture

A graph consists of edges and nodes. In Pho architecture, the core components edges and nodes are organized as subclasses of [Entity](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/EntityInterface.php) for the common themes they share (such as an identifier, label etc). [Graph](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/GraphInterface.php) is positioned completely different, and [SubGraph](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/SubGraph.php) stands uniquely as a subclass of [Node](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/NodeInterface.php) that also shows [Graph traits](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/ClusterTrait.php) at the same time.

![Architecture](https://github.com/phonetworks/pho-lib-graph/raw/master/.github/lib-graph-components.png "Pho LibGraph Architecture")

> Pho is written in PHP 7. All files in pho-lib-graph (with the single exception of the EdgeList.php) are 
> in strict mode enabled by default.

## Documentation

Don't forget to autoload:

```php
<?php
require 'vendor/autoload.php';
```

The default namespace is **Pho\Lib\Graph**

With PHP 7.1+ you can use the following notation for our example;

```php
use Pho\Lib\Graph\{Graph, SubGraph, Node};
```

Otherwise, either include the classes above (Graph, SubGraph, Node) individually, or ```use Pho\Lib\Graph;``` and modify the code below accordingly with a ```Graph\``` prefix before the class names.

Let's fire up the graph, add some nodes and subgraphs (which implement both NodeInterface and GraphInterface) and connect them with edges.

Below we have the world as a graph, Google "the company" as a subgraph, and some notable employees from Google and Facebook as nodes.

```php
$world = new Graph();
$google = new SubGraph($world);
$mark_zuckerberg = new Node($world); // facebook
$larry_page = new Node($google); // google
$vincent_cerf = new Node($google); // google
$yann_lecun = new Node($world); // facebook
$ray_kurzweil = new Node($google); // google
```

So far, we have five nodes, a single subgraph and a single graph. The graph ($world) implements GraphInterface, the nodes (employees) implement NodeInterface and the only subgraph we have created (which is $google) does both. 

We can set up their attributes as follows:

```php
$mark_zuckerberg->attributes()->position = "ceo";
$larry_page->attributes()->position = "ceo";
$vincent_cerf->attributes()->position = "chief evangelist";
$yann_lecun->attributes()->position = "director of ai research";
$ray_kurzweil->attributes()->position = "chief futurist";
```

> The **attributes()** function gives access to a getter/setter, which is actually an instance of the [AttributeBag
> (https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/AttributeBag.php) class, and that you can use
> with any variable you'd like. Once you make an update to the AttributeBag instance (e.g set a new value, update an existing
> one or delete), it is passed to the node object via **observeAttributeBagUpdate(\SplSubject $subject)** function where
> $subject is the AttributeBag itself in its current state. You can fetch the latest attributes of the $subject via its
> **toArray()** method.

Each node and edge created is assigned a cryptographically secure unique identifier (in [UUIDv4](https://en.wikipedia.org/wiki/Universally_unique_identifier) format) automatically:

```php
echo $mark_zuckerberg->id(); 
echo $vincent_cerf->id();
```

You create edges by passing by its tail (aka. origin or source) and head (target) nodes as well as a predicate (optional) as parameters.

```php
$ceo = new class extends \Pho\Lib\Graph\PredicateInterface {};
$is_ceo_of = new \Pho\Lib\Graph\Edge($larry_page, $google, $ceo);
echo $is_ceo_of->id();
```

> Edges hold references to their tail and head and implement a predicate that defines their characteristics (e.g. whether it's
> binding, which means, once the edge is deleted, the head nodes will also need to be deleted.) Plus, similarly to nodes, they
> can hold attributes.

You do have the option to skip the head node and just pass a tail node, but it is not advised. An edge with no head node becomes **orphan** and its status can be checked via ```$edge->orphan()```. You can connect an orphan edge to a head node by calling ```$edge->connect($head_node)``` where $edge is an edge that implements **EdgeInterface** and $head_node is a node that implements **NodeInterface**.

It's important to note that when a NodeInterface object (such as Node or SubGraph) is created within a context (in other words, a GraphInterface object), its reference is added to the context and its parent contexts --if available-- automatically. To illustrate from the example below:

```php
foreach($google->members() as $google_employee) echo (string) $google_employee->id(). PHP_EOL;
```

will print three elements (as expected)

while:

```php
print_r($world->toArray());
```

will print six. Five elements (with notable Google and Facebook employees), plus Google the company SubGraph -- even though we did not specify the $world context while setting up the Google employee nodes.

Last but not least, please note all ids in Pho are a Pho\Lib\Graph\ID object. You use ```ID::generate()``` to generate new ID, or use ```ID::fromString($string)``` to enforce one from pure string. You can cast ID objects into string with (string) prefix as shown in examples above. You may also compare two ID objects via ```$node->id()->equals($another_node->id())``` call.


## Reference

Below is an API reference for most of the Pho LibGraph classes:

### GraphInterface

GraphInterface forms the basis of both Graph and SubGraph objects.

| Method               | Parameter(s)            | Description                   | Returns                |
| -------------------- | ----------------------- | ----------------------------- | ---------------------- |
| id                   |                         | Always returns "." as ID obj. | ID                     |
| add [\*]             | NodeInterface $node     | Adds a new node               | void                   |
| count                |                         | Counts the # of member nodes. | int                    |
| contains             | ID $node_id             | Checks if a node is a member  | bool                   |
| get                  | ID $node_id             | Fetches a member              | NodeInterface          |
| remove               | ID $node_id             | Removes a member              | void                   |
| members              |                         | Lists members in  object form | array\<NodeInterface\> |
| toArray              |                         | Lists member ref.s in ID form | array\<ID\>            |
| loadNodesFromArray   | array $nodes            | Array of NodeInteface objects | void                   |
| loadNodesFromIDArray | array $node_ids         | Array of node IDs in string   | void                   |

> [\*] You won't need to use this function since graph adding is handled automatically at object construction.

### EntityInterface

EntityInterface constitutes the basis of both Node and Edge objects. Most important characteristics are:

* Each entity has an auto-generated ID.
* They hold customizable attributes accessible via **attributes()** call.

| Method        | Parameter(s)            | Description                    | Returns              |
| ------------- | ----------------------- | ------------------------------ | -------------------- |
| id            |                         | Retrieves its ID               | ID                   |
| label         |                         | Returns the class name         | string               |
| isA           | string $class_name      | Validates object class         | bool                 |
| attributes    | ID $node_id             | Returns the attributes class   | AttributeBag         |
| *destroy*[\*] |                         | Readies object for destruction | void                 |
| toArray       |                         | Lists member ref.s in ID form  | array                |

> [\*] Just a placeholder. May be extended in higher levels for dealing with persistence et al.

### NodeInterface

NodeInterface extends EntityInterface, and adds two things:
1. A reference to its context (a GraphInterface object) where it was created. So this is either a Graph or a SubGraph.
2. It holds edges accessible via **edges()** call.

| Method        | Param(s)              | Description                                                        | Returns        |
| ------------- | --------------------- | ------------------------------------------------------------------ | -------------- |
| edges         |                       | Retrieves the EdgeList object that interfaces its edges.           | EdgeList       |
| context       |                       | Retrieves its context                                              | GraphInterface |
| inDestruction |                       | Reserved to use by observers to understand the state of the node.  | bool           |
<!--| join         | GraphInterface $graph | Adds the node to the given graph                         | void           |-->

### EdgeInterface

| Method       | Parameter(s)        | Description                                              | Returns            |
| ------------ | ------------------- | -------------------------------------------------------- | ------------------ |
| tail         |                     | Retrieves the tail node of the edge.                     | TailNode [\*]      |
| tailID       |                     | Retrieves the tail node's ID.                            | ID                 |
| head         |                     | Retrieves the head node of the edge.                     | HeadNode [\*]      |
| headID       |                     | Retrieves the head node's ID                             | ID                 |
| predicate    |                     | Retrieves the predicate                                  | PredicateInterface |
| connect      | NodeInterface $head | Connects the edge to a head node.                        | void               |
| orphan       |                     | Checks if the edge fails to possess a tail or a head     | bool               |

> [\*] TailNode and HeadNode objects behave the same way with NodeInterface objects. You can query them all identically.

 ### PredicateInterface
 
 | Method  | Parameter(s) | Description                                              | Returns               |
 | ------- | -------------| -------------------------------------------------------- | --------------------- |
 | binding |              | Whether the edge is binding [\*\*]                       | bool                  |
 | label   |              | The class name, in lower case.                           | string                |
 
 > [\*] Possible values are 0, 1 or 2. In Predicate class constant form. 0: R_DEFAULT, 1: R_REFLECTIVE, 2: R_CONSUMER

 > [\*\*] Which means once it's deleted, the head node will be too.

### EdgeList

EdgeList, accessible via a node's edges() method, enables the developer to manipulate/retrieve a node's edges. A node has two types of edges:

1. Incoming: Edges that are pointed towards this node.
2. Outgoing: Edges that originate from this node.

You add a new edge via **addIncoming(EdgeInterface $edge)** and **addOutgoing(EdgeInterface $edge)** methods but these won't be covered since edge additions are handled automatically and will not be used by most end-users of this library.

You can list edges via:

| Method   | Parameter(s)                  | Description                                               | Returns                          |
| -------- | ----------------------------- | --------------------------------------------------------- | -------------------------------- |
| in       | string $class=""              | Lists incoming edges.                                     | \\ArrayIterator\<EdgeInterface\> |
| out      | string $class=""              | Lists outgoing edges                                      | \\ArrayIterator\<EdgeInterface\> |
| all      | string $class=""              | Lists all edges, both incoming and outgoing.              | \\ArrayIterator\<EdgeInterface\> |
| to       | ID $node_id, string $class="" | Lists edges from this node to the node in question        | \\ArrayIterator\<EdgeInterface\> |
| from     | ID $node_id, string $class="" | Lists edges to this node from the node in question        | \\ArrayIterator\<EdgeInterface\> |
| between  | ID $node_id, string $class="" | Lists edges in between this node and the node in question | \\ArrayIterator\<EdgeInterface\> |


## Extending Lib-Graph

Lib-Graph comes with "protected" methods that you can override to extend the functionality of the library. 

#### For Graph and SubGraph:

* **onAdd(NodeInterface $node)**: called at the end of ```add(NodeInterface $node)``` function. The return value is **void**.
* **onRemove(ID $node_id)**: called at the end of ```remove(ID $node_id)``` function. The return value is **void**.
* **hydratedGet(ID $node_id)**: called when ```get(ID $node_id)``` can't find the object in ```$nodes```. Enables you to access ```$node_ids``` to fetch the object from external sources. The return value is **NodeInterface**.
* **hydratedMembers()**: called when ```members()``` can't find any objects in ```$nodes```. Enables you to access ```$node_ids``` to fetch the objects from external sources. The return value is **array** (of NodeInterface objects).

#### For Edge:

* **hydratedHead()**: called when ```head()``` can't find the head node. Enables you to access ```$head_id``` to fetch it from external sources. The return value is **NodeInterface**.
* **hydratedTail()**: called when ```tail()``` can't find the tail node. Enables you to access ```$tail_id``` to fetch it from external sources. The return value is **NodeInterface**.
* **hydratedPredicate()**: called when ```predicate()``` can't find the predicate object. Enables you to access ```$predicate_label``` to recreate it or fetch from external sources. The return value is **PredicateInterface**.

#### For Node and SubGraph:

* **hydratedContext()**: called when ```context()``` can't find the context. Enables you to access ```$context_id``` to fetch it from external sources. The return value is **GraphInterface**.
* **hydratedEdge(string $edge_id)**: called to retrieve an edge object from external sources. The return value must be an **EdgeInterface**

## Internals

Pho-Lib-Graph makes heavy use of Observer Design Pattern, which you should note and be aware of while using the library. The library implements PHP's default [\SplObserver](http://php.net/manual/en/class.splobserver.php) and [\SplSubject](http://php.net/manual/en/class.splsubject.php) interfaces as follows;

* All classes implementing **EntityInterface** (e.g. **Node**, **Edge** and **SubGraph**) observe **AttributeBag** so that an update to the attributes are reflected to the entity.

* All classes implementing **GraphInterface** and use **ClusterTrait** (e.g. **Graph**, **SubGraph**) observe objects implementing **NodeInterface** (e.g. **Node** and **SubGraph**) so that a node removal is handled. Please note **SubGraph**, when destroyed, not only notifies its context (and its parents) about deletion but also destroys its members as well.

* **Edge** observes **TailNode** and its children classes so that when the tail is deleted, the edge is also deleted, and if the edge's predicate is _binding_, the head node gets also deleted.

To sum up; the observers can be classified as follows, with observers in rows vertically and subjects in columns horizontally:

|              | AttributeBag<sup>1</sup> | Node & SubGraph<sup>2</sup>  | TailNode<sup>3</sup> \[\*\]
| ------------ | -----------------------  | ---------------------------- | -------------------------
| Edge         | Y                        | N                            | Y
| Graph        | N                        | Y                            | N
| Node         | Y                        | N                            | N
| SubGraph     | Y                        | Y                            | N

1. For changes in attributebag so that they may be persisted.
2. For deletion and notifying the GraphInterface so that members list is updated.
3. For deletion and notifying the Edge so that the edge and possibly head node are also deleted.

\[\*\] Please note TailNode extends Node, hence it will also notify all classes that Node notifies -- although in a separate context, for a different function.

## FAQ

**1. What is the difference between an edge and a predicate?**
Predicate determines the characteristics of an edge. All edges must have a predicate, albeit defining the predicate explicitly is optional. If predicate is not defined, edges will be formed with a generic predicate. 

**2. What is a binding predicate?**
If a predicate is binding, should the edge's tail node is deleted, not only the edge itself gets stripped off, but the head node must be removed as well.

**3. What is an orphan edge?**
An edge that does not have its head node (this edgelists for neither tail node nor head node are formed) is called an orphan edge. These are incomplete structures and programmers are not advised to use them. You can connect an orphan edge to its head with the ```connect(NodeInterface $node)``` method.


## License

MIT, see [LICENSE](https://github.com/phonetworks/pho-lib-graph/blob/master/LICENSE).

