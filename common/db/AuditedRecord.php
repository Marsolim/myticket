<?php

namespace common\db;

use common\db\ObjectQuery;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

/**
 * @property string $type Class type of object
 */

abstract class AuditedRecord extends ObjectRecord
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class,
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

    public static function instantiate($row)
    {
        $type = $row['type'];
        return new $type();
    }

    public static function find()
    {
        //echo VarDumper::dump(get_called_class());
        return new ObjectQuery(get_called_class());
    }
}