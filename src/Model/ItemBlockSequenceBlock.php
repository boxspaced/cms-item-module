<?php
namespace Boxspaced\CmsItemModule\Model;

use Boxspaced\EntityManager\Entity\AbstractEntity;
use Boxspaced\CmsBlockModule\Model\Block;

class ItemBlockSequenceBlock extends AbstractEntity
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
     * @return ItemBlockSequenceBlock
     */
    public function setId($id)
    {
        $this->set('id', $id);
        return $this;
    }

    /**
     * @return ItemBlockSequence
     */
    public function getParentBlockSequence()
    {
        return $this->get('parent_block_sequence');
    }

    /**
     * @param ItemBlockSequence $parentBlockSequence
     * @return ItemBlockSequenceBlock
     */
    public function setParentBlockSequence(ItemBlockSequence $parentBlockSequence)
    {
        $this->set('parent_block_sequence', $parentBlockSequence);
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
     * @return ItemBlockSequenceBlock
     */
    public function setBlock(Block $block)
    {
        $this->set('block', $block);
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
     * @return ItemBlockSequenceBlock
     */
    public function setOrderBy($orderBy)
    {
        $this->set('order_by', $orderBy);
		return $this;
    }

}
