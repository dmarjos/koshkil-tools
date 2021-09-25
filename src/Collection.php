<?php
namespace Koshkil\Tools;

class Collection implements \ArrayAccess, \Countable, \IteratorAggregate {

	private $attributes=array();
	private $items=array();

	public function addItem($item,$key=false) {
		if ($key===false)
			$this->items[]=$item;
		else
			$this->items[$key]=$item;
	}

	public function first() {
		return $this->items[0];
	}

	public function last() {
		return $this->items[count($this->items)-1];
	}

	public function getItem($key) {
		return $this->items[$key];
	}

	public function getKeys() {
		return array_keys($this->items);
	}

	public function __toString() {
		return serialize($this->items);
	}

	public function __get($attribute) {
		return $this->attributes[$attribute];
	}

	public function __set($attribute,$value) {
		$this->attributes[$attribute]=$value;
	}

	public function count() {
		return count($this->items);
	}

	public function slice($offset,$take=false) {
		if ($take===false) {
			$take=$this->count()-$offset;
		} else if ($take<0) {
			$take=$this->count()-($offset-$take);
		}
		$retVal=new TCollection();
		for($idx=0; $idx<$take; $idx++) {
			$retVal->addItem($this[$offset+$idx]);
		}
		return $retVal;
	}
    public function getIterator() {
        return new \ArrayIterator($this->items);
    }

	public function offsetExists ($offset) {
		return isset($this->items[$offset]);
	}

	/**
	 * @param offset
	 */
	public function offsetGet ($offset) {
		if (isset($this->items[$offset]))
			return $this->items[$offset];
		else
			return null;

	}

	/**
	 * @param offset
	 * @param value
	 */
	public function offsetSet ($offset, $value) {
		$this->items[$offset]=$value;
	}

	/**
	 * @param offset
	 */
	public function offsetUnset ($offset) {
		unset($this->items[$offset]);
	}

}
