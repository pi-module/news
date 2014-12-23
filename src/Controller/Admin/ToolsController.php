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
use Pi\Filter;
use Pi\Mvc\Controller\ActionController;
use Module\News\Form\RebuildForm;
use Module\News\Form\PruneForm;
use Module\News\Form\SitemapForm;
use Module\News\Form\PageForm;
use Module\News\Form\RegenerateImageForm;

class ToolsController extends ActionController
{
    public function indexAction()
    {
        // Set template
        $this->view()->setTemplate('tools_index');
    }

    public function rebuildAction()
    {
        // Set message
        $message = __('You can rebuild all your added stores, after rebuild all your old data update to new. And you must set start and end publish time.');
        // Set form
        $form = new RebuildForm('rebuild');
        if ($this->request->isPost()) {
            // Set form date
            $values = $this->request->getPost();
            // Get all story
            $where = array('time_publish > ?' => strtotime($values['start']), 'time_publish < ?' => strtotime($values['end']));
            $columns = array('id', 'title', 'slug', 'seo_title', 'seo_keywords', 'seo_description');
            $order = array('id ASC');
            $select = $this->getModel('story')->select()->where($where)->columns($columns)->order($order);
            $rowset = $this->getModel('story')->selectWith($select);
            // Do rebuild
            switch ($values['rebuild']) {
                case 'slug':
                    foreach ($rowset as $row) {
                        $filter = new Filter\Slug;
                        $slug = $filter($row->slug);
                        $this->getModel('story')->update(array('slug' => $slug), array('id' => $row->id));
                    }
                    $message = __('Finish Rebuild slug, all story slug update');
                    break;

                case 'slug_title':
                    foreach ($rowset as $row) {
                        $filter = new Filter\Slug;
                        $slug = $filter($row->title);
                        $this->getModel('story')->update(array('slug' => $slug), array('id' => $row->id));
                    }
                    $message = __('Finish Rebuild slug, all story slug update');
                    break;

                case 'slug_id':
                    foreach ($rowset as $row) {
                          $this->getModel('story')->update(array('slug' => $row->id), array('id' => $row->id));
                    }
                    $message = __('Finish Rebuild slug, all story slug update');
                    break;

                case 'seo_title':
                    foreach ($rowset as $row) {
                        $filter = new Filter\HeadTitle;
                        $title = $filter($row->title);
                        $this->getModel('story')->update(array('seo_title' => $title), array('id' => $row->id));
                    }
                    $message = __('Finish Rebuild SEO title, all story SEO title update');
                    break;

                case 'seo_keywords':
                    foreach ($rowset as $row) {
                        $keywordsOptions = array('force_replace' => true);
                        $filter = new Filter\HeadKeywords;
                        $filter->setOptions($keywordsOptions);
                        $keywords = $filter($row->title);
                        $this->getModel('story')->update(array('seo_keywords' => $keywords), array('id' => $row->id));
                    }
                    $message = __('Finish Rebuild SEO keywords, all story SEO keywords update');
                    break;    

                case 'seo_description':
                    foreach ($rowset as $row) {
                        $filter = new Filter\HeadDescription;
                        $description = $filter($row->title);
                        $this->getModel('story')->update(array('seo_description' => $description), array('id' => $row->id));
                    }
                    $message = __('Finish Rebuild SEO description, all story SEO description update');
                    break;
            }
        }
        // Set view
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
            $where = array('time_publish < ?' => strtotime($values['date']));
            // Set topics if select
            //if ($values['topic'] && is_array($values['topic'])) {
            //    $where['topic'] = $values['topic'];
            //}
            // Delete storys
            $number = $this->getModel('story')->delete($where);
            $number = $this->getModel('link')->delete($where);
            if ($number) {
                // Set class
                $message = sprintf(__('<strong>%s</strong> old stores removed'), $number);
            } else {
                // Set class
                $message = __('Error in pruned old stores. perhaps no stroy exist whit your select query');
            }
        }
        // Set view
        $this->view()->setTemplate('tools_prune');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Prune stores'));
        $this->view()->assign('message', $message);
    }

    public function spotlightAction()
    {
        // Delete spotlights
        $where = array('time_expire < ?' => time());
        $number = $this->getModel('spotlight')->delete($where);
        if ($number) {
            // Set class
            $message = sprintf(__('<strong>%s</strong> old spotlights removed'), $number);
        } else {
            // Set class
            $message = __('Error in remove old spotlights. perhaps no spotlights exist');
        }
        // Set view
        $this->view()->setTemplate('tools_spotlight');
        $this->view()->assign('title', __('Prune spotlights'));
        $this->view()->assign('message', $message);
    }

    public function pageAction()
    {
        $form = new PageForm('page');
        $message = __('Remove all topic pages from system page table. and keep just module default pages');
        if ($this->request->isPost()) {
            // Set form date
            $values = $this->request->getPost()->toArray();
            if ($values['confirm']) {
                $where1 = array(
                    'section'       => 'front',
                    'module'        => $this->getModule(),
                    'controller'    => 'topic',
                    'action != ?'   => 'list',
                );
                $select = Pi::model('page')->select()->where($where1);
                $rowset = Pi::model('page')->selectWith($select);
                foreach ($rowset as $row) {
                    if (!in_array($row->action, array('list', ''))) {
                        $row->delete();
                    }
                }
                Pi::service('registry')->page->clear($this->getModule());
                $message = __('All other pages removed');
            } else {
                $message = __('No pages were removed');
            }    
        }    
        // Set view
        $this->view()->setTemplate('tools_page');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Remove topic pages'));
        $this->view()->assign('message', $message);
    }

    public function sitemapAction()
    {
        $form = new SitemapForm('sitemap');
        $message = __('Rebuild thie module links on sitemap module tabels');
        if ($this->request->isPost()) {
            // Set form date
            $values = $this->request->getPost()->toArray();
            switch ($values['type']) {
                case '1':
                    Pi::api('story', 'news')->sitemap();
                    Pi::api('topic', 'news')->sitemap();
                    Pi::api('author', 'news')->sitemap();
                    break;

                case '2':
                    Pi::api('story', 'news')->sitemap();
                    break;

                case '3':
                    Pi::api('topic', 'news')->sitemap();
                    break;

                case '4':
                    Pi::api('author', 'news')->sitemap();
                    break;    
            }
            $message = __('Sitemap rebuild finished');
        }    
        // Set view
        $this->view()->setTemplate('tools_sitemap');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Rebuild sitemap links'));
        $this->view()->assign('message', $message);
    }

    public function imageAction()
    {
        $form = new RegenerateImageForm('image');
        $message = __('Regenerate all images by new setting. Please make backup from youe files on upload folder , before use this tools');
        if ($this->request->isPost()) {
            // Set form date
            $values = $this->request->getPost()->toArray();
            switch ($values['type']) {
                case '1':
                    Pi::api('story', 'news')->regenerateImage();
                    Pi::api('topic', 'news')->regenerateImage();
                    Pi::api('author', 'news')->regenerateImage();
                    break;

                case '2':
                    Pi::api('story', 'news')->regenerateImage();
                    break;

                case '3':
                    Pi::api('topic', 'news')->regenerateImage();
                    break;

                case '4':
                    Pi::api('author', 'news')->regenerateImage();
                    break;

            }
            $message = __('Regenerate images finished');
        }    
        // Set view
        $this->view()->setTemplate('tools_image');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Regenerate images'));
        $this->view()->assign('message', $message);
    }
}