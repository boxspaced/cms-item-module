<?php
namespace Boxspaced\CmsItemModule\Service;

use Boxspaced\CmsCoreModule\Model\ProvisionalLocation as ProvisionalLocationEntity;

class ProvisionalLocation
{

    /**
     *
     * @var string
     */
    public $to;

    /**
     *
     * @var int
     */
    public $beneathMenuItemId;

    /**
     * @param ProvisionalLocationEntity $entity
     * @return ProvisionalLocation
     */
    public static function createFromEntity(ProvisionalLocationEntity $entity)
    {
        $provisionalLocation = new static();

        $provisionalLocation->to = $entity->getTo();
        $provisionalLocation->beneathMenuItemId = (int) $entity->getBeneathMenuItemId();

        return $provisionalLocation;
    }

}
