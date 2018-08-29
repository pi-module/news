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
        $this->view()->setTemplate('tools-index');
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
            $where   = ['time_publish > ?' => strtotime($values['start']), 'time_publish < ?' => strtotime($values['end'])];
            $columns = ['id', 'title', 'slug', 'seo_title', 'seo_keywords', 'seo_description'];
            $order   = ['id ASC'];
            $select  = $this->getModel('story')->select()->where($where)->columns($columns)->order($order);
            $rowset  = $this->getModel('story')->selectWith($select);
            // Do rebuild
            switch ($values['rebuild']) {
                case 'slug':
                    foreach ($rowset as $row) {
                        $filter = new Filter\Slug;
                        $slug   = $filter($row->slug);
                        $this->getModel('story')->update(['slug' => $slug], ['id' => $row->id]);
                    }
                    $message = __('Finish Rebuild slug, all story slug update');
                    break;

                case 'slug_title':
                    foreach ($rowset as $row) {
                        $filter = new Filter\Slug;
                        $slug   = $filter($row->title);
                        $this->getModel('story')->update(['slug' => $slug], ['id' => $row->id]);
                    }
                    $message = __('Finish Rebuild slug, all story slug update');
                    break;

                case 'slug_id':
                    foreach ($rowset as $row) {
                        $this->getModel('story')->update(['slug' => $row->id], ['id' => $row->id]);
                    }
                    $message = __('Finish Rebuild slug, all story slug update');
                    break;

                case 'seo_title':
                    foreach ($rowset as $row) {
                        $filter = new Filter\HeadTitle;
                        $title  = $filter($row->title);
                        $this->getModel('story')->update(['seo_title' => $title], ['id' => $row->id]);
                    }
                    $message = __('Finish Rebuild SEO title, all story SEO title update');
                    break;

                case 'seo_keywords':
                    foreach ($rowset as $row) {
                        $filter = new Filter\HeadKeywords;
                        $filter->setOptions(
                            [
                                'force_replace_space' => true,
                            ]
                        );
                        $keywords = $filter($row->title);
                        $this->getModel('story')->update(['seo_keywords' => $keywords], ['id' => $row->id]);
                    }
                    $message = __('Finish Rebuild SEO keywords, all story SEO keywords update');
                    break;

                case 'seo_description':
                    foreach ($rowset as $row) {
                        $filter      = new Filter\HeadDescription;
                        $description = $filter($row->title);
                        $this->getModel('story')->update(['seo_description' => $description], ['id' => $row->id]);
                    }
                    $message = __('Finish Rebuild SEO description, all story SEO description update');
                    break;
            }
        }
        // Set view
        $this->view()->setTemplate('tools-rebuild');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Rebuild stores'));
        $this->view()->assign('message', $message);
    }

    public function pruneAction()
    {
        $form    = new PruneForm('prune', $this->getModule());
        $message = __('You can prune all old stores, from selected topic.');
        if ($this->request->isPost()) {
            // Set form date
            $values = $this->request->getPost();
            // Set where date
            $where = ['time_publish < ?' => strtotime($values['date'])];
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
        $this->view()->setTemplate('tools-prune');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Prune stores'));
        $this->view()->assign('message', $message);
    }

    public function spotlightAction()
    {
        // Delete spotlights
        $where  = ['time_expire < ?' => time()];
        $number = $this->getModel('spotlight')->delete($where);
        if ($number) {
            // Set class
            $message = sprintf(__('<strong>%s</strong> old spotlights removed'), $number);
        } else {
            // Set class
            $message = __('Error in remove old spotlights. perhaps no spotlights exist');
        }
        // Set view
        $this->view()->setTemplate('tools-spotlight');
        $this->view()->assign('title', __('Prune spotlights'));
        $this->view()->assign('message', $message);
    }

    public function pageAction()
    {
        $form    = new PageForm('page');
        $message = __('Remove all topic pages from system page table. and keep just module default pages');
        if ($this->request->isPost()) {
            // Set form date
            $values = $this->request->getPost()->toArray();
            if ($values['confirm']) {
                $where1 = [
                    'section'     => 'front',
                    'module'      => $this->getModule(),
                    'controller'  => 'topic',
                    'action != ?' => 'list',
                ];
                $select = Pi::model('page')->select()->where($where1);
                $rowset = Pi::model('page')->selectWith($select);
                foreach ($rowset as $row) {
                    if (!in_array($row->action, ['list', ''])) {
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
        $this->view()->setTemplate('tools-page');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Remove topic pages'));
        $this->view()->assign('message', $message);
    }

    public function sitemapAction()
    {
        $form    = new SitemapForm('sitemap');
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
        $this->view()->setTemplate('tools-sitemap');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Rebuild sitemap links'));
        $this->view()->assign('message', $message);
    }


    public function mediaAction()
    {
        // Set view
        $this->view()->setTemplate('tools-image');
        $this->view()->assign('title', __('Media'));
    }

    public function migrateMediaAction()
    {

        $msg = Pi::api('story', 'news')->migrateMedia();

        $messenger = $this->plugin('flashMessenger');
        $messenger->addSuccessMessage(__('Media have migrate successfully'));

        $messenger->addWarningMessage($msg);

        $this->redirect()->toRoute(null, ['action' => 'media']);
    }


    /**
     * Generate all media files Main image only
     *
     * @return array
     */
    public function generatePicturesAction()
    {
        $nbPicturesToGenerate = 0;
        $messenger            = $this->plugin('flashMessenger');
        $sizes                = ['thumbnail', 'medium', 'item', 'large'];

        try {
            $storyCollection = Pi::model('story', 'news')->select([]);

            foreach ($storyCollection as $storyEntity) {
                foreach ($sizes as $size) {
                    $mainImage = (string)Pi::api('doc', 'media')->getSingleLinkUrl($storyEntity['main_image'])->setConfigModule('news')->thumb($size);
                    if ($mainImage) {
                        $nbPicturesToGenerate++;
                    }

                    foreach (explode(',', $storyEntity['additional_images']) as $mediaId) {
                        $image = (string)Pi::api('doc', 'media')->getSingleLinkUrl($mediaId)->setConfigModule('news')->thumb($size);
                        if ($image) {
                            $nbPicturesToGenerate++;
                        }
                    }
                }
            }

            $messenger->addSuccessMessage(sprintf(__('%s picture(s) has been generated or already exists'), $nbPicturesToGenerate));

        } catch (Exception $e) {
            $messenger->addErrorMessage($e->getMessage());
        }

        $this->redirect()->toRoute(null, ['action' => 'index']);
    }
}