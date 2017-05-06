# pho-lib-graph [![Build Status](https://travis-ci.org/phonetworks/pho-lib-graph.svg?branch=master)](https://travis-ci.org/phonetworks/pho-lib-graph) [![Code Climate](https://img.shields.io/codeclimate/github/phonetworks/pho-lib-graph.svg)](https://codeclimate.com/github/phonetworks/pho-lib-cli)

A general purpose [graph](http://en.wikipedia.org/wiki/Graph_theory) library written in PHP (5.3+)

![Graph](https://github.com/phonetworks/pho-lib-graph/raw/master/.github/socialgraph.gif "A Social Graph example")

## Install

The recommended way to install pho-lib-graph is [through composer](https://getcomposer.org/).

```bash
composer require phonetworks/pho-lib-graph
```

## Example

(sample graph image)

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
$brad_fitzpatrick = new Node($google); // google
$ray_kurzweil = new Node($google); // google
```

So far, we have six nodes, a single subgraph and a single graph. The graph ($world) implements GraphInterface, the nodes (employees) implement NodeInterface and the only subgraph we have created (which is $google) does both. 

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
$brad_fitzpatrick->attributes()->position = "staff software engineer";
$ray_kurzweil->attributes()->position = "futurist";
```



Last but not least, a graph is formed by, not only nodes, but also edges. So let's give it a try:

## Architecture

## License

MIT, see [LICENSE](https://github.com/phonetworks/pho-lib-graph/blob/master/LICENSE).

