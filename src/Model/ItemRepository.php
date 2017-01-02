<?php
namespace Item\Model;

use DateTime;
use Boxspaced\EntityManager\EntityManager;
use Boxspaced\EntityManager\Collection\Collection;
use Versioning\Model\VersionableInterface;

class ItemRepository
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(
        EntityManager $entityManager
    )
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     * @return Item
     */
    public function getById($id)
    {
        return $this->entityManager->find(Item::class, $id);
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        return $this->entityManager->findAll(Item::class);
    }

    /**
     * @param int $offset
     * @param int $showPerPage
     * @return Collection
     */
    public function getAllLive($offset = null, $showPerPage = null)
    {
        $now = new DateTime();

        $conditions = $this->entityManager->createConditions();
        $conditions->field('status')->eq(VersionableInterface::STATUS_PUBLISHED);
        $conditions->field('liveFrom')->lt($now);
        $conditions->field('expiresEnd')->gt($now);

        if (null !== $offset && null !== $showPerPage) {
            $conditions->paging($offset, $showPerPage);
        }

        return $this->entityManager->findAll(Item::class, $conditions);
    }

    /**  *
     * @param int $versionOfId
     * @return Collection
     */
    public function getAllVersionOf($versionOfId)
    {
        $conditions = $this->entityManager->createConditions();
        $conditions->field('versionOf.id')->eq($versionOfId);

        return $this->entityManager->findAll(Item::class, $conditions);
    }

    /**
     * @param Item $entity
     * @return ItemRepository
     */
    public function delete(Item $entity)
    {
        $this->entityManager->delete($entity);
        return $this;
    }

}
