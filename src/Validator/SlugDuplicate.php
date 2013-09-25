<?php
/**
 * News slug validator
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
 * @author          Taiwen Jiang <taiwenjiang@tsinghua.org.cn>
 * @author          Hossein Azizabadi <azizabadi@faragostaresh.com>
 * @since           3.0
 * @package         Module\News
 * @version         $Id$
 */

namespace Module\News\Validator;

use Pi;
use Zend\Validator\AbstractValidator;

class SlugDuplicate extends AbstractValidator
{
    const TAKEN        = 'slugExists';

    /**
     * @var array
     */
    protected $messageTemplates = array(
        self::TAKEN     => 'This slug already exists',
    );

    /**
     * Page slug validate
     *
     * @param  mixed $table
     * @param  mixed $value
     * @param  int   $id
     * @return boolean
     */
    public function isValid($table, $value, $id = null)
    {
        $module = Pi::service('module')->current();
        $this->setValue($value);
        if (null !== $value) {
            $where = array('slug' => $value);
            if (!empty($id)) {
                $where['id <> ?'] = $id;
            }
            $rowset = Pi::model($table, $module)->select($where);
            if ($rowset->count()) {
                $this->error(static::TAKEN);
                return false;
            }
        }
        return true;
    }
}
