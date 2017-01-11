<?php
namespace Boxspaced\CmsItemModule\Service;

use DateTime;

class PublishingOptions
{

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $colourScheme;

    /**
     *
     * @var DateTime
     */
    public $liveFrom;

    /**
     *
     * @var DateTime
     */
    public $expiresEnd;

    /**
     *
     * @var int
     */
    public $teaserTemplateId;

    /**
     *
     * @var int
     */
    public $templateId;

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
     *
     * @var FreeBlock[]
     */
    public $freeBlocks = [];

    /**
     *
     * @var BlockSequence[]
     */
    public $blockSequences = [];

}
