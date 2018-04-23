# CHANGELOG

This changelog references the relevant changes (bug and security fixes) introduced with version 4.0 and beyond.

To get the diff for a specific change, go to https://github.com/phonetworks/pho-lib-graph/commit/XXX where XXX is the change hash.

## 3.6 to 4.0

To get the diff between two versions, go to https://github.com/phonetworks/pho-lib-graph/compare/v4.0.0...v3.6.1

* Added "sabre/event" 5.0 as a composer dependency in composer.json.
* Eventful classes: Edge, Node, Graph
* In adherence to PSR-2 standards, changed interface naming into a multiline format.
* inDestruction is now named inDeletion (changed internal naming accordingly) in Node.php
* Introduced SplSubjectTrait to amass common \SplSubject functions and variables. 
* SplSubjectTrait in use by AttributeBag,
* hydrated methods renamed with the new prefix; "hy"
* Duplicate trait functions now use the "\_\_" prefix as recommended by https://github.com/phonetworks/commons-php
* New "type()" function in EntityTrait
* Signals emitted: "modified" in Graph, Edge, and Node.
* ClusterTrait renamed as GraphTrait
* onAdd() onRemoved() removed from GraphTrait since all event-related functions will soon be handled by "on()" of Sabre.
* In GraphTrait, clusterToArray() renamed as graphToArray()
* In Node.php populateGraphObservers() renamed as attachGraphObservers()
* Added new unit tests.

## 4.0 to 4.1
* Updated travis.yml to work with PHP 7.1
* Introduced worker interfaces (NodeWorkerInterface and EntityWorkerInterface) to improve the readability of AdjacentNode.php

## 4.1 to 5.0
* Introduction of "hooks" -- to replace hy\* (hydrating) functions.
* Making of the new docs/ folder and refactoring of the README.md

## 5.0 to 5.1
* AbstractEdge Helper method renamed (\_resolvePredicate is now resolvePredicate)

## 5.1 to 5.2
* Major code cleanup in EdgeList

## 5.2 to 5.3
* AttributeBag quietSet method.

## 5.3 to 5.4
* multiplicable predicates

## 5.4 to 6.0
* New ID format, replacing UUIDv4 with a similar format with more entropy (15 bytes) and an entity-type definition header of 1 byte.

## 6.0 to 6.1
* MalformedGraphIDException renamed as MalformedIDException

## 6.1 to 6.2
* ID randomness changed from 15 bytes to 15 bytes and 8 bits.

## 6.2 to 6.3
* toArray now returns label as well

## 6.3 to 6.4
* added ```delete``` method to EdgeList.

## 6.4 to 6.5
* subgraphs are encompassing. See b0d5ba83faaab1da1a12827c07206e9a8a4013fd

## 6.5 to 7.0
* removed observer pattern in favor of signals

## 7.0 to 7.1
* toArray returns event listeners too