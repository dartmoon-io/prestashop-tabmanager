<?php

namespace Dartmoon\TabManager;

use Dartmoon\Utils\Facades\MultiLangText;
use PrestaShop\PrestaShop\Adapter\Entity\Tab;
use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

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
     * Install a tab menu into PrestaShop menu
     */
    public function installTab($name, $className, $parent, $module = '', $icon = '') 
    {
        $name = is_array($name) ?: MultiLangText::generate($name);

        // Create the new Tab
        $tab = new Tab();
        $tab->class_name = $className;
        $tab->name = $name;
        $tab->icon = $icon;
        $tab->module = $module;

        // Find the parent
        $idParent = $parent;
        if (is_string($parent)) {
            $parentTab = $this->tabRepository->findOneByClassName($parent);
            $idParent = $parentTab->id;
        }
        $tab->id_parent = (int) $idParent;

        // Save the tab
        $tab->save();

        // Return the tab (its ID is needed to nesting other tabs)
        return $tab;
    }

    /**
     * Uninstall a tab menu from PrestaShop menu
     */
    public function uninstallTab($className)
    {
        $idTab = $this->tabRepository->findOneByClassName($className);
        if ($idTab) {
            $tab = new Tab($idTab);
            $tab->delete();
            return true;
        }

        return false;
    }

    /**
     * Install multiple tabs
     */
    public function install(array $tabs)
    {
        foreach ($tabs as $tab) {
            $this->installTab(
                $tab['name'],
                $tab['class_name'],
                $tab['parent'],
                $tab['module'] ?? '',
                $tab['icon'] ?? ''
            );
        }
    }

    /**
     * Uninstall multiple tabs
     */
    public function uninstall(array $tabs)
    {
        foreach ($tabs as $tab) {
            $this->uninstallTab($tab['class_name']);
        }
    }
}