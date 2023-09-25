<?php

namespace common\models\docs;

use common\models\actors\Store;
use common\models\tickets\actions\Action;
use common\models\tickets\Ticket;
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
class Image extends Document
{
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
}
