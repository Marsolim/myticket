<?php

namespace common\models\tickets;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "ticket_action".
 *
 * @property int $id
 * @property int $ticket
 * @property int $engineer_id
 * @property string $action_date
 * @property int $status_override
 * @property string|null $summary
 */
abstract class Status extends \yii\db\ActiveRecord
{
    const COLOR_PRIMARY = 'primary';
    const COLOR_SECONDARY = 'secondary';
    const COLOR_INFO = 'info';
    const COLOR_DANGER = 'danger';
    const COLOR_SUCCESS = 'success';
    const COLOR_WARNING = 'warning';

    const FLAG_SHOW = 1;
    const FLAG_HIDE = 0;

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
    public static function tableName()
    {
        return '{{%status%}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description', 'color_class', 'type'], 'required'],
            [['show'], 'integer'],
            [['show'], 'in', 'range' => [self::FLAG_HIDE, self::FLAG_SHOW]],
            [['color_class'], 'in', 'range' => [self::COLOR_DANGER, self::COLOR_INFO, self::COLOR_PRIMARY, self::COLOR_SECONDARY, self::COLOR_SUCCESS, self::COLOR_WARNING]],
            [['name', 'type', 'description', 'color_class'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'color_class' => 'Color Class',
        ];
    }

    public static function instantiate($row)
    {
        $type = $row['type'];
        return new $type();
    }
}
