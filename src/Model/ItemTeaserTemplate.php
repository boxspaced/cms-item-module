<?php
namespace Boxspaced\CmsItemModule\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;

class ItemTeaserTemplate extends AbstractEntity
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
     * @return ItemTeaserTemplate
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return ItemType
     */
    public function getForType()
    {
        return $this->get('for_type');
    }

    /**
     * @param ItemType $forType
     * @return ItemTeaserTemplate
     */
    public function setForType(ItemType $forType)
    {
        $this->set('for_type', $forType);
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
     * @return ItemTeaserTemplate
     */
    public function setName($name)
    {
        $this->set('name', $name);
		return $this;
    }

    /**
     * @return string
     */
    public function getViewScript()
    {
        return $this->get('view_script');
    }

    /**
     * @param string $viewScript
     * @return ItemTeaserTemplate
     */
    public function setViewScript($viewScript)
    {
        $this->set('view_script', $viewScript);
		return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->get('description');
    }

    /**
     * @param string $description
     * @return ItemTeaserTemplate
     */
    public function setDescription($description)
    {
        $this->set('description', $description);
		return $this;
    }

}
