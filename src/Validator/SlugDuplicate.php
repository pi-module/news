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

namespace Module\News\Validator;

use Pi;
use Laminas\Validator\AbstractValidator;

class SlugDuplicate extends AbstractValidator
{
    const TAKEN = 'slugExists';

    /**
     * @var array
     */
    protected $messageTemplates
        = [
            self::TAKEN => 'This slug already exists',
        ];

    protected $options
        = [
            'module', 'table', 'id',
        ];

    /**
     * Slug validate
     *
     * @param mixed $value
     * @param array $context
     *
     * @return boolean
     */
    public function isValid($value, $context = null)
    {
        $this->setValue($value);
        if (null !== $value) {
            $where = ['slug' => $value];
            if (!empty($this->options['id'])) {
                $where['id <> ?'] = $this->options['id'];
            }
            $rowset = Pi::model($this->options['table'], $this->options['module'])->select($where);
            if ($rowset->count()) {
                $this->error(static::TAKEN);
                return false;
            }
        }
        return true;
    }
}