# Signals

Signals are events emitted by pho-lib-graph on certain critical points:

## Graph
* **node.added**: when a new node is added to the graph.
* **node.removed**: when a node is removed from the graph.
* **modified**: when the graph is modified with a node addition or removal.

## Edge
* **modified**: when the edge is modified either by connecting or by its attribute bag.
* **deleting**: when the edge is being deleted.

## SubGraph
* **node.added**: when a new node is added to the graph.
* **node.removed**: when a node is removed from the graph.
* **modified**: when the subgraph is modified by its attribute bag.
* **edge.created**: when there is a new edge originating from this subgraph. (triggered by tail)
* **edge.connected**: triggered by head, when an orphan edge is connected to them.
* **deleting**: when the subgraph is being deleted.

## Node
* **modified**: when the node is modified by its attribute bag.
* **edge.created**: when there is a new edge originating from this node.
* **edge.connected**: triggered by head, when an orphan edge is connected to them.
* **deleting**: when the node is being deleted.
