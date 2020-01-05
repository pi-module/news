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
use Pi\Paginator\Paginator;
use Module\News\Form\SpotlightForm;
use Module\News\Form\SpotlightFilter;
use Zend\Db\Sql\Predicate\Expression;

class SpotlightController extends ActionController
{
    public function indexAction()
    {
        // Get page
        $page           = $this->params('page', 1);
        $whereSpotlight = [];

        // Get story and topic
        $columns = ['story', 'topic'];
        $select  = $this->getModel('spotlight')->select()->where($whereSpotlight)->columns($columns);
        $idSet   = $this->getModel('spotlight')->selectWith($select)->toArray();

        // Set topics and stores
        $topicArr = [];
        $storyArr = [];
        foreach ($idSet as $spotlight) {
            $topicArr[] = $spotlight['topic'];
            $storyArr[] = $spotlight['story'];
        }

        // Get topics
        $whereTopic = $topicArr ? ['id' => array_unique($topicArr)] : [];
        $columns    = ['id', 'title', 'slug'];
        $select     = $this->getModel('topic')->select()->where($whereTopic)->columns($columns);
        $topicSet   = $this->getModel('topic')->selectWith($select);

        // Make topic list
        foreach ($topicSet as $row) {
            $topicList[$row->id] = $row->toArray();
        }
        $topicList[-1] = [
            'id'    => -1,
            'title' => __('Home Page'),
            'slug'  => '',
        ];
        $topicList[0]  = [
            'id'    => 0,
            'title' => __('All Topics'),
            'slug'  => '',
        ];

        // Get stores
        $whereStory = $storyArr ? ['id' => array_unique($storyArr)] : [];
        $columns    = ['id', 'title', 'slug'];
        $select     = $this->getModel('story')->select()->where($whereStory)->columns($columns);
        $storySet   = $this->getModel('story')->selectWith($select);

        // Make story list
        foreach ($storySet as $row) {
            $storyList[$row->id] = $row->toArray();
        }

        // Get spotlights
        $order        = ['id DESC', 'time_publish DESC'];
        $select       = $this->getModel('spotlight')->select()->where($whereSpotlight)->order($order);
        $spotlightSet = $this->getModel('spotlight')->selectWith($select);

        // Make spotlight list
        $spotlightList = [];
        foreach ($spotlightSet as $row) {
            $spotlightList[$row->id]                      = $row->toArray();
            $spotlightList[$row->id]['storytitle']        = $storyList[$row->story]['title'];
            $spotlightList[$row->id]['storyslug']         = $storyList[$row->story]['slug'];
            $spotlightList[$row->id]['topictitle']        = $topicList[$row->topic]['title'];
            $spotlightList[$row->id]['topicslug']         = $topicList[$row->topic]['slug'];
            $spotlightList[$row->id]['time_publish_view'] = _date($spotlightList[$row->id]['time_publish']);
            $spotlightList[$row->id]['time_expire_view']  = _date($spotlightList[$row->id]['time_expire']);
        }

        // Set paginator
        $count     = ['count' => new Expression('count(*)')];
        $select    = $this->getModel('spotlight')->select()->where($whereSpotlight)->columns($count);
        $count     = $this->getModel('spotlight')->selectWith($select)->current()->count;
        $paginator = Paginator::factory(intval($count));
        $paginator->setItemCountPerPage($this->config('admin_perpage'));
        $paginator->setCurrentPageNumber($page);
        $paginator->setUrlOptions(
            [
                'router' => $this->getEvent()->getRouter(),
                'route'  => $this->getEvent()->getRouteMatch()->getMatchedRouteName(),
                'params' => array_filter(
                    [
                        'module'     => $this->getModule(),
                        'controller' => 'spotlight',
                        'action'     => 'index',
                    ]
                ),
            ]
        );

        // Set view
        $this->view()->setTemplate('spotlight-index');
        $this->view()->assign('spotlightList', $spotlightList);
        $this->view()->assign('paginator', $paginator);
    }

    public function updateAction()
    {
        // Get id
        $id   = $this->params('id');
        $form = new SpotlightForm('topic', $this->getModule());
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new SpotlightFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();

                // Set time
                $values['time_publish'] = strtotime(sprintf('%s 00:00:00', $values['time_publish']));
                $values['time_expire']  = strtotime(sprintf('%s 23:59:59', $values['time_expire']));

                // Set if new
                if (empty($id)) {
                    // Set user
                    $values['uid'] = Pi::user()->getId();
                }

                // Save values
                if (!empty($id)) {
                    $row = $this->getModel('spotlight')->find($id);
                } else {
                    $row = $this->getModel('spotlight')->createRow();
                }
                $row->assign($values);
                $row->save();

                // Check it save or not
                if ($row->id) {
                    $message = __('Spotlight data saved successfully.');
                    $this->jump(['action' => 'index'], $message);
                }
            }
        } else {
            if ($id) {
                $values                 = $this->getModel('spotlight')->find($id)->toArray();
                $values['time_publish'] = date('Y-m-d', $values['time_publish']);
                $values['time_expire']  = date('Y-m-d', $values['time_expire']);
                $form->setData($values);
            }
        }
        $this->view()->setTemplate('spotlight-update');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Add a Spotlight'));
    }

    public function deleteAction()
    {
        // Get information
        $this->view()->setTemplate(false);
        $id  = $this->params('id');
        $row = $this->getModel('spotlight')->find($id);
        if ($row) {
            // Remove page
            $row->delete();
            $this->jump(['action' => 'index'], __('Selected spotlight delete'));
        }
        $this->jump(['action' => 'index'], __('Please select spotlight'));
    }
}