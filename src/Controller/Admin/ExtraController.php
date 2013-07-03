<?php
/**
 * News admin topic controller
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

namespace Module\News\Controller\Admin;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Pi\File\Transfer\Upload;
use Module\News\Form\ExtraForm;
use Module\News\Form\ExtraFilter;

class ExtraController extends ActionController
{
    protected $ImagePrefix = 'extra_';
    protected $extraColumns = array('id', 'title', 'image', 'type', 'order', 'status', 'search');

    public function indexAction()
    {
        // Get info
        $select = $this->getModel('field')->select()->order(array('order ASC'));
        $rowset = $this->getModel('field')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $field[$row->id] = $row->toArray();
            $field[$row->id]['imageurl'] = Pi::url('upload/' . $this->config('file_path') . '/extra/' . $field[$row->id]['image']);
        }
        // Go to update page if empty
        if (empty($field)) {
            return $this->redirect()->toRoute('', array('action' => 'update'));
        }
        // Set view
        $this->view()->setTemplate('extra_index');
        $this->view()->assign('fields', $field);
    }

    public function removeAction()
    {
        // Get id and status
        $id = $this->params('id');
        // set story
        $field = $this->getModel('field')->find($id);
        // Check
        if ($field && !empty($id)) {
            // remove image
            $this->removeFile($field->image);
            // clear DB
            $field->image = '';
            // Save
            if ($field->save()) {
                $message = sprintf(__('Image of %s removed'), $field->title);
                $status = 1;
            } else {
                $message = __('Image not remove');
                $status = 0;
            }
        } else {
            $message = __('Please select story');
            $status = 0;
        }
        return array(
            'status' => $status,
            'message' => $message,
        );
    }

    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
        $module = $this->params('module');
        // Set story image url
        $options['imageurl'] = null;
        // Get story
        if ($id) {
            $values = $this->getModel('field')->find($id)->toArray();
            // Set story image url
            if ($values['image']) {
                $options['imageurl'] = Pi::url('upload/' . $this->config('file_path') . '/extra/' . $values['image']);
                $options['removeurl'] = $this->url('', array('action' => 'remove', 'id' => $values['id']));
            }
        }
        // Set form
        $form = new ExtraForm('story', $options);
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $file = $this->request->getFiles();
            $form->setInputFilter(new ExtraFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                // upload image
                if (!empty($file['image']['name'])) {
                    // Do upload
                    $uploader = new Upload(array('destination' => $this->config('file_path') . '/extra/', 'rename' => $this->ImagePrefix . '%random%'));
                    $uploader->setExtension($this->config('image_extension'));
                    $uploader->setSize($this->config('image_size'));
                    if ($uploader->isValid()) {
                        $uploader->receive();
                        // Get image name
                        $values['image'] = $uploader->getUploaded('image');
                    } else {
                        $this->jump(array('action' => 'update'), __('Problem in upload image. please try again'));
                    }
                } elseif (!isset($values['image'])) {
	                $values['image'] = '';	
                }
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->extraColumns)) {
                        unset($values[$key]);
                    }
                }
                // Set order
                $select = $this->getModel('field')->select()->columns(array('order'))->order(array('order DESC'))->limit(1);
                $values['order'] = $this->getModel('field')->selectWith($select)->current()->order + 1;
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('field')->find($values['id']);
                } else {
                    $row = $this->getModel('field')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Check it save or not
                if ($row->id) {
                    $message = __('Extra field data saved successfully.');
                    $url = array('action' => 'index');
                    $this->jump($url, $message);
                } else {
                    $message = __('Extra field data not saved.');
                }
            } else {
                $message = __('Invalid data, please check and re-submit.');
            }
        } else {
            if ($id) {
                $form->setData($values);
                $message = 'You can edit this extra field';
            } else {
                $message = 'You can add new extra field';
            }
        }
        // Set view
        $this->view()->setTemplate('extra_update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add Extra'));
        $this->view()->assign('message', $message);
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
        /*
           * not completed and need confirm option
           */
        // Get information
        $this->view()->setTemplate(false);
        $id = $this->params('id');
        $row = $this->getModel('field')->find($id);
        if ($row) {
            // Remove all data
            $this->getModel('data')->delete(array('field' => $row->id));
            // Remove image
            $this->removeFile($row->image);
            // Remove field
            $row->delete();
            $this->jump(array('action' => 'index'), __('Selected field delete'));
        } else {
            $this->jump(array('action' => 'index'), __('Please select field'));
        }
    }

    protected function removeFile($file)
    {
        $file = Pi::path('upload/' . $this->config('file_path') . '/extra/' . $file);
        Pi::service('file')->remove($file);
    }
}