<?php
namespace Boxspaced\CmsItemModule\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;

class ItemField extends AbstractEntity
{

    /**
     * @return int
     */
    public function getId()
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     * @return ItemField
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->get('name');
    }

    /**
     * @param string $name
     * @return ItemField
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->get('value');
    }

    /**
     * @param string $value
     * @return ItemField
     */
    public function setValue($value)
    {
        $this->set('value', $value);
		return $this;
    }

    /**
     * @return Item
     */
    public function getParentItem()
    {
        return $this->get('parent_item');
    }

    /**
     * @param Item $parentItem
     * @return ItemField
     */
    public function setParentItem(Item $parentItem)
    {
        $this->set('parent_item', $parentItem);
		return $this;
    }

}
