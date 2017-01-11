<?php
namespace Boxspaced\CmsItemModule\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;

class ItemPartField extends AbstractEntity
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
     * @return ItemPartField
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
     * @return ItemPartField
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
     * @return ItemPartField
     */
    public function setValue($value)
    {
        $this->set('value', $value);
		return $this;
    }

    /**
     * @return ItemPart
     */
    public function getParentPart()
    {
        return $this->get('parent_part');
    }

    /**
     * @param ItemPart $parentPart
     * @return ItemPartField
     */
    public function setParentPart(ItemPart $parentPart)
    {
        $this->set('parent_part', $parentPart);
		return $this;
    }

}
