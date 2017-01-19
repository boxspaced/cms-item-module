<?php
namespace Boxspaced\CmsItemModule\Model;

use DateTime;
use Boxspaced\EntityManager\EntityManager;
use Boxspaced\EntityManager\Collection\Collection;
use Boxspaced\CmsVersioningModule\Model\VersionableInterface;

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

        $query = $this->entityManager->createQuery();
        $query->field('status')->eq(VersionableInterface::STATUS_PUBLISHED);
        $query->field('live_from')->lt($now);
        $query->field('expires_end')->gt($now);

        if (null !== $offset && null !== $showPerPage) {
            $query->paging($offset, $showPerPage);
        }

        return $this->entityManager->findAll(Item::class, $query);
    }

    /**  *
     * @param int $versionOfId
     * @return Collection
     */
    public function getAllVersionOf($versionOfId)
    {
        $query = $this->entityManager->createQuery();
        $query->field('version_of.id')->eq($versionOfId);

        return $this->entityManager->findAll(Item::class, $query);
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
