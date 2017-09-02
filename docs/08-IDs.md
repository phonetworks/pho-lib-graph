# IDs

Pho IDs are immutable and come in the format of cryptographically secure,
 similarly to [UUIDv4](https://en.wikipedia.org/wiki/Universally_unique_identifier),
 though not the same.
 
 Pho IDs are used to define all graph entities, e.g nodes and edges. It is 16 bytes (128 bits) long similar to UUID, but the first 8 bits are reserved to determine entity type, while the UUID variants are omitted. Hence, Pho ID provides 15 bytes and 8 bits of randomness.
 
 The Graph ID defaults to nil (00000000000000000000000000000000), or 32 chars of 0. It may may be called with ```ID::root()```
 
 Even at scale of billions of nodes and edges, the chances of collision 
 is identical to zero.
  
 You can generate a new ID with ```$id_object = ID::generate($entity)```, 
 where $entity is any Pho entity, and fetch its  string representation with 
 PHP type-casting; ```(string) $id_object```.

 Entity headers will be as follows:
     
* 0: Graph
* 1: Unidentified Node
* 2: SubGraph Node
* 3: Framework\Graph Node
* 4: Actor Node
* 5: Object Node
* 6: Unidentified Edge
* 7: Read Edge
* 8: Write Edge
* 9: Subscribe Edge
* 10: Mention Edge
* 11: Unidentified