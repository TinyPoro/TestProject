<?php
/**
 * Created by PhpStorm.
 * User: TinyPoro
 * Date: 8/16/18
 * Time: 3:43 PM
 */

namespace App\Poro_Crawler\Entity;


class Node
{
    /**
     * @var mixed
     */
    private $value;

    /**
     * @var mixed
     */
    private $tag;

    /**
     * @var mixed
     */
    private $level;

    /**
     * parent
     *
     * @var Node
     * @access private
     */
    public $parent = null;

    /**
     * @var Node[]
     */
    public $children = [];

    /**
     * @param mixed $data
     * @param mixed $value
     * @param Node[] $children
     */
    public function __construct($value = null, $data = 'root', array $children = [])
    {
        $this->handleData($data);
        $this->setValue($value);

        if (!empty($children)) {
            $this->setChildren($children);
        }
    }

    public function handleData($data){
        if($data == 'ul') {
            $this->setTag('ul');
            $this->setLevel(7);
        }else if($data[0] == 'h'){
            $this->setTag('h');
            $this->setLevel($data[1]);
        }else{
            $this->setTag('root');
            $this->setLevel(0);
        }
    }

    public function isRoot(){
        if($this->tag == 'root') return true;
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($value)
    {
        if(is_null($value)) $this->value = $value;
        else {
            $clone = clone $value;
            $this->value = $clone;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue()
    {
        return $this->value;
    }

    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    public function getTag()
    {
        return $this->tag;
    }

    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    public function getLevel()
    {
        return $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public function addChild(Node $child)
    {
        $child->setParent($this);
        $this->children[] = $child;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeChild(Node $child)
    {
        foreach ($this->children as $key => $myChild) {
            if ($child === $myChild) {
                unset($this->children[$key]);
            }
        }

        $this->children = array_values($this->children);

        $child->setParent(null);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAllChildren()
    {
        $this->setChildren([]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * {@inheritdoc}
     */
    public function setChildren(array $children)
    {
        $this->removeParentFromChildren();
        $this->children = [];

        foreach ($children as $child) {
            $this->addChild($child);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setParent(Node $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->parent;
    }

    private function removeParentFromChildren()
    {
        foreach ($this->getChildren() as $child)
            $child->setParent(null);
    }

    public function getTagName(){
        if($this->isRoot()) return 'root';

        return $this->getValue()->tag_name();
    }

    public function getText(){
        if($this->isRoot()) return '';

        return $this->getValue()->text();
    }
}