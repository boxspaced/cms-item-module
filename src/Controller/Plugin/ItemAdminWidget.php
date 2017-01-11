<?php
namespace Boxspaced\CmsItemModule\Controller\Plugin;

use DateTime;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;
use Zend\View\Model\ViewModel;
use Boxspaced\CmsAccountModule\Service\AccountService;

class ItemAdminWidget extends AbstractPlugin
{

    /**
     * @var AccountService
     */
    protected $accountService;

    /**
     * @var array
     */
    protected $config;

    /**
     * @param AccountService $accountService
     * @param array $config
     */
    public function __construct(
        AccountService $accountService,
        array $config
    )
    {
        $this->accountService = $accountService;
        $this->config = $config;
    }

    /**
     * @todo just pass content identifier in here and fetch data (liveFrom etc.) needed from services
     * @param DateTime $liveFrom
     * @param DateTime $expiresEnd
     * @param string $typeName
     * @return ItemAdminWidget|ViewModel|null
     */
    public function __invoke(DateTime $liveFrom = null, DateTime $expiresEnd = null, $typeName = null)
    {
        if (0 === func_num_args()) {
            // @todo remove as only done so we can access the calc* methods
            return $this;
        }

        if (null === $this->accountService->getIdentity()) {
            return null;
        }

        $lifespanState = $this->calcLifeSpanState($liveFrom, $expiresEnd);
        $lifespanTitle = $this->calcLifeSpanTitle($liveFrom, $expiresEnd);

        $viewModel = new ViewModel([
            'id' => $this->getController()->params()->fromRoute('id'),
            'lifespanState' => $lifespanState,
            'lifespanTitle' => $lifespanTitle,
            'typeName' => $typeName,
            'allowEdit' => $this->accountService->isAllowed('item', 'edit'),
            'allowPublish' => $this->accountService->isAllowed('item', 'publish'),
            'allowDelete' => $this->accountService->isAllowed('item', 'delete'),
        ]);

        return $viewModel->setTemplate('cms-item-module/item/admin-widget.phtml');
    }

    /**
     * @todo move to a view helper
     * @param DateTime $liveFrom
     * @param DateTime $expiresEnd
     * @return string
     */
    public function calcLifeSpanState(DateTime $liveFrom, DateTime $expiresEnd)
    {
        $now = new DateTime();

        if ($liveFrom < $now && $expiresEnd > $now) {
            return 'on';
        }

        return 'off';
    }

    /**
     * @todo move to a view helper
     * @param DateTime $liveFrom
     * @param DateTime $expiresEnd
     * @return string
     */
    public function calcLifeSpanTitle(DateTime $liveFrom, DateTime $expiresEnd)
    {
        $now = new DateTime();

        if ($liveFrom < $now && $expiresEnd > $now) {

            $lifespanTitle = 'Online - ';

            if ('2038-01-19' === $expiresEnd->format('Y-m-d')) {
                $lifespanTitle .= 'never expiring';
            } else {
                $lifespanTitle .= 'expires ' . $expiresEnd->format('j F Y');
            }

            return $lifespanTitle;
        }

        if ($liveFrom > $now) {
            return 'Offline - due to come online ' . $liveFrom->format('j F Y');
        }

        return 'Expired on ' . $expiresEnd->format('j F Y');
    }

}
