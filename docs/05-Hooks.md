# Hooks

Hooks allow developers to intercept certain functions that may benefit from hydration at higher-levels. Hydration takes place with persistent objects which, once deserialized, may lose some of their object associations. 

To illustrate, when you persist a node object, its EdgeList object may turn into an array rather than a full-blown object, which would be hard and expensive to store. Then, in order to retrieve the edge list, you can use the IDs and tap into your database in separate calls, which would enhance the performance of your app. Lib-Graph's hooks come into play in such scenarios, because you can intercept these getter methods and inject value by leveraging the information stored in your database.

You can use hooks as follows:

```php
$node->hook("get", function($id) use ($existing_node) {
   return $existing_node;
});
```

where 

1. The first argument is the hook key, in string format.
2. The second argument is a PHP closure (you can pass it as a variable too).

Below you can see a full list of entities that support hooks and their keys.

#### Graph and SubGraph:

* **get(ID $node_id)**: called when ```get(ID $node_id)``` can't find the object in ```$nodes```. Enables you to access ```$node_ids``` to fetch the object from external sources. The return value is **NodeInterface**.
* **members**: called when ```members()``` can't find any objects in ```$nodes```. Enables you to access ```$node_ids``` to fetch the objects from external sources. The return value is **array** (of NodeInterface objects).

#### Edge:

* **head**: called when ```head()``` can't find the head node. Enables you to access ```$head_id``` to fetch it from external sources. The return value is **NodeInterface**.
* **tail**: called when ```tail()``` can't find the tail node. Enables you to access ```$tail_id``` to fetch it from external sources. The return value is **NodeInterface**.
* **predicate**: called when ```predicate()``` can't find the predicate object. Enables you to access ```$predicate_label``` to recreate it or fetch from external sources. The return value is **PredicateInterface**.

#### Node and SubGraph:

* **context**: called when ```context()``` can't find the context. Enables you to access ```$context_id``` to fetch it from external sources. The return value is **GraphInterface**.
* **edge(string $edge_id)**: called to retrieve an edge object from external sources. The return value must be an **EdgeInterface**
