<?php
namespace Item\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;

class ItemTemplateBlock extends AbstractEntity
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
     * @return ItemTemplateBlock
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return ItemTemplate
     */
    public function getParentTemplate()
    {
        return $this->get('parentTemplate');
    }

    /**
     * @param ItemTemplate $parentTemplate
     * @return ItemTemplateBlock
     */
    public function setParentTemplate(ItemTemplate $parentTemplate)
    {
        $this->set('parentTemplate', $parentTemplate);
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
     * @return ItemTemplateBlock
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return string
     */
    public function getAdminLabel()
    {
        return $this->get('adminLabel');
    }

    /**
     * @param string $adminLabel
     * @return ItemTemplateBlock
     */
    public function setAdminLabel($adminLabel)
    {
        $this->set('adminLabel', $adminLabel);
		return $this;
    }

    /**
     * @return bool
     */
    public function getSequence()
    {
        return $this->get('sequence');
    }

    /**
     * @param bool $sequence
     * @return ItemTemplateBlock
     */
    public function setSequence($sequence)
    {
        $this->set('sequence', $sequence);
		return $this;
    }

}
