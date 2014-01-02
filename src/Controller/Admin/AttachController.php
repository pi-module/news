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
use Pi\File\Transfer\Upload;
use Module\News\Form\AttachForm;
use Module\News\Form\AttachFilter;
use Zend\Json\Json;

class AttachController extends ActionController
{
    protected $FilePrefix = 'attach_';
    protected $attachColumns = array(
        'id', 'title', 'file', 'path', 'story', 'time_create', 'size', 'type', 'status', 'hits'
    );

    public function indexAction()
    {
        // Get page
        $page = $this->params('p', 1);
        $story = $this->params('story');
        $module = $this->params('module');
        // Set info
        $order = array('time_create DESC', 'id DESC');
        $offset = (int)($page - 1) * $this->config('admin_perpage');
        $limit = intval($this->config('admin_perpage'));
        // Get info
        $select = $this->getModel('attach')->select()->order($order)->offset($offset)->limit($limit);
        $rowset = $this->getModel('attach')->selectWith($select);
        // Make list
        foreach ($rowset as $row) {
            $file[$row->id] = $row->toArray();
            $story = $this->getModel('story')->find($file[$row->id]['story'])->toArray();
            $file[$row->id]['time_create'] = _date($file[$row->id]['time_create']);
            $file[$row->id]['storytitle'] = $story['title'];
            $file[$row->id]['preview'] = $this->filePreview($file[$row->id]['type'], $file[$row->id]['path'], $file[$row->id]['file']);
            if ($file[$row->id]['type'] == 'image') {
                $file[$row->id]['link'] = Pi::url('upload/' . $this->config('image_path') . '/original/' . $file[$row->id]['path'] . '/' . $file[$row->id]['file']);
            } else {
                $file[$row->id]['link'] = Pi::url('upload/' . $this->config('file_path') . '/' . $file[$row->id]['type'] . '/' . $file[$row->id]['path'] . '/' . $file[$row->id]['file']);
            }
        }
        // Go to time_update page if empty
        if (empty($file)) {
            $this->jump(array('controller' => 'story', 'action' => 'index'), __('No file attached'));
        }
        // Set paginator
        $select = $this->getModel('attach')->select()->columns(array('count' => new \Zend\Db\Sql\Predicate\Expression('count(*)')))->where($where);
        $count = $this->getModel('attach')->selectWith($select)->current()->count;
        $paginator = \Pi\Paginator\Paginator::factory(intval($count));
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
                'controller' => 'attach',
                'action' => 'index',
            ),
        ));
        // Set view
        $this->view()->setTemplate('attach_index');
        $this->view()->assign('files', $file);
        $this->view()->assign('paginator', $paginator);
    }

    public function addAction()
    {
        // Get id
        $id = $this->params('id');
        if (empty($id)) {
            $this->jump(array('controller' => 'story', 'action' => 'index'), __('You must select story'));
        }
        // Get story
        $story = $this->getModel('story')->find($id)->toArray();
        if (empty($story)) {
            $this->jump(array('controller' => 'story', 'action' => 'index'), __('Your selected story not exist'));
        }
        // Get all attach files
        $select = $this->getModel('attach')->select()->where(array('story' => $story['id']));
        $rowset = $this->getModel('attach')->selectWith($select);
        // Make list
        $contents = array();
        foreach ($rowset as $row) {
            $content[$row->id] = $row->toArray();
            $content[$row->id]['time_create'] = _date($content[$row->id]['time_create']);
            $content[$row->id]['preview'] = $this->filePreview($content[$row->id]['type'], $content[$row->id]['path'], $content[$row->id]['file']);
            $contents[] = $content[$row->id];
        }
        // Set view
        $this->view()->setTemplate('attach_add');
        $this->view()->assign('content', Json::encode($contents));
        $this->view()->assign('story', $story);
        $this->view()->assign('title', sprintf(__('Attach files to %s'), $story['title']));
    }

    public function editAction()
    {
        // Get id
        $id = $this->params('id');
        $module = $this->params('module');
        if (empty($id)) {
            $this->jump(array('action' => 'index'), __('You must select file'));
        }
        //Get file and story
        $file = $this->getModel('attach')->find($id)->toArray();
        $file['view'] = $this->fileView($file['type'], $file['path'], $file['file']);
        $story = $this->getModel('story')->find($file['story'])->toArray();
        // Set form
        $form = new AttachForm('attach', $module, array($story['id'] => $story['title']));
        $form->setAttribute('enctype', 'multipart/form-data');
        if ($this->request->isPost()) {
            $data = $this->request->getPost();
            $form->setInputFilter(new AttachFilter);
            $form->setData($data);
            if ($form->isValid()) {
                $values = $form->getData();
                foreach (array_keys($values) as $key) {
                    if (!in_array($key, $this->attachColumns)) {
                        unset($values[$key]);
                    }
                }
                $row = $this->getModel('attach')->find($values['id']);
                $row->assign($values);
                $row->save();
                $message = __('All changes in file saved successfully.');
                $this->jump(array('action' => 'index'), $message);
            } else {
                $message = __('Invalid data, please check and re-submit.');
            }
        } else {
            $form->setData($file);
            $message = 'You can edit this File';
        }
        // Set view
        $this->view()->setTemplate('attach_edit');
        $this->view()->assign('form', $form);
        $this->view()->assign('title', __('Edit file'));
        $this->view()->assign('message', $message);
        $this->view()->assign('file', $file);
        $this->view()->assign('story', $story);
    }

    public function uploadAction()
    {
        // deactive log
        Pi::service('log')->active(false);
        // Set return
        $return = array(
            'status' => 1, 'message' => '', 'id' => '', 'title' => '', 'time_create' => '',
            'type' => '', 'status' => '', 'hits' => '', 'size' => '', 'preview' => '',
        );
        // Get id
        $id = $this->params('id');
        if (empty($id)) {
            $return = array(
                'status' => 0,
                'message' => __('You must select story'),
            );
        } else {
            // Get story
            $story = $this->getModel('story')->find($id)->toArray();
            if (empty($story)) {
                $return = array(
                    'status' => 0,
                    'message' => __('Your selected story not exist'),
                );
            } else {
                // start upload
                $path = date('Y') . '/' . date('m');
                $uploader = new Upload(array('destination' => $this->config('file_path') . '/file/' . $path, 'rename' => $this->FilePrefix . '%random%'));
                $uploader->setExtension($this->config('file_extension'));
                $uploader->setSize($this->config('file_size'));
                if ($uploader->isValid()) {
                    $uploader->receive();
                    // Set info
                    $file = $uploader->getUploaded('file');
                    $type = $this->fileType($file);
                    $this->filePath($type, $path, $file);
                    // Set save array
                    $values['file'] = $file;
                    $values['title'] = '';
                    $values['path'] = $path;
                    $values['story'] = $story['id'];
                    $values['time_create'] = time();
                    $values['type'] = $type;
                    $values['status'] = 1;
                    $values['size'] = '';
                    // save in DB
                    $row = $this->getModel('attach')->createRow();
                    $row->assign($values);
                    $row->save();
                    // Set erturn array
                    $return['id'] = $row->id;
                    $return['title'] = $row->title;
                    $return['time_create'] = _date($row->time_create);
                    $return['type'] = $row->type;
                    $return['status'] = $row->status;
                    $return['size'] = $row->size;
                    $return['preview'] = $this->filePreview($row->type, $row->path, $row->file);
                    // Set Story Attach count
                    Pi::service('api')->news(array('Story', 'AttachCount'), $row->story);
                } else {
                    // Upload error
                    $messages = $uploader->getMessages();
                    $return = array(
                        'status' => 0,
                        'message' => implode('; ', $messages),
                    );
                }
            }
        }
        $this->view()->setTemplate(false)->setLayout('layout-content');
        return Json::encode($return);
    }

    public function deleteAction()
    {
        $id = $this->params('id');
        if ($id) {
            $row = $this->getModel('attach')->find($id);
            $this->removeFile($row->file, $row->type, $row->path);
            $row->delete();
            $ajaxstatus = 1;
            $message = __('Your attached file remove successfully');
        } else {
            $ajaxstatus = 0;
            $message = __('Please select file');
        }

        return array(
            'status' => $ajaxstatus,
            'message' => $message,
        );
    }

    protected function fileType($file)
    {
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        switch ($extension) {
            case 'zip':
            case 'rar':
            case 'tar':
                $type = 'archive';
                break;

            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
                $type = 'image';
                break;

            case 'avi':
            case 'flv':
            case 'mp4':
            case 'webm':
            case 'ogv':
                $type = 'video';
                break;

            case 'mp3':
            case 'ogg':
                $type = 'audio';
                break;

            case 'pdf':
                $type = 'pdf';
                break;

            case 'doc':
            case 'docx':
                $type = 'doc';
                break;

            default:
                $type = 'other';
                break;
        }
        // return
        return $type;
    }

    protected function filePath($type, $path, $file)
    {
        $oldPath = Pi::path('upload/' . $this->config('file_path') . '/file/' . $path . '/' . $file);
        switch ($type) {
            // Move and resize images
            case 'image':
                $newPach = Pi::path('upload/' . $this->config('image_path') . '/original/' . $path . '/' . $file);
                Pi::service('file')->copy($oldPath, $newPach);
                Pi::service('file')->remove($oldPath);
                Pi::service('api')->news(array('Resize', 'start'), $file, $this->config('image_path') . '/original/' . $path, $this->config('image_path') . '/large/' . $path, $this->config('image_largew'), $this->config('image_largeh'));
                Pi::service('api')->news(array('Resize', 'start'), $file, $this->config('image_path') . '/original/' . $path, $this->config('image_path') . '/medium/' . $path, $this->config('image_mediumw'), $this->config('image_mediumh'));
                Pi::service('api')->news(array('Resize', 'start'), $file, $this->config('image_path') . '/original/' . $path, $this->config('image_path') . '/thumb/' . $path, $this->config('image_thumbw'), $this->config('image_thumbh'));
                break;

            // Move video, audio, pdf, doc
            case 'video':
            case 'audio':
            case 'pdf':
            case 'doc':
                $newPach = Pi::path('upload/' . $this->config('file_path') . '/' . $type . '/' . $path . '/' . $file);
                Pi::service('file')->copy($oldPath, $newPach);
                Pi::service('file')->remove($oldPath);
                break;
        }
    }

    protected function filePreview($type, $path, $file)
    {
        if ($type == 'image') {
            $preview = Pi::url('upload/' . $this->config('image_path') . '/thumb/' . $path . '/' . $file);
        } else {
            $image = 'image/' . $type . '.png';
            $preview = Pi::service('asset')->getModuleAsset($image, $this->getModule());
        }
        return $preview;
    }

    protected function fileView($type, $path, $file)
    {
        if ($type == 'image') {
            $view = Pi::url('upload/' . $this->config('image_path') . '/thumb/' . $path . '/' . $file);
        } else {
            $view = Pi::url('upload/' . $this->config('file_path') . '/' . $type . '/' . $path . '/' . $file);
        }
        return $view;
    }

    protected function removeFile($file, $type, $path)
    {
        if ($type == 'image') {
            $file = array(
                Pi::path('upload/' . $this->config('image_path') . '/original/' . $path . '/' . $file),
                Pi::path('upload/' . $this->config('image_path') . '/large/' . $path . '/' . $file),
                Pi::path('upload/' . $this->config('image_path') . '/medium/' . $path . '/' . $file),
                Pi::path('upload/' . $this->config('image_path') . '/thumb/' . $path . '/' . $file),
            );
        } else {
            $file = Pi::path('upload/' . $this->config('file_path') . '/' . $type . '/' . $path . '/' . $file);
        }
        Pi::service('file')->remove($file);
    }
}