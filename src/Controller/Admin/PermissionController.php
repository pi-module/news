<?php
/**
 * News admin permission controller
 *
 * You may not change or alter any portion of this comment or credits
 * of supporting developers from this source code or any supporting source code
 * which is considered copyrighted (c) material of the original comment or credit authors.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @copyright       Copyright (c) Pi Engine http://www.xoopsengine.org
 * @license         http://www.xoopsengine.org/license New BSD License
 * @author          Hossein Azizabadi <azizabadi@faragostaresh.com>
 * @since           3.0
 * @package         Module\News
 * @version         $Id$
 */

/* 
namespace Module\News\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\Acl\Acl as AclHandler;

class PermissionController extends ActionController
{
	 protected function getRoles($section, &$role)
    {
        $rowset = Pi::model('acl_role')->select(array('section' => $section));
        $roles = array();
        foreach ($rowset as $row) {
            if ('admin' == $row->name) {
                continue;
            }
            $roles[$row->name] = __($row->title);
            if (!$role) {
                $role = $row->name;
            }
        }

        return $roles;
    }
	 
	 public function indexAction()
    {
        $section = $this->params('section', 'front');
        $name = $this->params('name', 'read');
        $role = $this->params('role', 'member');

        $roles = $this->getRoles($section);
    	  
    	  
    	  // Set view
        $this->view()->setTemplate('permission_index');
        $this->view()->assign('title', __('Module permissions'));
        $this->view()->assign('role', $role);
        $this->view()->assign('roles', $roles);
        $this->view()->assign('name', $name);
        $this->view()->assign('section', $section);
    }	
}	
*/