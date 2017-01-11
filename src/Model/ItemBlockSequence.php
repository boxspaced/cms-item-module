<?php
namespace Boxspaced\CmsItemModule\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\EntityManager\Collection\Collection;

class ItemBlockSequence extends AbstractEntity
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
     * @return ItemBlockSequence
     */
    public function setId($id)
    {
        $this->set('id', $id);
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
     * @return ItemBlockSequence
     */
    public function setParentItem(Item $parentItem)
    {
        $this->set('parent_item', $parentItem);
		return $this;
    }

    /**
     * @return ItemTemplateBlock
     */
    public function getTemplateBlock()
    {
        return $this->get('template_block');
    }

    /**
     * @param ItemTemplateBlock $templateBlock
     * @return ItemBlockSequence
     */
    public function setTemplateBlock(ItemTemplateBlock $templateBlock)
    {
        $this->set('template_block', $templateBlock);
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
     * @param ItemBlockSequenceBlock $block
     * @return ItemBlockSequence
     */
    public function addBlock(ItemBlockSequenceBlock $block)
    {
        $block->setParentBlockSequence($this);
        $this->getBlocks()->add($block);
		return $this;
    }

    /**
     * @param ItemBlockSequenceBlock $block
     * @return ItemBlockSequence
     */
    public function deleteBlock(ItemBlockSequenceBlock $block)
    {
        $this->getBlocks()->delete($block);
		return $this;
    }

    /**
     * @return ItemBlockSequence
     */
    public function deleteAllBlocks()
    {
        foreach ($this->getBlocks() as $block)
        {
            $this->deleteBlock($block);
        }
		return $this;
    }

}
