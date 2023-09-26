<?php

namespace common\db;

use common\db\ObjectQuery;
use ReflectionClass;
use Yii;
use yii\base\UnknownClassException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\VarDumper;

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
        $this->type = get_called_class();
    }

    public function beforeSave($insert)
    {
        $this->type = get_called_class();
        return parent::beforeSave($insert);
    }

    public static function instantiate($row)
    {
        $type = $row['type'];
        $rtype = new ReflectionClass($row['type']);
        $type = self::getFirstNonAbstractChildClass($rtype);
        if (!empty($type)) {
            $type = $type->getName();
            return new $type();
        }
        throw new UnknownClassException("Class of ".$row['type'].' is either not defined or registered in object class parameter.');
    }

    private static function getFirstNonAbstractChildClass(ReflectionClass $type)
    {
        if (!$type->isAbstract()) return $type;
        $classes = Yii::$app->params['objectclasses'];
        foreach ($classes as $class)
        {
            $class = new ReflectionClass($class);
            if ($class->isSubclassOf($type))
                return $class;
        }
        return null;
    }

    public static function find()
    {
        //echo VarDumper::dump(get_called_class());
        return new ObjectQuery(get_called_class());
    }
}