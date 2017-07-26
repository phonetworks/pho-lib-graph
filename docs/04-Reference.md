# Reference

Below is an API reference for most of the Pho LibGraph classes:

## GraphInterface

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

## EntityInterface

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

## NodeInterface

NodeInterface extends EntityInterface, and adds two things:
1. A reference to its context (a GraphInterface object) where it was created. So this is either a Graph or a SubGraph.
2. It holds edges accessible via **edges()** call.
3. It holds attributes accessible via **attributes()** call.

| Method        | Param(s)              | Description                                                        | Returns        |
| ------------- | --------------------- | ------------------------------------------------------------------ | -------------- |
| edges         |                       | Retrieves the EdgeList object that interfaces its edges.           | EdgeList       |
| attributes    |                       | Retrieves the AttributeBag object                                  | AttributeBag    |
| context       |                       | Retrieves its context                                              | GraphInterface |
| inDestruction |                       | Reserved to use by observers to understand the state of the node.  | bool           |
<!--| join         | GraphInterface $graph | Adds the node to the given graph                         | void           |-->

## EdgeInterface

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

 ## PredicateInterface
 
 | Method  | Parameter(s) | Description                                              | Returns               |
 | ------- | -------------| -------------------------------------------------------- | --------------------- |
 | binding |              | Whether the edge is binding [\*\*]                       | bool                  |
 | label   |              | The class name, in lower case.                           | string                |
 
 > [\*] Possible values are 0, 1 or 2. In Predicate class constant form. 0: R_DEFAULT, 1: R_REFLECTIVE, 2: R_CONSUMER

 > [\*\*] Which means once it's deleted, the head node will be too.

## EdgeList

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


## AttributeBag

Holds entity (node and edge) attributes. It works similarly to stdObject in the sense, you set a value simply by:

```php
$node->attributes()->key = "value";
```

Standard methods like ```isset()``` and ```unset()``` also works as expected:

```php
$node->attributes()->key = "value";
if(isset($node->attributes()->key)) 
    echo $node->attributes()->key; // prints "value"
unset($node->attributes()->key); // it is unset now.
```

The difference (from stdObject) is that it also notifies its master object with changes, emitting the "modified" signal.

In order to set a value without triggering a "modified" signal, one may use the ```quietSet(string $key, mixed $value): void``` method.
