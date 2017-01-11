<?php
namespace Boxspaced\CmsItemModule\Service;

use Boxspaced\CmsItemModule\Model\Item as ItemEntity;

class ItemMeta
{

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var int
     */
    public $typeId;

    /**
     *
     * @var string
     */
    public $typeName;

    /**
     *
     * @var string
     */
    public $typeIcon;

    /**
     *
     * @var bool
     */
    public $multipleParts;

    /**
     *
     * @var ProvisionalLocation
     */
    public $provisionalLocation;

    /**
     *
     * @var int
     */
    public $authorId;

    /**
     *
     * @var ItemNote[]
     */
    public $notes = [];

    /**
     * @param ItemEntity $entity
     * @return ItemMeta
     */
    public static function createFromEntity(ItemEntity $entity)
    {
        $meta = new static();

        if ($entity->getVersionOf()) {
            $meta->name = $entity->getVersionOf()->getRoute()->getSlug();
        } else {
            $meta->name = $entity->getRoute()->getSlug();
        }

        $meta->typeId = $entity->getType()->getId();
        $meta->typeName = $entity->getType()->getName();
        $meta->typeIcon = $entity->getType()->getIcon();
        $meta->multipleParts = $entity->getType()->getMultipleParts();
        $meta->authorId = $entity->getAuthor()->getId();

        foreach ($entity->getNotes() as $note) {
            $meta->notes[] = ItemNote::createFromEntity($note);
        }

        return $meta;
    }

}
