# pho-lib-graph [![Build Status](https://travis-ci.org/phonetworks/pho-lib-graph.svg?branch=master)](https://travis-ci.org/phonetworks/pho-lib-graph)

A general purpose [graph](https://en.wikipedia.org/wiki/Graph_theory) library written in PHP (5.3+)

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

```php
use Pho\Lib\Graph;
```

Let's fire up the graph, add some nodes and subgraphs (which implement both NodeInterface and GraphInterface) and connect them with edges.

```php
$hollywood_industry = new Graph\Graph();
$titanic_movie = new Graph\SubGraph($hollywood_industry);
$inception_movie = new Graph\SubGraph($hollywood_industry);
$leo_di_caprio_actor = new Graph\Node(titanic_movie);
```

## License

MIT, see LICENSE.

