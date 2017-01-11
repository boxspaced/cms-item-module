<?php
namespace Boxspaced\CmsItemModule\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Collection\Collection;

class ItemPart extends AbstractEntity
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
     * @return ItemPart
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return int
     */
    public function getOrderBy()
    {
        return $this->get('order_by');
    }

    /**
     * @param int $orderBy
     * @return ItemPart
     */
    public function setOrderBy($orderBy)
    {
        $this->set('order_by', $orderBy);
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
     * @return ItemPart
     */
    public function setParentItem(Item $parentItem)
    {
        $this->set('parent_item', $parentItem);
		return $this;
    }

    /**
     * @return Collection
     */
    public function getFields()
    {
        return $this->get('fields');
    }

    /**
     * @param ItemPartField $field
     * @return ItemPart
     */
    public function addField(ItemPartField $field)
    {
        $field->setParentPart($this);
        $this->getFields()->add($field);
		return $this;
    }

    /**
     * @param ItemPartField $field
     * @return ItemPart
     */
    public function deleteField(ItemPartField $field)
    {
        $this->getFields()->delete($field);
		return $this;
    }

    /**
     * @return ItemPart
     */
    public function deleteAllFields()
    {
        foreach ($this->getFields() as $field) {
            $this->deleteField($field);
        }
		return $this;
    }

}
