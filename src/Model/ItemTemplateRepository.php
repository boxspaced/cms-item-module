<?php
namespace Item\Model;

use Boxspaced\EntityManager\EntityManager;
use Boxspaced\EntityManager\Collection\Collection;

class ItemTemplateRepository
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
     * @return ItemTemplate
     */
    public function getById($id)
    {
        return $this->entityManager->find(ItemTemplate::class, $id);
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        return $this->entityManager->findAll(ItemTemplate::class);
    }

    /**
     * @param ItemTemplate $entity
     * @return ItemTemplateRepository
     */
    public function delete(ItemTemplate $entity)
    {
        $this->entityManager->delete($entity);
        return $this;
    }

}
