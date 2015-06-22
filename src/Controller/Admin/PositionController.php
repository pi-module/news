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
namespace Module\News\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Module\News\Form\PositionForm;
use Module\News\Form\PositionFilter;

class PositionController extends ActionController
{
    protected $positionColumns = array('id', 'title', 'order', 'status');

	public function indexAction()
    {
        // Get from url
        $module = $this->params('module');
        // Get info
        $list = array();
        $order = array('order ASC', 'id ASC');
        $select = $this->getModel('field_position')->select()->order($order);
        $rowset = $this->getModel('field_position')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $list[$row->id] = $row->toArray();
        }
        // Go to update page if empty
        if (empty($list)) {
            return $this->redirect()->toRoute('', array('action' => 'update'));
        }
        // Set view
        $this->view()->setTemplate('position-index');
        $this->view()->assign('list', $list);
    }

    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
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
                if (!empty($values['id'])) {
                    $row = $this->getModel('field_position')->find($values['id']);
                } else {
                    $row = $this->getModel('field_position')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Add log
                $operation = (empty($values['id'])) ? 'add' : 'edit';
                Pi::api('log', 'shop')->addLog('position', $row->id, $operation);
                $message = __('Attribute position data saved successfully.');
                $this->jump(array('action' => 'index'), $message);
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