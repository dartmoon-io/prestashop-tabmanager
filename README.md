# Prestashop TabManager
A simple package that allows you to add your controllers to the PrestaShop Backoffice menu. Simply define an array with all the menu items and let the package install them. 

## Installation

1. Install the package
```bash
composer require dartmoon/prestashop-tabmanager
```

2. Define an array called `menu_tabs` inside the main class of your module

```php
//...
protected $menu_tabs = [
    //
];
//...
```

4. Fix `install` and `unistall` method of your module

```php
//...
public function install()
{
    if (
        parent::install()
        && TabManager::install($this->menu_tabs, $this)
        // && $this->registerHook(...)
    ) {
        //...

        return true;
    }

    return false;
}

public function uninstall()
{
    //...
    TabManager::uninstallForModule($this);
    return parent::uninstall();
}
//...
```

## Usage

Simply add all the menu items to the `menu_tabs` array.

```php
protected $menu_tabs = [
    [// This is a parent tab
        'name' => 'Parent tab',
        'class_name' => 'UNIQUE_TAB_NAME',
        'route_name' => '',
        'parent_class_name' => '',
        'icon' => 'settings',
        'visible' => true,
    ],
    [ // This a child of the previus tab
        'name' => 'Child tab',
        'class_name' => 'MySuperClass', // Remember that the controller class name is MySuperClassController, but we need to add it without the suffix "Controller"
        'route_name' => '',
        'parent_class_name' => 'UNIQUE_TAB_NAME',
        'icon' => '',
        'visible' => true,
    ],
];
```

## License

This project is licensed under the MIT License - see the LICENSE.md file for details