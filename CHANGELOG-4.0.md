## CHANGELOG for 4.0.x

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
* Duplicate trait functions now use the "__" prefix as recommended by https://github.com/phonetworks/commons-php
* New "type()" function in EntityTrait
* Signals emitted: "modified" in Graph, Edge, and Node.
* ClusterTrait renamed as GraphTrait
* onAdd() onRemoved() removed from GraphTrait since all event-related functions will soon be handled by "on()" of Sabre.
* In GraphTrait, clusterToArray() renamed as graphToArray()
* In Node.php populateGraphObservers() renamed as attachGraphObservers()
* Added new unit tests.

