<?php
namespace Item\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Collection\Collection;

class ItemTemplate extends AbstractEntity
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
     * @return ItemTemplate
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
     * @return ItemTemplate
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
     * @return ItemTemplate
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
     * @return ItemTemplate
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
     * @return ItemTemplate
     */
    public function setDescription($description)
    {
        $this->set('description', $description);
		return $this;
    }

    /**
     * @return Collection
     */
    public function getBlocks()
    {
        return $this->get('blocks');
    }

    /**
     * @param ItemTemplateBlock $block
     * @return ItemTemplate
     */
    public function addBlock(ItemTemplateBlock $block)
    {
        $block->setParentTemplate($this);
        $this->getBlocks()->add($block);
		return $this;
    }

    /**
     * @param ItemTemplateBlock $block
     * @return ItemTemplate
     */
    public function deleteBlock(ItemTemplateBlock $block)
    {
        $this->getBlocks()->delete($block);
		return $this;
    }

    /**
     * @return ItemTemplate
     */
    public function deleteAllBlocks()
    {
        foreach ($this->getBlocks() as $block) {
            $this->deleteBlock($block);
        }
		return $this;
    }

}
