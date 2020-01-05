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
use Pi\Mvc\Controller\ActionController;
use Module\News\Form\PositionForm;
use Module\News\Form\PositionFilter;

class PositionController extends ActionController
{
    protected $positionColumns = ['id', 'title', 'order', 'status'];

    public function indexAction()
    {
        // Get from url
        $module = $this->params('module');
        // Get info
        $list   = [];
        $order  = ['order ASC', 'id ASC'];
        $select = $this->getModel('field_position')->select()->order($order);
        $rowset = $this->getModel('field_position')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
        }
        // Go to update page if empty
        if (empty($list)) {
            return $this->redirect()->toRoute('', ['action' => 'update']);
        }
        // Set view
        $this->view()->setTemplate('position-index');
        $this->view()->assign('list', $list);
    }

    public function updateAction()
    {
        // Get id
        $id     = $this->params('id');
        $module = $this->params('module');
        // Set form
        $form = new PositionForm('position');
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new PositionFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // Set just category fields
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->positionColumns)) {
                        unset($values[$key]);
                    }
                }
                // Save values
                if (!empty($id)) {
                    $row = $this->getModel('field_position')->find($id);
                } else {
                    $row = $this->getModel('field_position')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Jump
                $message = __('Attribute position data saved successfully.');
                $this->jump(['action' => 'index'], $message);
            }
        } else {
            if ($id) {
                $position = $this->getModel('field_position')->find($id)->toArray();
                $form->setData($position);
            }
        }
        // Set view
        $this->view()->setTemplate('position-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add position'));
    }
}