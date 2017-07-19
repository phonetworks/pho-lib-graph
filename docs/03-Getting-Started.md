# Getting Started

Make sure the libraries are autoloaded by:

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

> Please note, while PHP versions below 7.1 may work fine with pho-lib-graph, they are not recommended for general use. 
> Also, the higher-level packages of the Pho stack do not support any PHP version less than 7.1.

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

Last but not least, please note all IDs in Pho are Pho\Lib\Graph\ID objects. You can 
* use ```ID::generate()``` to generate new ID, 
* or type ```ID::fromString($string)``` to enforce one from pure string. 

You can cast ID objects into string with (string) prefix as shown in examples above, or using the ```toString()``` method. You may also compare two ID objects via ```$node->id()->equals($another_node->id())``` call.
