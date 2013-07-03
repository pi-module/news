<?php
/**
 * News admin tools controller
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
use Module\News\Form\RebuildForm;
use Module\News\Form\PruneForm;

class ToolsController extends ActionController
{
    public function indexAction()
    {
        // Set template
        $this->view()->setTemplate('tools_index');
    }

    public function writerAction()
    {
        $select = $this->getModel('story')->select()->columns(array('author'))->group('author');
        $rowset = $this->getModel('story')->selectWith($select)->toArray();
        foreach ($rowset as $row) {
            $select = $this->getModel('story')->select()->columns(array('id'))->where(array('author' => $row['author']));
            $rowset = $this->getModel('story')->selectWith($select);
            $writers[] = Pi::service('api')->news(array('Writer', 'Reset'), $row['author'], $rowset->count());
        }
        // Set view
        $this->view()->setTemplate('tools_writer');
        $this->view()->assign('writers', $writers);
        $this->view()->assign('title', __('Reset write info'));
        $this->view()->assign('message', __('All information saved'));
    }

    public function rebuildAction()
    {
        $form = new RebuildForm('rebuild');
        $message = __('You can rebuild all your added stores, after rebuild all your old data update to new.
		                 And you must set start and end publish time.');
        if ($this->request->isPost()) {
            // Set form date
            $values = $this->request->getPost();
            // Get all story
            $where = array('publish > ?' => strtotime($values['start']), 'publish < ?' => strtotime($values['end']));
            $columns = array('id', 'title', 'alias', 'keywords', 'description');
            $order = array('id ASC');
            $select = $this->getModel('story')->select()->where($where)->columns($columns)->order($order);
            $rowset = $this->getModel('story')->selectWith($select);
            // Do rebuild
            switch ($values['rebuild']) {
                case 'alias':
                    foreach ($rowset as $row) {
                        $values['alias'] = $this->alias($row->title, $row->id, $this->getModel('story'));
                        $this->getModel('story')->update(array('alias' => $alias), array('id' => $row->id));
                    }
                    $message = __('Finish Rebuild Alias, all story alias update');
                    break;

                case 'keywords':
                    foreach ($rowset as $row) {
                        $keywords = $this->meta()->keywords($row->title);
                        $this->getModel('story')->update(array('keywords' => $keywords), array('id' => $row->id));
                    }
                    $message = __('Finish Rebuild Meta keywords, all story Meta keywords update');
                    break;

                case 'description':
                    foreach ($rowset as $row) {
                        $description = $this->meta()->description($row->title);
                        $this->getModel('story')->update(array('description' => $description), array('id' => $row->id));
                    }
                    $message = __('Finish Rebuild Meta description, all story Meta description update');
                    break;
            }
        }
        $this->view()->setTemplate('tools_rebuild');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Rebuild stores'));
        $this->view()->assign('message', $message);
    }

    public function pruneAction()
    {
        $form = new PruneForm('prune', $this->getModule());
        $message = __('You can prune all old stores, from selected topic.');
        if ($this->request->isPost()) {
            // Set form date
            $values = $this->request->getPost();
            // Set where date
            $where = array('publish < ?' => strtotime($values['date']));
            // Set topics if select
            if ($values['topic'] && is_array($values['topic'])) {
                $where['topic'] = $values['topic'];
            }
            // Delete storys
            $number = $this->getModel('story')->delete($where);
            if ($number) {
                // Set class
                $message = sprintf(__('<strong>%s</strong> old stores removed'), $number);
            } else {
                // Set class
                $message = __('Error in pruned old stores. perhaps no stroy exist whit your select query');
            }
        }
        $this->view()->setTemplate('tools_prune');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Prune stores'));
        $this->view()->assign('message', $message);
    }

    public function spotlightAction()
    {
        // Delete spotlights
        $where = array('expire < ?' => time());
        $number = $this->getModel('spotlight')->delete($where);
        if ($number) {
            // Set class
            $message = sprintf(__('<strong>%s</strong> old spotlights removed'), $number);
        } else {
            // Set class
            $message = __('Error in remove old spotlights. perhaps no spotlights exist');
        }
        $this->view()->setTemplate('tools_spotlight');
        $this->view()->assign('title', __('Prune spotlights'));
        $this->view()->assign('message', $message);
    }
}