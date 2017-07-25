<?php
namespace Boxspaced\CmsItemModule\Controller;

use DateTime;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Session\Container as SessionContainer;
use Zend\View\Model\ViewModel;
use Zend\Log\Logger;
use Boxspaced\CmsItemModule\Service;
use Boxspaced\CmsItemModule\Exception;
use Boxspaced\CmsItemModule\Form;
use Boxspaced\CmsAccountModule\Service\AccountService;
use Boxspaced\CmsWorkflowModule\Service\WorkflowService;
use Boxspaced\CmsBlockModule\Service\BlockService;

class ItemController extends AbstractActionController
{

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
     * @var AccountService
     */
    protected $accountService;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var SessionContainer
     */
    protected $previewSession;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var ViewModel
     */
    protected $view;

    /**
     * @param Service\ItemService $itemService
     * @param BlockService $blockService
     * @param WorkflowService $workflowService
     * @param AccountService $accountService
     * @param Logger $logger
     * @param array $config
     */
    public function __construct(
        Service\ItemService $itemService,
        BlockService $blockService,
        WorkflowService $workflowService,
        AccountService $accountService,
        Logger $logger,
        array $config
    )
    {
        $this->itemService = $itemService;
        $this->blockService = $blockService;
        $this->workflowService = $workflowService;
        $this->accountService = $accountService;
        $this->logger = $logger;
        $this->config = $config;

        $this->previewSession = new SessionContainer('preview');

        $this->view = new ViewModel();
    }

    /**
     * @return void
     */
    protected function initBackendAction()
    {
        if ($this->config['core']['has_ssl']) {
            $this->forceHttps();
        }
        $this->layout('layout/admin');
    }

    /**
     * @return void
     */
    public function viewAction()
    {
        $id = $this->params()->fromRoute('id');
        $part = $this->params()->fromRoute('part', 1);
        $preview = $this->params()->fromQuery('preview');

        if (!$id) {
            return $this->notFoundAction();
        }

        try {
            $itemMeta = $this->itemService->getCacheControlledItemMeta($id);
            $itemType = $this->itemService->getType($itemMeta->typeId);
        } catch (Exception\UnexpectedValueException $e) {
            return $this->notFoundAction();
        }

        $canEdit = $this->accountService->isAllowed(get_class(), 'edit');
        $canPublish = $this->accountService->isAllowed(get_class(), 'publish');

        if ('content' === $preview && $canEdit) {

            // Previewing content
            $item = $this->previewSession->content;

            if ($this->workflowService->getStatus(Service\ItemService::MODULE_NAME, $id) === WorkflowService::WORKFLOW_STATUS_NEW) {

                $publishingOptions = new Service\PublishingOptions();
                $publishingOptions->templateId = $this->previewSession->templateId;
                $publishingOptions->to = Service\ItemService::PUBLISH_TO_STANDALONE;

            } else {
                $publishingOptions = $this->itemService->getCurrentPublishingOptions($id);
            }

        } elseif ('publishing' === $preview && $canPublish) {

            // Previewing publishing
            $itemId = $this->params()->fromQuery('contentId') ?: $id;
            $item = $this->itemService->getItem($itemId);

            // @todo module name constant shouldn't be coming from service
            if ($this->workflowService->getStatus(Service\ItemService::MODULE_NAME, $id) === WorkflowService::WORKFLOW_STATUS_NEW) {

                if ($this->params()->fromQuery('templateId')) {
                    $publishingOptions = new Service\PublishingOptions();
                    $publishingOptions->templateId = $this->params()->fromQuery('templateId');
                } else {
                    $publishingOptions = $this->previewSession->publishing;
                }

            } else {

                if ($this->params()->fromQuery('contentId')) {
                    $publishingOptions = $this->itemService->getCurrentPublishingOptions($id);
                } else {
                    $publishingOptions = $this->previewSession->publishing;
                }
            }

        } else {

            try {
                $item = $this->itemService->getCacheControlledItem($id);
                $publishingOptions = $this->itemService->getCurrentPublishingOptions($id);
            } catch (Exception\UnexpectedValueException $e) {
                return $this->notFoundAction();
            }

            // Live check
            $live = true;
            $now = new DateTime();

            if (
                $publishingOptions->liveFrom > $now
                || $publishingOptions->expiresEnd < $now
            ) {
                $live = false;
            }

            if (!$live && null === $this->accountService->getIdentity()) {
                return $this->notFoundAction();
            }

            $adminNavigation = $this->adminNavigationWidget();
            if (null !== $adminNavigation) {
                $this->layout()->addChild($adminNavigation, 'adminNavigation');
            }

            $contentAdmin = $this->itemAdminWidget(
                $publishingOptions->liveFrom,
                $publishingOptions->expiresEnd,
                $itemMeta->typeName
            );
            if (null !== $contentAdmin) {
                $this->layout()->addChild($contentAdmin, 'adminPanel');
            }
        }

        // Templates
        foreach ($itemType->templates as $template) {

            if ($template->id == $publishingOptions->templateId) {
                $itemTemplate = $template;
                break;
            }
        }

        if (!isset($itemTemplate)) {
            throw new Exception\RuntimeException('Item template not found');
        }

        $this->view->setTemplate('boxspaced/cms-item-module/item/' . $itemTemplate->viewScript . '.phtml');
        $this->layout()->template = $itemTemplate->name;

        foreach ($item as $name => $value) {

            if (!is_array($value) && !is_object($value)) {
                $this->view->setVariable($name, $value);
            }
        }

        $this->layout()->isStandalone = ($publishingOptions->to === Service\ItemService::PUBLISH_TO_STANDALONE);
        $this->view->colourScheme = $publishingOptions->colourScheme;

        if ('home' === $itemMeta->name) {
            $this->layout()->hideBreadcrumbs = true;
        }

        // Fields
        foreach ($item->fields as $field) {
            $this->view->setVariable($field->name, $field->value);
        }

        // Part
        foreach ($item->parts[abs($part - 1)]->fields as $field) {
            $this->view->setVariable($field->name, $field->value);
        }

        $this->itemBlocks($this->view, $publishingOptions);

        return $this->view;
    }

    /**
     * @return void
     */
    public function createAction()
    {
        $this->initBackendAction();

        $form = new Form\ItemCreateForm($this->itemService);
        $form->get('from')->setValue($this->params()->fromQuery('from'));
        $form->get('provisionalTo')->setValue($this->params()->fromQuery('provisionalTo'));
        $form->get('provisionalBeneathMenuItemId')->setValue($this->params()->fromQuery('provisionalBeneathMenuItemId'));

        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {
            return $this->view;
        }

        $form->setData($this->getRequest()->getPost());

        if (!$form->isValid()) {

            $this->flashMessenger()->addErrorMessage('Validation failed.');
            return $this->view;
        }

        $values = $form->getData();

        // Provisional location
        $provisionalLocation = null;
        if ($values['provisionalTo']) {

            $provisionalLocation = new Service\ProvisionalLocation();
            $provisionalLocation->to = $values['provisionalTo'];
            $provisionalLocation->beneathMenuItemId = (int) $values['provisionalBeneathMenuItemId'];
        }

        $itemId = $this->itemService->createDraft($values['name'], $values['typeId'], $provisionalLocation);

        $this->flashMessenger()->addSuccessMessage('Create successful, add content below.');

        return $this->redirect()->toRoute('item', [
            'action' => 'edit',
            'id' => $itemId,
            'from' => $values['from'],
        ]);
    }

    /**
     * @return void
     */
    public function editAction()
    {
        $this->initBackendAction();

        $id = $this->params()->fromRoute('id');
        $itemMeta = $this->itemService->getItemMeta($id);
        $item = $this->itemService->getItem($id);
        $identity = $this->accountService->getIdentity();

        if (
            $this->workflowService->getStatus(Service\ItemService::MODULE_NAME, $id) !== WorkflowService::WORKFLOW_STATUS_CURRENT
            && $itemMeta->authorId != $identity->id
        ) {
            throw new Exception\RuntimeException('User has not authored this draft/revision');
        }

        $this->view->titleSuffix = '';
        if ($this->workflowService->getStatus(Service\ItemService::MODULE_NAME, $id) !== WorkflowService::WORKFLOW_STATUS_CURRENT) {
            $this->view->titleSuffix = $this->workflowService->getStatus(Service\ItemService::MODULE_NAME, $id);
        }

        $this->view->typeName = $itemMeta->typeName;
        $this->view->itemName = $itemMeta->name;
        $this->view->itemNotes = $itemMeta->notes;
        $this->view->enableMetaFields = $this->config['item']['enable_meta_fields'];
        $this->view->multipleParts = $itemMeta->multipleParts;

        $form = new Form\ItemEditForm(
            $itemMeta->typeName,
            $id,
            $this->itemService,
            $this->workflowService
        );
        $form->get('id')->setValue($id);
        $form->get('from')->setValue($this->params()->fromQuery('from'));

        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {

            $form->populateFromItem($item);
            return $this->view;
        }

        $form->setData($this->getRequest()->getPost());

        if ($this->params()->fromPost('partial')) {

            $form->get('partial')->setValue(false);
            return $this->view;
        }

        if (!$form->isValid()) {

            $this->flashMessenger()->addErrorMessage('Validation failed.');
            return $this->view;
        }

        $values = $form->getData();

        $item = new Service\Item();
        $item->navText = $values['navText'];
        $item->title = $values['title'];
        $item->metaKeywords = isset($values['metaKeywords']) ? $values['metaKeywords'] : '';
        $item->metaDescription = isset($values['metaDescription']) ? $values['metaDescription'] : '';

        $item->fields = [];

        foreach (isset($values['fields']) ? $values['fields'] : [] as $name => $value) {

            $field = new Service\ItemField();
            $field->name = $name;
            $field->value = $value;

            $item->fields[] = $field;
        }

        $item->parts = [];

        foreach (isset($values['parts']) ? $values['parts'] : [] as $part => $fields) {

            if (empty($fields['delete'])) {

                $part = new Service\ItemPart();
                $part->orderBy = isset($fields['orderBy']) ? (int) $fields['orderBy'] : 0;

                unset($fields['delete']);
                unset($fields['orderBy']);

                foreach ($fields as $name => $value) {

                    $field = new Service\ItemField();
                    $field->name = $name;
                    $field->value = $value;

                    $part->fields[] = $field;
                }

                $item->parts[] = $part;
            }
        }

        if (null !== $values['save']) {

            $editId = $id;

            if ($this->workflowService->getStatus(Service\ItemService::MODULE_NAME, $id) === WorkflowService::WORKFLOW_STATUS_CURRENT) {
                $editId = $this->itemService->createRevision($id);
            }

            $this->itemService->edit($editId, $item, $values['note']);

            $this->flashMessenger()->addSuccessMessage('Save successful.');

            return $this->redirect()->toRoute('workflow', [
                'action' => 'authoring',
            ]);
        }

        if (null !== $values['publish']) {

            $canPublish = $this->accountService->isAllowed(get_class(), 'publish');

            if (!$canPublish) {

                $editId = $id;

                if ($this->workflowService->getStatus(Service\ItemService::MODULE_NAME, $id) === WorkflowService::WORKFLOW_STATUS_CURRENT) {
                    $editId = $this->itemService->createRevision($id);
                }

                $this->itemService->edit($editId, $item, $values['note']);
                $this->workflowService->moveToPublishing(Service\ItemService::MODULE_NAME, $editId);

                $this->flashMessenger()->addSuccessMessage('Save successful, content moved to publishing for approval.');

                return $this->redirect()->toRoute('workflow', [
                    'action' => 'authoring',
                ]);
            }

            switch ($this->workflowService->getStatus(Service\ItemService::MODULE_NAME, $id)) {

                case WorkflowService::WORKFLOW_STATUS_CURRENT:

                    $revisionId = $this->itemService->createRevision($id);

                    $this->itemService->edit($revisionId, $item, $values['note']);
                    $this->itemService->publish($revisionId);

                    $this->flashMessenger()->addSuccessMessage('Update successful.');

                    return $this->redirect()->toRoute('content', ['slug' => $itemMeta->name]);
                    break;

                case WorkflowService::WORKFLOW_STATUS_UPDATE:

                    $this->itemService->edit($id, $item, $values['note']);
                    $this->itemService->publish($id);

                    $this->flashMessenger()->addSuccessMessage('Update successful.');

                    return $this->redirect()->toRoute('workflow', [
                        'action' => 'authoring',
                    ]);
                    break;

                case WorkflowService::WORKFLOW_STATUS_NEW:

                    $this->itemService->edit($id, $item, $values['note']);
                    $this->workflowService->moveToPublishing(Service\ItemService::MODULE_NAME, $id);

                    $this->flashMessenger()->addSuccessMessage('Save successful, please set options below to complete publishing process.');

                    return $this->redirect()->toRoute('item', [
                        'action' => 'publish',
                        'id' => $id,
                    ]);
                    break;

                default:
                    throw new Exception\UnexpectedValueException('Workflow status unknown');
            }
        }

        // Preview
        $this->previewSession->content = $item;
        $this->previewSession->templateId = isset($values['previewTemplateId']) ? $values['previewTemplateId'] : null;
        $this->view->preview = true;

        return $this->view;
    }

    /**
     * @return void
     */
    public function publishAction()
    {
        $this->initBackendAction();

        $id = $this->params()->fromRoute('id');
        $itemMeta = $this->itemService->getItemMeta($id);
        $type = $this->itemService->getType($itemMeta->typeId);

        $provisionalLocation = $this->itemService->getProvisionalLocation($id);
        $availableLocationOptions = $this->itemService->getAvailableLocationOptions($id);

        $publishingOptions = null;
        if ($this->workflowService->getStatus(Service\ItemService::MODULE_NAME, $id) === WorkflowService::WORKFLOW_STATUS_CURRENT) {
            $publishingOptions = $this->itemService->getCurrentPublishingOptions($id);
        }

        $this->view->typeName = $itemMeta->typeName;
        $this->view->itemName = $itemMeta->name;
        $this->view->itemNotes = $itemMeta->notes;

        if (null !== $provisionalLocation) {

            $provisionalTo = array_filter(
                $availableLocationOptions->toOptions,
                function ($option) use ($provisionalLocation) {
                    return $option->value === $provisionalLocation->to;
                }
            );
            $provisionalTo = array_pop($provisionalTo);

            $this->view->provisionalTo = $provisionalTo->label;

            if ($provisionalLocation->beneathMenuItemId) {

                $provisionalBeneathMenuItem = array_filter(
                    $availableLocationOptions->beneathMenuItemOptions,
                    function($option) use ($provisionalLocation) {
                        return $option->value === $provisionalLocation->beneathMenuItemId;
                    }
                );
                $provisionalBeneathMenuItem = array_pop($provisionalBeneathMenuItem);

                $this->view->provisionalBeneathMenuItem = $provisionalBeneathMenuItem->label;

            } elseif ($provisionalLocation->to === Service\ItemService::PUBLISH_TO_MENU) {
                $this->view->provisionalBeneathMenuItem = 'Top level';
            }
        }

        foreach ($type->teaserTemplates as $teaserTemplate) {

            if ($teaserTemplate->id == $this->params()->fromPost('teaserTemplateId')) {
                $this->view->teaserTemplateDescription = $teaserTemplate->description;
            }
        }

        foreach ($type->templates as $template) {

            if ($template->id == $this->params()->fromPost('templateId')) {
                $this->view->templateDescription = $template->description;
            }
        }

        $form = new Form\ItemPublishForm(
            $id,
            $this->itemService,
            $this->blockService,
            $this->workflowService,
            $this->params()->fromPost('templateId')
        );
        $form->get('id')->setValue($id);
        $form->get('from')->setValue($this->params()->fromQuery('from'));

        $this->view->form = $form;

        if (!$this->getRequest()->isPost()) {

            $form->populateValues([
                'name' => $itemMeta->name,
            ]);

            if ($publishingOptions) {
                // Already published, editing
                $form->populateFromPublishingOptions($publishingOptions);
            }

            if (null !== $provisionalLocation) {
                $form->get('useProvisional')->setChecked(true);
            }

            return $this->view;
        }

        $form->setData($this->getRequest()->getPost());

        if ($this->params()->fromPost('partial')) {

            $form->get('partial')->setValue(false);
            return $this->view;
        }

        if (!$form->isValid()) {

            $this->flashMessenger()->addErrorMessage('Validation failed.');
            return $this->view;
        }

        $values = $form->getData();

        if (null === $publishingOptions) {
            $publishingOptions = new Service\PublishingOptions();
        }

        $publishingOptions->name = $values['name'];
        $publishingOptions->colourScheme = $values['colourScheme'];
        $publishingOptions->liveFrom = (new DateTime($values['liveFrom']))->setTime(0, 0, 0);
        $publishingOptions->expiresEnd = (new DateTime($values['expiresEnd']))->setTime(23, 59, 59);
        $publishingOptions->teaserTemplateId = $values['teaserTemplateId'];
        $publishingOptions->templateId = $values['templateId'];

        if (!empty($values['useProvisional']) && null !== $provisionalLocation) {

            $publishingOptions->to = $provisionalLocation->to;
            $publishingOptions->beneathMenuItemId = $provisionalLocation->beneathMenuItemId;

        } else {

            $publishingOptions->to = $values['to'];
            $publishingOptions->beneathMenuItemId = null;

            if ($values['to'] === Service\ItemService::PUBLISH_TO_MENU) {
                $publishingOptions->beneathMenuItemId = $values['beneathMenuItemId'];
            }
        }

        $publishingOptions->freeBlocks = [];

        foreach ($values['freeBlocks'] as $name => $block) {

            if (empty($block['id'])) {
                continue;
            }

            $freeBlock = new Service\FreeBlock();
            $freeBlock->name = $name;
            $freeBlock->id = $block['id'];

            $publishingOptions->freeBlocks[] = $freeBlock;
        }

        $publishingOptions->blockSequences = [];

        foreach ($values['blockSequences'] as $name => $sequence) {

            $blockSequence = new Service\BlockSequence();
            $blockSequence->name = $name;

            foreach ($sequence['blocks'] as $key => $block) {

                if (is_numeric($key)) {

                    $blockSequenceBlock = new Service\BlockSequenceBlock();
                    $blockSequenceBlock->id = $block['id'];
                    $blockSequenceBlock->orderBy = $block['orderBy'];
                    $blockSequence->blocks[] = $blockSequenceBlock;
                }
            }

            $publishingOptions->blockSequences[] = $blockSequence;
        }

        if (null !== $values['publish']) {

            $this->itemService->publish($id, $publishingOptions);

            $this->flashMessenger()->addSuccessMessage('Publishing successful.');

            return $this->redirect()->toRoute('content', ['slug' => $publishingOptions->name]);
        }

        // Preview
        $this->previewSession->publishing = $publishingOptions;
        $this->view->preview = true;

        return $this->view;
    }

    /**
     * @return void
     */
    public function deleteAction()
    {
        $this->initBackendAction();

        $form = new Form\ConfirmForm();
        $form->get('id')->setValue($this->params()->fromRoute('id'));
        $form->get('from')->setValue($this->params()->fromQuery('from'));
        $form->get('confirm')->setValue('Confirm delete');

        $this->view->form = $form;

        $this->layout('layout/dialog');
        $this->view->setTemplate('boxspaced/cms-item-module/item/confirm.phtml');

        if (!$this->getRequest()->isPost()) {
            return $this->view;
        }

        $form->setData($this->getRequest()->getPost());

        if (!$form->isValid()) {

            $this->flashMessenger()->addErrorMessage('Validation failed.');
            return $this->view;
        }

        $values = $form->getData();

        $this->itemService->delete($values['id']);

        $this->flashMessenger()->addSuccessMessage('Delete successful.');

        switch ($values['from']) {

            case 'menu':
                return $this->redirect()->toRoute('menu');
                break;

            case 'standalone':
                return $this->redirect()->toRoute('standalone');
                break;

            default:
                return $this->redirect()->toRoute('home');
        }
    }

    /**
     * @return void
     */
    public function publishUpdateAction()
    {
        $this->initBackendAction();

        $form = new Form\ConfirmForm($this->getRequest());
        $form->get('id')->setValue($this->params()->fromRoute('id'));
        $form->get('confirm')->setValue('Confirm update');

        $this->view->form = $form;

        $this->layout('layout/dialog');
        $this->view->setTemplate('boxspaced/cms-item-module/item/confirm.phtml');

        if (!$this->getRequest()->isPost()) {
            return $this->view;
        }

        $form->setData($this->getRequest()->getPost());

        if (!$form->isValid()) {

            $this->flashMessenger()->addErrorMessage('Validation failed.');
            return $this->view;
        }

        $values = $form->getData();

        $this->itemService->publish($values['id']);

        $this->flashMessenger()->addSuccessMessage('Update successful.');

        return $this->redirect()->toRoute('workflow', [
            'action' => 'publishing',
        ]);
    }

}
