<?php
/**
 * Pi Engine (http://pialog.org)
 *
 * @link            http://code.pialog.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://pialog.org
 * @license         http://pialog.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
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