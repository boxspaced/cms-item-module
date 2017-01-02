<?php
namespace Item\Model;

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
        return $this->get('forType');
    }

    /**
     * @param ItemType $forType
     * @return ItemTeaserTemplate
     */
    public function setForType(ItemType $forType)
    {
        $this->set('forType', $forType);
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
        return $this->get('viewScript');
    }

    /**
     * @param string $viewScript
     * @return ItemTeaserTemplate
     */
    public function setViewScript($viewScript)
    {
        $this->set('viewScript', $viewScript);
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
