<?php

namespace Pho\Lib\Graph;

class EncapsulatedEdge implements \Serializable {

    private $object;
    private $classes = [];
    private $id;

    private function __construct(EdgeInterface $edge) 
    {
        $this->id = $edge->id();
        $this->object = $edge;
        $this->classes = $this->findClasses(get_class($edge));
    }

    public function id(): ID
    {
        return $this->id;
    }

    public function classes(): array
    {
        return $this->classes;
    }

    private function findClasses(string $class): array
    {
        for ($classes[] = $class; $class = get_parent_class ($class); $classes[] = $class); 
            return $classes;
    }
    
    public static function create(EdgeInterface $edge): EncapsulatedEdge
    {
        return new EncapsulatedEdge($edge);
    }

    private function deobject(): array
    {
        return array(
            "id" => $this->id,
            "classes" => $this->classes
        );
    }

    public function hydrated(): bool
    {
        return (isset($this->object) && $this->object instanceof EdgeInterface);
    }

    public function edge(): EdgeInterface
    {
        return $this->object;
    }

    /**
    * @internal
    *
    * Used for serialization. 
    *
    * @return string in PHP serialized format.
    */
   public function serialize(): string 
   {
        return serialize($this->deobject());
    }

    /**
    * @internal
    *
    * Used for deserialization. 
    *
    * @param string $data 
    *
    * @return void
    */
    public function unserialize(/* mixed */ $data): void 
    {
        $values = unserialize($data);
        foreach ($values as $key=>$value) {
            $this->$key = $value;
        }
    }

}