<?php

namespace common\db;

use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

class ActionQuery extends ActiveQuery
{
    public $type;
    public $tableName;

    public function prepare($builder)
    {
        if ($this->type !== null) {
            $this->andWhere(["$this->tableName.type" => self::getChildClasses($this->type)]);
        }
        $this->with('ticket');
        return parent::prepare($builder);
    }

    private static function getChildClasses($type)
    {
        $query = new Query();
        //$query->from('object_types')->select('type')->all();
        $classes = ArrayHelper::getColumn($query->from('object_types')->select('type')->all(), 'type');
        $children = array();
        foreach ($classes as $class)
        {
            if (is_subclass_of($class, $type))
                $children[] = $class;
        }
        return $children;
    }
}