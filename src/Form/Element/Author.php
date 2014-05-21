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
namespace Module\News\Form\Element;

use Pi;
use Zend\Form\Element\Select;

class Author extends Select
{
    /**
     * @return array
     */
    public function getValueOptions()
    {
        if (empty($this->valueOptions)) {
            if (isset($this->options['list']) && !empty($this->options['list'])) {
                $this->valueOptions = $this->options['list'];
            } else {
                $this->valueOptions = Pi::api('author', 'news')->getFormAuthor();
            }
        }
        return $this->valueOptions;
    }
}