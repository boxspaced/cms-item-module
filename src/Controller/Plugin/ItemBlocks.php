<?php
namespace Boxspaced\CmsItemModule\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Boxspaced\CmsItemModule\Service;

class ItemBlocks extends AbstractPlugin
{

    /**
     * @param ViewModel $parentViewModel
     * @param Service\PublishingOptions $publishingOptions
     * @return ViewModel
     */
    public function __invoke(
        ViewModel $parentViewModel,
        Service\PublishingOptions $publishingOptions
    )
    {
        return $this->assign($parentViewModel, $publishingOptions);
    }

    /**
     * @param ViewModel $parentViewModel
     * @param Service\PublishingOptions $publishingOptions
     * @return ViewModel
     */
    public function assign(
        ViewModel $parentViewModel,
        Service\PublishingOptions $publishingOptions
    )
    {
        foreach ($publishingOptions->freeBlocks as $freeBlock) {

            $block = $this->getController()->blockWidget(
                $freeBlock->id,
                $freeBlock->name
            );

            if (null === $block) {
                continue;
            }

            $parentViewModel->addChild($block, $freeBlock->name);
        }

        foreach ($publishingOptions->blockSequences as $blockSequence) {

            foreach ($blockSequence->blocks as $block) {

                $block = $this->getController()->blockWidget(
                    $block->id,
                    $blockSequence->name
                );

                if (null === $block) {
                    continue;
                }

                $parentViewModel->addChild($block, $blockSequence->name, true);
            }
        }

        return $parentViewModel;
    }

}
