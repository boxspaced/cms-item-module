<?php
namespace Boxspaced\CmsItemModule\Form;

use DateTime;
use Zend\Form\Form;
use Zend\Form\Element;
use Boxspaced\CmsBlockModule\Service\BlockService;
use Boxspaced\CmsWorkflowModule\Service\WorkflowService;
use Zend\InputFilter\InputFilter;
use Zend\Form\Fieldset;
use Boxspaced\CmsItemModule\Service;
use Zend\Validator;
use Zend\Filter;

class ItemPublishForm extends Form
{

    /**
     * @var int
     */
    protected $itemId;

    /**
     * @var Service\ItemService
     */
    protected $itemService;

    /**
     * @var BlockService
     */
    protected $blockService;

    /**
     * @var WorkflowService
     */
    protected $workflowService;

    /**
     * @var int
     */
    protected $selectedTemplateId;

    /**
     * @param int $itemId
     * @param Service\ItemService $itemService
     * @param BlockService $blockService
     * @param WorkflowService $workflowService
     * @param int $selectedTemplateId
     */
    public function __construct(
        $itemId,
        Service\ItemService $itemService,
        BlockService $blockService,
        WorkflowService $workflowService,
        $selectedTemplateId
    )
    {
        parent::__construct('item-publish');
        $this->itemId = $itemId;
        $this->itemService = $itemService;
        $this->blockService = $blockService;
        $this->workflowService = $workflowService;
        $this->selectedTemplateId = $selectedTemplateId;

        $this->setAttribute('method', 'post');
        $this->setAttribute('accept-charset', 'UTF-8');

        $this->addElements();
        $this->addInputFilter();
    }

    /**
     * @return void
     */
    protected function addElements()
    {
        $element = new Element\Csrf('token');
        $element->setCsrfValidatorOptions([
            'timeout' => 900,
        ]);
        $this->add($element);

        $element = new Element\Hidden('id');
        $this->add($element);

        $element = new Element\Hidden('from');
        $this->add($element);

        $element = new Element\Hidden('partial');
        $this->add($element);

        $element = new Element\Text('name');
        $element->setLabel('Name');
        $element->setOption('description', 'a-z, 0-9 and hyphens only');
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Select('colourScheme');
        $element->setLabel('Colour scheme');
        $element->setEmptyOption('');
        $element->setValueOptions($this->getColourSchemeValueOptions());
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Text('liveFrom');
        $element->setLabel('Live from');
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Text('expiresEnd');
        $element->setLabel('Expires end');
        $element->setAttribute('required', true);
        $this->add($element);

        $teaserTemplateValueOptions = $this->getTeaserTemplateValueOptions();

        $element = new Element\Select('teaserTemplateId');
        $element->setLabel('Teaser template');
        $element->setEmptyOption('');
        $element->setValueOptions($teaserTemplateValueOptions);
        if (1 === count($teaserTemplateValueOptions)) {
            $element->setValue(key($teaserTemplateValueOptions));
        }
        $element->setAttribute('required', true);
        $element->setOption('description', 'Template description: ');
        $this->add($element);

        $templateValueOptions = $this->getTemplateValueOptions();

        $element = new Element\Select('templateId');
        $element->setLabel('Main template');
        $element->setEmptyOption('');
        $element->setValueOptions($templateValueOptions);
        if (1 === count($templateValueOptions)) {
            $element->setValue(key($templateValueOptions));
        }
        $element->setAttribute('required', true);
        $element->setOption('description', 'Template description: ');
        $this->add($element);

        $element = new Element\Checkbox('useProvisional');
        $element->setLabel('Use provisional location (choosen by author)');
        $this->add($element);

        $element = new Element\Select('to');
        $element->setLabel('To');
        $element->setEmptyOption('');
        $element->setValueOptions($this->getToValueOptions());
        $element->setDisableInArrayValidator(true);
        $element->setAttribute('required', true);
        $this->add($element);

        $element = new Element\Select('beneathMenuItemId');
        $element->setLabel('Menu position');
        $element->setEmptyOption('Top level');
        $element->setValueOptions($this->getMenuPositionValueOptions());
        $this->add($element);

        $freeBlocks = new Fieldset('freeBlocks');
        $this->add($freeBlocks);

        $blockSequences = new Fieldset('blockSequences');
        $this->add($blockSequences);

        $template = $this->getTemplate();

        if (null !== $template) {

            foreach ($template->blocks as $block) {

                if (!$block->sequence) {

                    $freeBlock = new FreeBlockFieldset(
                        $block->name,
                        $block->adminLabel,
                        $this->getBlockValueOptions()
                    );
                    $freeBlocks->add($freeBlock);
                    continue;
                }

                $blockSequence = new BlockSequenceFieldset(
                    $block->name,
                    $block->adminLabel,
                    $this->getBlockValueOptions()
                );
                $blockSequences->add($blockSequence);
            }
        }

        $element = new Element\Submit('preview');
        $element->setValue('Preview');
        $this->add($element);

        $element = new Element\Submit('publish');
        $element->setValue('Publish');
        $this->add($element);
    }

    /**
     * @return ItemPublishForm
     */
    protected function addInputFilter()
    {
        $inputFilter = new InputFilter();

        $inputFilter->add([
            'name' => 'id',
            'filters' => [
                ['name' => Filter\ToInt::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'from',
            'allow_empty' => true,
            'validators' => [
                [
                    'name' => Validator\InArray::class,
                    'options' => [
                        'haystack' => ['menu', 'standalone'],
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'partial',
            'allow_empty' => true,
            'filters' => [
                ['name' => Filter\Boolean::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'name',
            'validators' => [
                [
                    'name' => Validator\Regex::class,
                    'break_chain_on_failure' => true,
                    'options' => [
                        'pattern' => '/^[a-z0-9-]+$/',
                    ],
                ],
                [
                    'name' => Validator\Callback::class,
                    'options' => [
                        'callback' => function($value, $context = []) {

                            if ($this->getCurrentName() !== $value) {
                                return $this->itemService->isNameAvailable($value);
                            }

                            return true;
                        },
                        'messages' => [
                            Validator\Callback::INVALID_VALUE => 'The name is already in use',
                        ],
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'colourScheme',
        ]);

        $inputFilter->add([
            'name' => 'liveFrom',
            'validators' => [
                [
                    'name' => Validator\Regex::class,
                    'options' => [
                        'pattern' => '/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])$/',
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'expiresEnd',
            'validators' => [
                [
                    'name' => Validator\Regex::class,
                    'options' => [
                        'pattern' => '/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9])$/',
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'teaserTemplateId',
        ]);

        $inputFilter->add([
            'name' => 'templateId',
        ]);

        $inputFilter->add([
            'name' => 'useProvisional',
            'allow_empty' => true,
            'filters' => [
                ['name' => Filter\Boolean::class],
            ],
        ]);

        $inputFilter->add([
            'name' => 'to',
            'allow_empty' => true,
            'continue_if_empty' => true,
            'validators' => [
                [
                    'name' => Validator\Callback::class,
                    'options' => [
                        'callback' => function($value, $context = []) {

                            if (!$context['useProvisional']) {

                                if (!(new Validator\NotEmpty())->isValid($value)) {
                                    return false;
                                }

                                $inArray = new Validator\InArray();
                                $inArray->setHaystack(array_keys($this->getToValueOptions()));

                                if (!$inArray->isValid($value)) {
                                    return false;
                                }
                            }

                            return true;
                        },
                        'messages' => [
                            Validator\Callback::INVALID_VALUE => Validator\NotEmpty::IS_EMPTY,
                        ],
                    ],
                ],
            ],
        ]);

        $inputFilter->add([
            'name' => 'beneathMenuItemId',
            'allow_empty' => true,
        ]);

        return $this->setInputFilter($inputFilter);
    }

    /**
     * @return Service\ItemType
     */
    protected function getItemType()
    {
        $id = $this->itemId;
        $meta = $this->itemService->getItemMeta($id);
        return $this->itemService->getType($meta->typeId);
    }

    /**
     * @return Service\PublishingOptions
     */
    protected function getCurrentPublishingOptions()
    {
        $id = $this->itemId;

        if ($this->workflowService->getStatus(Service\ItemService::MODULE_NAME, $id) === WorkflowService::WORKFLOW_STATUS_CURRENT) {
            return $this->itemService->getCurrentPublishingOptions($id);
        }

        return null;
    }

    /**
     * @return string
     */
    protected function getCurrentName()
    {
        $id = $this->itemId;
        $meta = $this->itemService->getItemMeta($id);
        return $meta->name;
    }

    /**
     * @return Service\ItemTemplate
     */
    protected function getTemplate()
    {
        $type = $this->getItemType();

        if (1 === count($type->templates)) {
            return array_pop($type->templates);
        }

        $templateId = $this->selectedTemplateId;

        if (null === $templateId) {
            $currentPublishingOptions = $this->getCurrentPublishingOptions();
            $templateId = isset($currentPublishingOptions->templateId) ? $currentPublishingOptions->templateId : null;
        }

        if (null === $templateId) {
            return null;
        }

        foreach ($type->templates as $template) {

            if ($template->id != $templateId) {
                continue;
            }

            return $template;
        }

        return null;
    }

    /**
     * @return array
     */
    protected function getMenuPositionValueOptions()
    {
        $id = $this->itemId;

        $locationOptions = $this->itemService->getAvailableLocationOptions($id);
        $currentPublishingOptions = $this->getCurrentPublishingOptions();

        $beneathMenuItemValueOptions = [];

        foreach ($locationOptions->beneathMenuItemOptions as $option) {
            $beneathMenuItemValueOptions[$option->value] = str_repeat('--', $option->level) . ' ' . $option->label;
        }

        if (null !== $currentPublishingOptions) {

            foreach ($beneathMenuItemValueOptions as $value => $label) {

                if ($value == $currentPublishingOptions->beneathMenuItemId) {
                    $beneathMenuItemValueOptions[$value] .= ' <--- Currently beneath';
                }
            }
        }

        return [
            'Beneath' => [
                'label' => 'Beneath',
                'options' => $beneathMenuItemValueOptions,
            ],
        ];
    }

    /**
     * @return array
     */
    protected function getToValueOptions()
    {
        $id = $this->itemId;
        $locationOptions = $this->itemService->getAvailableLocationOptions($id);

        $valueOptions = [];

        foreach ($locationOptions->toOptions as $option) {

            $valueOptions[$option->value] = $option->label;
        }

        return $valueOptions;
    }

    /**
     * @return array
     */
    protected function getColourSchemeValueOptions()
    {
        $valueOptions = [];

        foreach ($this->itemService->getAvailableColourSchemeOptions() as $option) {

            $valueOptions[$option->value] = $option->label;
        }

        return $valueOptions;
    }

    /**
     * @return array
     */
    public function getBlockValueOptions()
    {
        $valueOptions = [];

        foreach ($this->blockService->getAvailableBlockOptions() as $typeOption) {

            $options = [];

            foreach ($typeOption->blockOptions as $blockOption) {
                $options[$blockOption->value] = $blockOption->label;
            }

            $valueOptions[$typeOption->name] = [
                'label' => $typeOption->name,
                'options' => $options,
            ];
        }

        return $valueOptions;
    }

    /**
     * @return array
     */
    protected function getTeaserTemplateValueOptions()
    {
        $type = $this->getItemType();

        $valueOptions = [];

        foreach ($type->teaserTemplates as $teaserTemplate) {
            $valueOptions[$teaserTemplate->id] = $teaserTemplate->name;
        }

        return $valueOptions;
    }

    /**
     * @return array
     */
    protected function getTemplateValueOptions()
    {
        $type = $this->getItemType();

        $valueOptions = [];

        foreach ($type->templates as $template) {
            $valueOptions[$template->id] = $template->name;
        }

        return $valueOptions;
    }

    /**
     * @param Service\PublishingOptions $options
     * @return ItemPublishForm
     */
    public function populateFromPublishingOptions(Service\PublishingOptions $options)
    {
        $values = (array) $options;

        $freeBlocks = $values['freeBlocks'];
        $blockSequences = $values['blockSequences'];

        $values['freeBlocks'] = [];
        $values['blockSequences'] = [];

        foreach ($freeBlocks as $freeBlock) {
            $values['freeBlocks'][$freeBlock->name]['id'] = $freeBlock->id;
        }

        foreach ($blockSequences as $blockSequence) {

            $values['blockSequences'][$blockSequence->name] = [];

            foreach ($blockSequence->blocks as $key => $block) {

                $values['blockSequences'][$blockSequence->name]['blocks'][$key + 1]['id'] = $block->id;
                $values['blockSequences'][$blockSequence->name]['blocks'][$key + 1]['orderBy'] = $block->orderBy;
            }
        }

        $values['liveFrom'] = ($values['liveFrom'] instanceof DateTime) ? $values['liveFrom']->format('Y-m-d') : '';
        $values['expiresEnd'] = ($values['expiresEnd'] instanceof DateTime) ? $values['expiresEnd']->format('Y-m-d') : '';

        return parent::populateValues($values);
    }

}
