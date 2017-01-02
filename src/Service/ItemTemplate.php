<?php
namespace Item\Service;

use Item\Model\ItemTemplate as ItemTemplateEntity;

class ItemTemplate
{

    /**
     *
     * @var int
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $viewScript;

    /**
     *
     * @var string
     */
    public $description;

    /**
     *
     * @var bool
     */
    public $commentsAvailable;

    /**
     *
     * @var ItemTemplateBlock[]
     */
    public $blocks = [];

    /**
     * @param ItemTemplateEntity $entity
     * @return ItemTemplate
     */
    public static function createFromEntity(ItemTemplateEntity $entity)
    {
        $template = new static();

        $template->id = (int) $entity->getId();
        $template->name = $entity->getName();
        $template->viewScript = $entity->getViewScript();
        $template->description = $entity->getDescription();

        foreach ($entity->getBlocks() as $block) {
            $template->blocks[] = ItemTemplateBlock::createFromEntity($block);
        }

        return $template;
    }

}
