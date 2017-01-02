<?php
namespace Item\Model;

use Boxspaced\EntityManager\EntityManager;
use Boxspaced\EntityManager\Collection\Collection;

class ItemTeaserTemplateRepository
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
     * @return ItemTeaserTemplate
     */
    public function getById($id)
    {
        return $this->entityManager->find(ItemTeaserTemplate::class, $id);
    }

    /**
     * @return Collection
     */
    public function getAll()
    {
        return $this->entityManager->findAll(ItemTeaserTemplate::class);
    }

    /**
     * @param ItemTeaserTemplate $entity
     * @return ItemTeaserTemplateRepository
     */
    public function delete(ItemTeaserTemplate $entity)
    {
        $this->entityManager->delete($entity);
        return $this;
    }

}
