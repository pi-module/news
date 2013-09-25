<?php
/**
 * News admin spotlight controller
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
use Module\News\Form\SpotlightForm;
use Module\News\Form\SpotlightFilter;

class SpotlightController extends ActionController
{
    protected $spotlightColumns = array(
        'id', 'story', 'topic', 'author', 'publish', 'expire', 'status'
    );

    public function indexAction()
    {
        // Get page
        $page = $this->params('p', 1);
        // Get story and topic
        $select = $this->getModel('spotlight')->select()->where(array('expire > ?' => time()))->columns(array('story', 'topic'));
        $idSet = $this->getModel('spotlight')->selectWith($select)->toArray();
        if (empty($idSet)) {
            return $this->redirect()->toRoute('', array('action' => 'update'));
        }
        // Set topics and stores
        foreach ($idSet as $spotlight) {
            $topicArr[] = $spotlight['topic'];
            $storyArr[] = $spotlight['story'];
        }
        // Get topics
        $select = $this->getModel('topic')->select()->where(array('id' => array_unique($topicArr)))->columns(array('id', 'title', 'slug'));
        $topicSet = $this->getModel('topic')->selectWith($select);
        // Make topic list
        foreach ($topicSet as $row) {
            $topicList[$row->id] = $row->toArray();
        }
        $topicList[-1] = array('id' => -1, 'title' => __('Home Page'), 'slug' => '');
        $topicList[0] = array('id' => 0, 'title' => __('All Topics'), 'slug' => '');
        // Get stores
        $select = $this->getModel('story')->select()->where(array('id' => array_unique($storyArr)))->columns(array('id', 'title', 'slug'));
        $storySet = $this->getModel('story')->selectWith($select);
        // Make story list
        foreach ($storySet as $row) {
            $storyList[$row->id] = $row->toArray();
        }
        // Get spotlights
        $select = $this->getModel('spotlight')->select()->where(array('expire > ?' => time()))->order(array('id DESC', 'publish DESC'));
        $spotlightSet = $this->getModel('spotlight')->selectWith($select);
        // Make spotlight list
        foreach ($spotlightSet as $row) {
            $spotlightList[$row->id] = $row->toArray();
            $spotlightList[$row->id]['storytitle'] = $storyList[$row->story]['title'];
            $spotlightList[$row->id]['storyslug'] = $storyList[$row->story]['slug'];
            $spotlightList[$row->id]['topictitle'] = $topicList[$row->topic]['title'];
            $spotlightList[$row->id]['topicslug'] = $topicList[$row->topic]['slug'];
            $spotlightList[$row->id]['publish'] = _date($spotlightList[$row->id]['publish']);
            $spotlightList[$row->id]['expire'] = _date($spotlightList[$row->id]['expire']);
        }
        // Set paginator
        $paginator = \Pi\Paginator\Paginator::factory($spotlightList);
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
                'controller' => 'spotlight',
                'action' => 'index',
            ),
            // Or use a URL template to create URLs
            //'template'      => '/url/p/%page%/t/%total%',

        ));
        // Set view
        $this->view()->setTemplate('spotlight_index');
        $this->view()->assign('spotlights', $paginator);
    }

    public function updateAction()
    {
        // Get id
        $id = $this->params('id');
        $form = new SpotlightForm('topic', $this->getModule());
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new SpotlightFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->spotlightColumns)) {
                        unset($values[$key]);
                    }
                }
                // Set time
                $values['publish'] = strtotime($values['publish']);
                $values['expire'] = strtotime($values['expire']);
                // Set if new
                if (empty($values['id'])) {
                    // Set user
                    $values['author'] = Pi::registry('user')->id;
                }
                // Save values
                if (!empty($values['id'])) {
                    $row = $this->getModel('spotlight')->find($values['id']);
                } else {
                    $row = $this->getModel('spotlight')->createRow();
                }
                $row->assign($values);
                $row->save();
                // Check it save or not
                if ($row->id) {
                    $message = __('Spotlight data saved successfully.');
                    $this->jump(array('action' => 'index'), $message);
                } else {
                    $message = __('Spotlight data not saved.');
                }
            } else {
                $message = __('Invalid data, please check and re-submit.');
            }
        } else {
            if ($id) {
                $values = $this->getModel('spotlight')->find($id)->toArray();
                $form->setData($values);
                $message = 'You can edit this spotlight';
            } else {
                $message = 'You can add new spotlight';
            }
        }
        $this->view()->setTemplate('spotlight_update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add a Spotlight'));
        $this->view()->assign('message', $message);
    }

    public function deleteAction()
    {
        /*
           * not completed and need confirm option
           */
        // Get information
        $this->view()->setTemplate(false);
        $id = $this->params('id');
        $row = $this->getModel('spotlight')->find($id);
        if ($row) {
            // Remove page
            $row->delete();
            $this->jump(array('action' => 'index'), __('Selected spotlight delete'));
        }
        $this->jump(array('action' => 'index'), __('Please select spotlight'));
    }
}	