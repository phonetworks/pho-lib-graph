# Architecture

A graph consists of edges and nodes.  In Pho, the core components edges and nodes are organized as subclasses of [Entity](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/EntityInterface.php) for the common themes they share (such as an identifier, label etc). [Graph](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/GraphInterface.php) is positioned completely different, and [SubGraph](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/SubGraph.php) stands uniquely as a subclass of [Node](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/NodeInterface.php) that also shows [Graph traits](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/ClusterTrait.php) at the same time.

![Architecture](https://github.com/phonetworks/pho-lib-graph/raw/master/.github/lib-graph-components.png "Pho LibGraph Architecture")

> Graph illustration created with [yEd](https://www.yworks.com/products/yed).

Pho is written in PHP 7. All files in pho-lib-graph (with the single exception of the EdgeList.php) are in strict mode enabled by default.
