<?php

namespace common\db;

use common\db\ObjectQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * @property string $type Class type of object
 */

abstract class ObjectRecord extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type'], 'required'],
            [['type'], 'string', 'max' => 255],
        ];
    }

    public function init()
    {
        $this->type = $this::class;
    }

    public function beforeSave($insert)
    {
        $this->type = $this::class;
        parent::beforeSave($insert);
    }

    public static function instantiate($row)
    {
        $type = $row['type'];
        return new $type();
    }

    public static function find()
    {
        return new ObjectQuery(get_called_class(), ['type' => get_called_class(), 'tableName' => get_called_class()::tableName()]);
    }
}