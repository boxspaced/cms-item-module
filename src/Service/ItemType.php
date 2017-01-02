<?php
namespace Item\Service;

use Item\Model\ItemType as ItemTypeEntity;

class ItemType
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
     * @var ItemTemplate[]
     */
    public $templates = [];

    /**
     *
     * @var ItemTeaserTemplate[]
     */
    public $teaserTemplates = [];

    /**
     * @param ItemTypeEntity $entity
     * @return ItemType
     */
    public static function createFromEntity(ItemTypeEntity $entity)
    {
        $type = new static();

        $type->id = $entity->getId();
        $type->name = $entity->getName();

        foreach ($entity->getTemplates() as $template) {
            $type->templates[] = ItemTemplate::createFromEntity($template);
        }

        foreach ($entity->getTeaserTemplates() as $teaserTemplate) {
            $type->teaserTemplates[] = ItemTeaserTemplate::createFromEntity($teaserTemplate);
        }

        return $type;
    }

}
