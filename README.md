# pho-lib-graph [![Build Status](https://travis-ci.org/phonetworks/pho-lib-graph.svg?branch=master)](https://travis-ci.org/phonetworks/pho-lib-graph) [![Code Climate](https://img.shields.io/codeclimate/github/phonetworks/pho-lib-graph.svg)](https://codeclimate.com/github/phonetworks/pho-lib-cli)

A general purpose [graph](http://en.wikipedia.org/wiki/Graph_theory) library written in PHP (5.3+)

![Graph](https://github.com/phonetworks/pho-lib-graph/raw/master/.github/socialgraph.gif "A Social Graph example")

## Getting Started

The recommended way to install pho-lib-graph is [through composer](https://getcomposer.org/).

```bash
composer require phonetworks/pho-lib-graph
```

Once you install, you can play with the library using the example application provided in the ```playground``` folder, named [bootstrap.php](https://github.com/phonetworks/pho-lib-graph/blob/master/playground/bootstrap.php)

## Architecture

A graph consists of edges and nodes. In Pho architecture, the core components edges and nodes are organized as subclasses of [Entity](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/EntityInterface.php) for the common themes they share (such as an identifier, label etc). [Graph](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/GraphInterface.php) is positioned completely different, and [SubGraph](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/SubGraph.php) stands uniquely as a subclass of [Node](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/NodeInterface.php) that also shows [Graph traits](https://github.com/phonetworks/pho-lib-graph/blob/master/src/Pho/Lib/Graph/ClusterTrait.php) at the same time.

![Architecture](https://github.com/phonetworks/pho-lib-graph/raw/master/.github/lib-graph-components.png "Pho LibGraph Architecture")

## Getting Started

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

So far, we have six nodes, a single subgraph and a single graph. The graph ($world) implements GraphInterface, the nodes (employees) implement NodeInterface and the only subgraph we have created (which is $google) does both. 

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


## Reference



### GraphInterface

* add($node)
* contains($node_id)
* get($node_id)
* remove($node)
* members()
* toArray()

```php
$world->add($)
```

## EntityInterface
* id()
* label()
* isA()
* attributes()
* destroy()
* toArray()

## NodeInterface
* edges()
* context()

## EdgeList
* add()
* addIncoming()
* addOutgoing()
* in()
* out()
* all()
* to()

### Attributes

The attributes() function gives access to a getter/setter, which is actually an instance of the AttributeBag class, and that you can use with any variable you'd like. Once you make an update to the AttributeBag instance (e.g set a new value, update an existing one or delete), it is passed to the node object via observeAttributeBagUpdate($subject) function where $subject is the AttributeBag itself in its current state.


```php
$mark_zuckerberg->attributes()->position = "ceo";
$larry_page->attributes()->position = "ceo";
$vincent_cerf->attributes()->position = "chief evangelist";
$yann_lecun->attributes()->position = "director of ai research";
$ray_kurzweil->attributes()->position = "chief futurist";
```



Last but not least, a graph is formed by, not only nodes, but also edges. So let's give it a try:


## License

MIT, see [LICENSE](https://github.com/phonetworks/pho-lib-graph/blob/master/LICENSE).

