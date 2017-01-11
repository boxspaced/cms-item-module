<?php
namespace Boxspaced\CmsItemModule\Service;

use Boxspaced\CmsItemModule\Model\Item as ItemEntity;

class Item
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
    public $navText;

    /**
     *
     * @var string
     */
    public $title;

    /**
     *
     * @var string
     */
    public $metaKeywords;

    /**
     *
     * @var string
     */
    public $metaDescription;

    /**
     *
     * @var ItemField[]
     */
    public $fields = [];

    /**
     *
     * @var ItemPart[]
     */
    public $parts = [];

    /**
     * @param ItemEntity $entity
     * @return Item
     */
    public static function createFromEntity(ItemEntity $entity)
    {
        $item = new static();

        $item->id = $entity->getId();
        $item->navText = $entity->getNavText();
        $item->title = $entity->getTitle();
        $item->metaKeywords = $entity->getMetaKeywords();
        $item->metaDescription = $entity->getMetaDescription();

        foreach ($entity->getFields() as $field) {
            $item->fields[] = ItemField::createFromEntity($field);
        }

        foreach ($entity->getParts() as $part) {
            $item->parts[] = ItemPart::createFromEntity($part);
        }

        return $item;
    }

}
