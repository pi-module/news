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
use Pi\Filter;
use Module\News\Form\AttributeForm;
use Module\News\Form\AttributeFilter;
use Zend\Json\Json;

class AttributeController extends ActionController
{
    protected $attributeColumns = array(
        'id', 'title', 'icon', 'type', 'order', 'status', 'search', 'value', 'position', 'name'
    );

    public function indexAction()
    {
        // Get position list
        $position = Pi::api('attribute', 'news')->attributePositionForm();
        // Get info
        $select = $this->getModel('field')->select()->order(array('order ASC'));
        $rowset = $this->getModel('field')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $field[$row->position][$row->id] = $row->toArray();
            $field[$row->position][$row->id]['position_view'] = $position[$row->position];
        }
        // Go to update page if empty
        if (empty($field)) {
            return $this->redirect()->toRoute('', array('action' => 'update'));
        }
        // Set view
        $this->view()->setTemplate('attribute_index');
        $this->view()->assign('fields', $field);
        $this->view()->assign('positions', $position);
    }

    /**
     * Attribute Action
     */
    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
        $module = $this->params('module');
        // Get attribute
        if ($id) {
            $attribute = $this->getModel('field')->find($id)->toArray();
            $attribute['topic'] = Pi::api('attribute', 'news')->getTopic($attribute['id']);
            // Set value
            $value = Json::decode($attribute['value'], true);
            $attribute['data'] = $value['data'];
            $attribute['default'] = $value['default'];
            $attribute['information'] = $value['information'];
        }
        // Set form
        $form = new AttributeForm('attribute', $options);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            // Set name
            $filter = new Filter\Slug;
            $data['name'] = $filter($data['name']);
            // Form filter
            $form->setInputFilter(new AttributeFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // Set value
                $value = array(
                    'data'         => $data['data'],
                    'default'      => $data['default'],
                    'information'  => $data['information'],
                );
                $values['value'] = Json::encode($value);
                // Set just product fields
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->attributeColumns)) {
                        unset($values[$key]);
                    }
                }
                // Set order
                if (empty($values['id'])) {
                    $columns = array('order');
                    $order = array('order DESC');
                    $select = $this->getModel('field')->select()->columns($columns)->order($order)->limit(1);
                    $values['order'] = $this->getModel('field')->selectWith($select)->current()->order + 1;
                }
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('field')->find($values['id']);
                } else {
                    $row = $this->getModel('field')->createRow();
                }
                $row->assign($values);
                $row->save();
                //
                Pi::api('attribute', 'news')->setTopic($row->id, $data['topic']);
                // Check it save or not
                $message = __('Attribute field data saved successfully.');
                $url = array('action' => 'index');
                $this->jump($url, $message);
            }
        } else {
            if ($id) {
                $form->setData($attribute);
            }
        }
        // Set view
        $this->view()->setTemplate('attribute_update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add attribute'));
    }

    public function sortAction()
    {
        $order = 1;
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            foreach ($data['mod'] as $id) {
                if ($id > 0) {
                    $row = $this->getModel('field')->find($id);
                    $row->order = $order;
                    $row->save();
                    $order++;
                }
            }
        }
        // Set view
        $this->view()->setTemplate(false);
    }

    public function deleteAction()
    {
        // Get information
        $this->view()->setTemplate(false);
        $id = $this->params('id');
        $row = $this->getModel('field')->find($id);
        if ($row) {
            // Remove all data
            $this->getModel('field_data')->delete(array('field' => $row->id));
            // Remove field
            $row->delete();
            $this->jump(array('action' => 'index'), __('Selected field delete'));
        } else {
            $this->jump(array('action' => 'index'), __('Please select field'));
        }
    }
}