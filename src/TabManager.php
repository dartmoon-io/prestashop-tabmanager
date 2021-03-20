<?php

namespace Dartmoon\TabManager;

use Dartmoon\Utils\Facades\MultiLangText;
use PrestaShop\PrestaShop\Adapter\Entity\Module;
use PrestaShop\PrestaShop\Adapter\Entity\Tab;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;
use PrestaShopBundle\Entity\Tab as EntityTab;
use Symfony\Component\HttpFoundation\ParameterBag;

class TabManager
{
    /**
     * Container
     */
    private $container;

    /**
     * TabRepository
     */
    private $tabRepository;

    public function __construct()
    {
        $this->container = SymfonyContainer::getInstance();
        $this->tabRepository = $this->container->get('prestashop.core.admin.tab.repository');
    }

    /**
     * Install multiple tabs
     */
    public function install(array $tabs, Module $module = null)
    {
        foreach ($tabs as $tabDetails) {
            $this->installTab(new ParameterBag($tabDetails), $module);
        }
    }

    /**
     * Uninstall multiple tabs
     */
    public function uninstall(array $tabs)
    {
        foreach ($tabs as $tabDetails) {
            $tab = $this->tabRepository->findOneByClassName($tabDetails['class_name']);
            $this->uninstallTab($tab);
        }
    }

    /**
     * Uninstall multiple tabs
     */
    public function uninstallForModule($module)
    {
        // We use the Tab repository to have only
        // installed tabs related to the module
        $tabs = $this->tabRepository->findByModule($module->name);
        foreach ($tabs as $tab) {
            $this->uninstallTab($tab);
        }
    }

    /**
     * Install a tab menu into PrestaShop menu
     */
    private function installTab(ParameterBag $tabDetails, Module $module) 
    {
        // Create the new Tab
        $tab = new Tab();
        $tab->id_parent = $this->getIdParent($tabDetails);
        $tab->module = $module ? $module->name : null;
        $tab->class_name = $tabDetails->get('class_name');
        $tab->route_name = $tabDetails->get('route_name');
        $tab->icon = $tabDetails->get('icon');
        $tab->active = $tabDetails->getBoolean('active');
        $tab->name = MultiLangText::generate($tabDetails->get('name'));
        $tab->save();
    }

    /**
     * Uninstall a tab menu from PrestaShop menu
     */
    private function uninstallTab(EntityTab $entityTab)
    {
        if (!$entityTab) {
            return;
        }

        $tab = new Tab($entityTab->getId());
        $tab->delete();
    }

    /**
     * Get the parent id
     */
    private function getIdParent(ParameterBag $tabDetails)
    {
        $parentClassName = $tabDetails->get('parent_class_name');
        if (empty($parentClassName)) {
            return 0;
        }

        $parentTab = $this->tabRepository->findOneByClassName($parentClassName);
        return $parentTab ? $parentTab->id : 0;
    }
}