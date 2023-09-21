<?php

namespace common\db;

use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ObjectQuery extends ActiveQuery
{
    public function prepare($builder)
    {
        $tableName = $this->modelClass::tableName();
        $this->andWhere(["$tableName.type" => self::getChildClasses($this->modelClass)]);
        return parent::prepare($builder);
    }

    private static function getChildClasses($type)
    {
        $classes = Yii::$app->params['objectclasses'];
        $children = array();
        $children[] = $type;
        foreach ($classes as $class)
        {
            if (is_subclass_of($class, $type))
                $children[] = $class;
        }
        return $children;
    }
}