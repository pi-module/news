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
use Pi\Paginator\Paginator;
use Module\News\Form\SpotlightForm;
use Module\News\Form\SpotlightFilter;

class SpotlightController extends ActionController
{
    protected $spotlightColumns = array(
        'id', 'story', 'topic', 'uid', 'time_publish', 'time_expire', 'status'
    );

    public function indexAction()
    {
        // Get page
        $page = $this->params('page', 1);
        $whereSpotlight = array('time_expire > ?' => time());
        // Get story and topic
        $columns = array('story', 'topic');
        $select = $this->getModel('spotlight')->select()->where($whereSpotlight)->columns($columns);
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
        $whereTopic = array('id' => array_unique($topicArr));
        $columns = array('id', 'title', 'slug');
        $select = $this->getModel('topic')->select()->where($whereTopic)->columns($columns);
        $topicSet = $this->getModel('topic')->selectWith($select);
        // Make topic list
        foreach ($topicSet as $row) {
            $topicList[$row->id] = $row->toArray();
        }
        $topicList[-1] = array(
            'id'     => -1, 
            'title'  => __('Home Page'), 
            'slug'   => ''
        );
        $topicList[0] = array(
            'id'     => 0, 
            'title'  => __('All Topics'), 
            'slug'   => ''
        );
        // Get stores
        $whereStory = array('id' => array_unique($storyArr));
        $columns = array('id', 'title', 'slug');
        $select = $this->getModel('story')->select()->where($whereStory)->columns($columns);
        $storySet = $this->getModel('story')->selectWith($select);
        // Make story list
        foreach ($storySet as $row) {
            $storyList[$row->id] = $row->toArray();
        }
        // Get spotlights
        $order = array('id DESC', 'time_publish DESC');
        $select = $this->getModel('spotlight')->select()->where($whereSpotlight)->order($order);
        $spotlightSet = $this->getModel('spotlight')->selectWith($select);
        // Make spotlight list
        foreach ($spotlightSet as $row) {
            $spotlightList[$row->id] = $row->toArray();
            $spotlightList[$row->id]['storytitle'] = $storyList[$row->story]['title'];
            $spotlightList[$row->id]['storyslug'] = $storyList[$row->story]['slug'];
            $spotlightList[$row->id]['topictitle'] = $topicList[$row->topic]['title'];
            $spotlightList[$row->id]['topicslug'] = $topicList[$row->topic]['slug'];
            $spotlightList[$row->id]['time_publish'] = _date($spotlightList[$row->id]['time_publish']);
            $spotlightList[$row->id]['time_expire'] = _date($spotlightList[$row->id]['time_expire']);
        }
        // Set paginator
        $count = array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)'));
        $select = $this->getModel('spotlight')->select()->where($whereSpotlight)->columns($count);
        $count = $this->getModel('spotlight')->selectWith($select)->current()->count;
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($this->config('admin_perpage'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(array(
            'router'    => $this->getEvent()->getRouter(),
            'route'     => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
            'params'    => array_filter(array(
                'module'        => $this->getModule(),
                'controller'    => 'spotlight',
                'action'        => 'index',
            )),
        ));
        // Set view
        $this->view()->setTemplate('spotlight-index');
        $this->view()->assign('spotlightList', $spotlightList);
        $this->view()->assign('paginator', $paginator);
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
                $values['time_publish'] = strtotime($values['time_publish']);
                $values['time_expire'] = strtotime($values['time_expire']);
                // Set if new
                if (empty($values['id'])) {
                    // Set user
                    $values['uid'] = Pi::user()->getId();
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
                $values['time_publish'] = date('Y-m-d', $values['time_publish']);
                $values['time_expire'] = date('Y-m-d', $values['time_expire']);
                $form->setData($values);
                $message = 'You can edit this spotlight';
            } else {
                $message = 'You can add new spotlight';
            }
        }
        $this->view()->setTemplate('spotlight-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add a Spotlight'));
        $this->view()->assign('message', $message);
    }

    public function deleteAction()
    {
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