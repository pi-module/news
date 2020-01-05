<?php
/**
 * Pi Engine (http://piengine.org)
 *
 * @link            http://code.piengine.org for the Pi Engine source repository
 * @copyright       Copyright (c) Pi Engine http://piengine.org
 * @license         http://piengine.org/license.txt New BSD License
 */

/**
 * @author Hossein Azizabadi <azizabadi@faragostaresh.com>
 */

namespace Module\News\Controller\Admin;

use Pi;
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;
use Pi\Paginator\Paginator;
use Pi\File\Transfer\Upload;
use Module\News\Form\AuthorRoleForm;
use Module\News\Form\AuthorRoleFilter;

class roleController extends ActionController
{
    protected $authorRoleColumns
        = [
            'id', 'title', 'status',
        ];

    public function indexAction()
    {
        // Get page
        $module = $this->params('module');
        // Set info
        $order = ['title DESC', 'id DESC'];
        // Get list of author
        $select = $this->getModel('author_role')->select()->order($order);
        $rowset = $this->getModel('author_role')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $role[$row->id] = $row->toArray();
        }
        // Set view
        $this->view()->setTemplate('author-role');
        $this->view()->assign('roles', $role);
    }

    public function updateAction()
    {
        // Get id
        $id     = $this->params('id');
        
        // Set form
        $form = new AuthorRoleForm('role');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new AuthorRoleFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // Set just category fields
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->authorRoleColumns)) {
                        unset($values[$key]);
                    }
                }
                // Save values
                if (!empty($id)) {
                    $row = $this->getModel('author_role')->find($id);
                } else {
                    $row = $this->getModel('author_role')->createRow();
                }
                $row->assign($values);
                $row->save();
                
                // Clear registry
                Pi::registry('authorList', 'news')->clear();
                
                // jump
                $message = __('Role data saved successfully.');
                $this->jump(['action' => 'index'], $message);
            }
        } else {
            if ($id) {
                $role = $this->getModel('author_role')->find($id)->toArray();
                $form->setData($role);
            }
        }
        
        // Set view
        $this->view()->setTemplate('author-role-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add Role'));
    }
}