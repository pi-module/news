<?php
/**
 * News admin moderator controller
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
use Module\News\Form\ModeratorForm;
use Module\News\Form\ModeratorFilter;

class ModeratorController extends ActionController
{
    protected $moderatorColumns = array(
        'id', 'manager', 'topic', 'status'
    );

    public function indexAction()
    {
        // Get page
        $page = $this->params('p', 1);
        // Get topic list row
        $select = $this->getModel('topic')->select()->columns(array('id', 'title', 'slug'))->order(array('id DESC', 'create DESC'));
        $topicSet = $this->getModel('topic')->selectWith($select);
        // Get moderator list
        $select = $this->getModel('moderator')->select()->order(array('status DESC', 'id DESC'));
        ;
        $moderatorSet = $this->getModel('moderator')->selectWith($select);
        // Process moderators
        foreach ($moderatorSet as $moderator) {
            $row[$moderator->id] = $moderator->toArray();
            $user = Pi::model('user_account')->find($moderator->manager);
            $row[$moderator->id]['identity'] = $user->identity;
            $moderatorArr[$moderator->topic][] = $row[$moderator->id];
        }
        // Process topics
        foreach ($topicSet as $topic) {
            $list[$topic->id] = $topic->toArray();
            $list[$topic->id]['moderators'] = $moderatorArr[$topic->id];
        }
        // Set paginator
        $paginator = \Pi\Paginator\Paginator::factory($list);
        $paginator->setItemCountPerPage($this->config('admin_perpage'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            // Use router to build URL for each page
            'pageParam' => 'p',
            'totalParam' => 't',
            'router' => $this->getEvent()->getRouter(),
            'route' => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params' => array(
                'module' => $this->getModule(),
                'controller' => 'moderator',
                'action' => 'index',
            ),
            // Or use a URL template to create URLs
            //'template'      => '/url/p/%page%/t/%total%',

        ));
        // Set view
        $this->view()->setTemplate('moderator_index');
        $this->view()->assign('list', $paginator);
        $this->view()->assign('title', __('Manage Moderators'));
    }

    public function topicAction()
    {
        // Get information
        $id = $this->params('id');
        $topic = $this->getModel('topic')->find($id)->toArray();
        // topic select or find
        if (!$topic['id']) {
            $this->redirect()->toRoute('', array('action' => 'index'));
        }
        // Get corect managers
        $select = $this->getModel('moderator')->select()->where(array('topic' => $topic['id']))->order(array('status DESC', 'id DESC'));
        $rowset = $this->getModel('moderator')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $moderator[$row->id] = $row->toArray();
            $user = Pi::model('user_account')->find($row->manager);
            $moderator[$row->id]['identity'] = $user->identity;
        }
        // Set view
        $this->view()->setTemplate('moderator_topic');
        $this->view()->assign('moderators', $moderator);
        $this->view()->assign('title', sprintf(__('Managers %s Topic, moderators'), $topic['title']));
    }

    public function userAction()
    {
        // Get id
        $id = $this->params('id');
        // find user
        $user = Pi::model('user_account')->find($id);
        // Get  manager info
        $select = $this->getModel('moderator')->select()->where(array('manager' => $id))->order(array('status DESC', 'id DESC'));
        $rowset = $this->getModel('moderator')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $moderator[$row->topic] = $row->toArray();
            $topicList[] = $moderator[$row->topic]['topic'];
        }
        // Get topics
        $select = $this->getModel('topic')->select()->where(array('id' => $topicList))->columns(array('id', 'title', 'slug'));
        $rowset = $this->getModel('topic')->selectWith($select);
        // Make list
        foreach ($rowset as $topic) {
            $topics[$topic->id] = $topic->toArray();
            $moderator[$topic->id]['title'] = $topics[$topic->id]['title'];
            $moderator[$topic->id]['slug'] = $topics[$topic->id]['slug'];
        }
        // Set view
        $this->view()->setTemplate('moderator_user');
        $this->view()->assign('moderators', $moderator);
        $this->view()->assign('title', sprintf(__('Managers %s User, topics'), $user->identity));
    }

    public function updateAction()
    {
        // Set form
        $form = new ModeratorForm('moderator', $this->getModule());
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new ModeratorFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->moderatorColumns)) {
                        unset($values[$key]);
                    }
                }
                // Check for duplication
                $where = array('manager' => $values['manager'], 'topic' => $values['topic']);
                $select = $this->getModel('moderator')->select()->where($where);
                $rowset = $this->getModel('moderator')->selectWith($select);
                if ($rowset->count() == 0) {
                    $row = $this->getModel('moderator')->createRow();
                    $row->assign($values);
                    $row->save();
                    // Check it save or not
                    if ($row->id) {
                        $message = __('Moderator data saved successfully.');
                        $this->jump(array('action' => 'index'), $message);
                    } else {
                        $message = __('Moderator data not saved.');
                    }
                } else {
                    $message = __('This user is moderator of your selected topic, Please change user or topic and check again');
                }
            } else {
                $message = __('Invalid data, please check and re-submit.');
            }
        } else {
            $message = 'You can add new moderator for your selected topic';
        }
        // Set view
        $this->view()->setTemplate('moderator_update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add a Moderator'));
        $this->view()->assign('message', $message);
    }

    public function statusAction()
    {
        // Get id and status
        $id = $this->params('id');
        $status = $this->params('status');
        if (in_array($status, array(0, 1))) {
            // update
            $row = $this->getModel('moderator')->update(array('status' => $status), array('id' => $id));
            if ($row) {
                $message = 'Moderator status update successfully';
                $ajaxstatus = 1;
            } else {
                $message = 'Error in update moderator status';
                $ajaxstatus = 0;
            }
        } else {
            $message = __('Error in select status');
            $ajaxstatus = 0;
        }

        return array(
            'status' => $ajaxstatus,
            'message' => $message,
        );

    }

    public function deleteAction()
    {
        // Delete row
        $this->getModel('moderator')->delete(array('id' => $this->params('id')));
        // view
        $this->view()->setTemplate(false);
        $this->jump(array('action' => 'index'), __('moderator removed'));
    }
}	