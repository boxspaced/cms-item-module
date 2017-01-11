<?php
namespace Boxspaced\CmsItemModule\Model;

use Boxspaced\CmsWorkflowModule\Model\WorkflowableInterface;

class WorkflowableItem implements WorkflowableInterface
{

    /**
     * @var Item
     */
    protected $item;

    /**
     * @param Item $item
     */
    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    /**
     * @return string
     */
    public function getWorkflowStage()
    {
        return $this->item->getWorkflowStage();
    }

    /**
     * @param string $stage
     * @return WorkflowableInterface
     */
    public function setWorkflowStage($stage)
    {
        $this->item->setWorkflowStage($stage);
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->item->getStatus();
    }

}
