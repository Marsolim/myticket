<?php

namespace common\models\docs;

use common\db\AuditedRecord;
use common\models\actors\Store;
use common\models\tickets\Action;
use common\models\tickets\Ticket;
use yii\web\UploadedFile;
use Yii;

/**
 * This is the model class for table "region".
 *
 * @property int $id
 * @property string $name
 * @property string $code
 *
 * @property Store[] $stores
 */
abstract class Document extends AuditedRecord
{
    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%document%}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['number', 'type'], 'required'],
            [['filename', 'uploadname', 'type'], 'string', 'max' => 255],
            [['file_type', 'number'], 'string', 'max' => 50],
            [['ticket_id', 'store_id', 'action_id', 'file_size'], 'integer'],
            [['file'], 'file', 'maxSize' => 1024*1024*2, 'skipOnEmpty' => false, 'extensions' => 'pdf, doc, docx, jpg, png'],
            [['ticket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Ticket::class, 'targetAttribute' => ['ticket_id' => 'id']],
            [['store_id'], 'exist', 'skipOnError' => true, 'targetClass' => Store::class, 'targetAttribute' => ['store_id' => 'id']],
            [['action_id'], 'exist', 'skipOnError' => true, 'targetClass' => Action::class, 'targetAttribute' => ['action_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'uploadname' => 'Name',
            'number' => 'Doc. Number',
        ];
    }

    public static function instantiate($row)
    {
        $type = $row['type'];
        return new $type();
    }

    public function init()
    {
        $this->type = self::class;
        parent::init();
    }

    public function beforeSave($insert)
    {
        $this->type = self::class;
        return parent::beforeSave($insert);
    }

    /**
     * Gets query for [[Action]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAction()
    {
        return $this->hasOne(Action::class, ['action_id' => 'id']);
    }

    /**
     * Gets query for [[Ticket]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::class, ['ticket_id' => 'id']);
    }

    /**
     * Gets query for [[Store]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStore()
    {
        return $this->hasOne(Store::class, ['store_id' => 'id']);
    }

    public function getFileIcon()
    {
        $icons = [
            'doc' => function($file) {
                if (preg_match("/^.*\.(doc|docx)$/i", $file))
                {
                    return '<i class="fas fa-file-word text-primary"></i>';
                }
            },
            'xls' => function($file) {
                if (preg_match("/^.*\.(xls|xlsx)$/i", $file))
                {
                    return'<i class="fas fa-file-excel text-success"></i>';
                }
            },
            'ppt' => function($file) {
                if (preg_match("/^.*\.(ppt|pptx)$/i", $file))
                {
                    return '<i class="fas fa-file-powerpoint text-primary"></i>';
                }
            },
            'pdf' => function($file) {
                if (preg_match("/^.*\.(pdf)$/i", $file))
                {
                    return '<i class="fas fa-file-pdf text-primary"></i>';
                }
            },
            'zip' => function($file) {
                if (preg_match("/^.*\.(zip|rar|tar|gzip|gz|7z)$/i", $file))
                {
                    return '<i class="fas fa-file-archive text-primary"></i>';
                }
            },
            'htm' => function($file) {
                if (preg_match("/^.*\.(htm|html)$/i", $file))
                {
                    return '<i class="fas fa-file-code text-primary"></i>';
                }
            },
            'txt' => function($file) {
                if (preg_match("/^.*\.(txt|ini|csv|java|php|js|css)$/i", $file))
                {
                    return '<i class="fas fa-file-alt text-primary"></i>';
                }
            },
            'mov' => function($file) {
                if (preg_match("/^.*\.(avi|mpg|mkv|mov|mp4|3gp|webm|wmv)$/i", $file))
                {
                    return '<i class="fas fa-file-video text-primary"></i>';
                }
            },
            'mp3' => function($file) {
                if (preg_match("/^.*\.(mp3|wav)$/i", $file))
                {
                    return '<i class="fas fa-file-audio text-primary"></i>';
                }
            },
            'image' => function($file) {
                if (preg_match("/^.*\.(jpg|jpeg|png|gif)$/i", $file))
                {
                    return '<i class="fas fa-file-image text-primary"></i>';
                }
            },
        ];
        foreach($icons as $icon)
        {
            $result = $icon($this->filename);
            if ($result) return $result;
            continue;
        }
    }

    public function upload($runValidation = true, $attributeNames = null) {
        $filename = Yii::$app->security->generateRandomString() . '.' . $this->file->extension;
        $filepath = "uploads/documents/$filename";
        $this->file->saveAs($filepath);
        $this->filename = $filename;
        $this->uploadname = $this->file->name;
        $this->file_size = $this->file->size;
        $this->file_type = $this->file->extension;
        return $this->save($runValidation, $attributeNames);
    }
}
