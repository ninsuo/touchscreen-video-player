<?php

namespace AppBundle\Menu;

use BaseBundle\Base\BaseMenu;
use Knp\Menu\FactoryInterface;

class Builder extends BaseMenu
{
    public function mainLeftMenu(FactoryInterface $factory, array $options)
    {
        $menu = $this->createMenu($factory, parent::POSITION_LEFT);
        $this->addRoute($menu, 'base.menu.home', 'home');

        if ($this->isGranted('ROLE_USER')) {
            $this->addRoute($menu, 'digilogin.menu.manage', 'digi_manage');
        }

        return $menu;
    }

    public function mainRightMenu(FactoryInterface $factory, array $options)
    {
        $menu = $this->createMenu($factory, parent::POSITION_RIGHT);

        if ($this->isGranted('ROLE_ADMIN')) {
            $this->addSubMenu($menu, 'base.menu.admin.main');
            $this->addRoute($menu['base.menu.admin.main'], 'base.menu.admin.users', 'admin_users');
            $this->addRoute($menu['base.menu.admin.main'], 'base.menu.admin.groups', 'admin_groups');
        }

        return $menu;
    }
}
