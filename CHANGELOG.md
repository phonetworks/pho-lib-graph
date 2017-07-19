## CHANGELOG

This changelog references the relevant changes (bug and security fixes) done in 4.0 minor versions.

To get the diff for a specific change, go to https://github.com/phonetworks/pho-lib-graph/commit/XXX where XXX is the change hash.

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

### 4.0 to 4.1

* Updated travis.yml to work with PHP 7.1
* Introduced worker interfaces (NodeWorkerInterface and EntityWorkerInterface) to improve the readability of AdjacentNode.php

### 4.2 to 5.0

* Introduction of "hooks" -- to replace hy\* (hydrating) functions.
* Making of the new docs/ folder and refactoring of the README.md
