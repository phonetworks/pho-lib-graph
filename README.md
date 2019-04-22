<p align="center">
  <img width="375" height="150" src="https://github.com/phonetworks/commons-php/raw/master/.github/cover-smaller.png">
</p>

# pho-lib-graph [![Build Status](https://travis-ci.org/phonetworks/pho-lib-graph.svg?branch=master)](https://travis-ci.org/phonetworks/pho-lib-graph) [![Code Climate](https://img.shields.io/codeclimate/github/phonetworks/pho-lib-graph.svg)](https://codeclimate.com/github/phonetworks/pho-lib-graph)

A general purpose [graph](http://en.wikipedia.org/wiki/Graph_theory) library written in PHP 7.1+

## Getting Started

The recommended way to install pho-lib-graph is [through composer](https://getcomposer.org/).

```bash
composer require phonetworks/pho-lib-graph
```

Once you install, you can play with the library using the example application provided in the ```playground``` folder, named [bootstrap.php](https://github.com/phonetworks/pho-lib-graph/blob/master/playground/bootstrap.php)

## Documentation

For more infomation on the internals of pho-lib-graph, as well as a simple user guide, please refer to the [docs/](https://github.com/phonetworks/pho-lib-graph/tree/master/docs) folder. You may also generate the APIs using phpdoc as described in [CONTRIBUTING.md](https://github.com/phonetworks/pho-lib-graph/blob/master/CONTRIBUTING.md)

## FAQ

**1. What is the difference between an edge and a predicate?**
Predicate determines the characteristics of an edge. All edges must have a predicate, albeit defining the predicate explicitly is optional. If predicate is not defined, edges will be formed with a generic predicate. 

**2. What is a binding predicate?**
If a predicate is binding, should the edge's tail node is deleted, not only the edge itself gets stripped off, but the head node must be removed as well.

**3. What is an orphan edge?**
An edge that does not have its head node (this edgelists for neither tail node nor head node are formed) is called an orphan edge. These are incomplete structures and programmers are not advised to use them. You can connect an orphan edge to its head with the ```connect(NodeInterface $node)``` method.

**4. What is a multiplicable predicate?**
An edge with multiplicable predicate may be created multiple times between a particular pair of head and tail nodes.


## License

MIT, see [LICENSE](https://github.com/phonetworks/pho-lib-graph/blob/master/LICENSE).

