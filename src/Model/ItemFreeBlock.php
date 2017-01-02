<?php
namespace Item\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Block\Model\Block;

class ItemFreeBlock extends AbstractEntity
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
     * @return Item
     */
    public function getParentItem()
    {
        return $this->get('parentItem');
    }

    /**
     * @param Item $parentItem
     * @return ItemField
     */
    public function setParentItem(Item $parentItem)
    {
        $this->set('parentItem', $parentItem);
		return $this;
    }

    /**
     * @return ItemTemplateBlock
     */
    public function getTemplateBlock()
    {
        return $this->get('templateBlock');
    }

    /**
     * @param ItemTemplateBlock $templateBlock
     * @return ItemFreeBlock
     */
    public function setTemplateBlock(ItemTemplateBlock $templateBlock)
    {
        $this->set('templateBlock', $templateBlock);
		return $this;
    }

    /**
     * @return Block
     */
    public function getBlock()
    {
        return $this->get('block');
    }

    /**
     * @param Block $block
     * @return ItemFreeBlock
     */
    public function setBlock(Block $block)
    {
        $this->set('block', $block);
		return $this;
    }

}
