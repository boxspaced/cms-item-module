<?php
namespace Item\Model;

use Boxspaced\EntityManager\EntityManager;
use Boxspaced\EntityManager\Collection\Collection;

class ItemTypeRepository
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
     * @return ItemType
     */
    public function getById($id)
    {
        return $this->entityManager->find(ItemType::class, $id);
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        return $this->entityManager->findAll(ItemType::class);
    }

    /**
     * @param ItemType $entity
     * @return ItemTypeRepository
     */
    public function delete(ItemType $entity)
    {
        $this->entityManager->delete($entity);
        return $this;
    }

}
