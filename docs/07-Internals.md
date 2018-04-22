# Internals

Pho-Lib-Graph makes heavy use of Observer Design Pattern, which you should note and be aware of while using the library. The library implements PHP's default [\SplObserver](http://php.net/manual/en/class.splobserver.php) and [\SplSubject](http://php.net/manual/en/class.splsubject.php) interfaces as follows;

* All classes implementing **EntityInterface** (e.g. **Node**, **Edge** and **SubGraph**) observe **AttributeBag** so that an update to the attributes are reflected to the entity.

* All classes implementing **GraphInterface** and use **ClusterTrait** (e.g. **Graph**, **SubGraph**) observe objects implementing **NodeInterface** (e.g. **Node** and **SubGraph**) so that a node removal is handled. Please note **SubGraph**, when destroyed, not only notifies its context (and its parents) about deletion but also destroys its members as well.

* **Edge** observes **TailNode** and its children classes so that when the tail is deleted, the edge is also deleted, and if the edge's predicate is _binding_, the head node gets also deleted.

To sum up; the observers can be classified as follows, with observers in rows vertically and subjects in columns horizontally:

|              | AttributeBag<sup>1</sup> | Node & SubGraph<sup>2</sup>  | TailNode<sup>3</sup> \[\*\]
| ------------ | -----------------------  | ---------------------------- | -------------------------
| Edge         | Y                        | N                            | Y
| Graph        | N                        | Y                            | N
| Node         | Y                        | N                            | N
| SubGraph     | Y                        | Y                            | N

1. For changes in attributebag so that they may be persisted.
2. For deletion and notifying the GraphInterface so that members list is updated.
3. For deletion and notifying the Edge so that the edge and possibly head node are also deleted.

\[\*\] Please note TailNode extends Node, hence it will also notify all classes that Node notifies -- although in a separate context, for a different function.


To put it in context, here's how Subject and Observer work:

## AttributeBag (as Subject)

#### \_\_construct:

```
$this->attach($this->owner);
```

#### \_\_set:

```
$this->notify();
```

## EntityTrait (as Observer)

#### update(\\SplSubject $subject)

```
if($subject instanceof AttributeBag) 
  $this->emit("modified");
```
