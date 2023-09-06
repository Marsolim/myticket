<?php

namespace frontend\models\forms;

use yii\base\Model;
use yii\web\UploadedFile;
use common\models\doc\Document;
use Yii;

class DocumentUploadForm extends Model
{
    public $owner_id;
    public $store_id;
    public $ticket_id;
    public $action_id;
    public $type;

    /**
     * @var UploadedFile[]
     */
    public $files;

    public function rules()
    {
        return [
            [['owner_id', 'store_id', 'ticket_id', 'action_id', 'type'], 'integer'],
            ['owner_id', 'required'],
            ['type', 'default', 'value' => Document::FILE_UNCATEGORIZED],
            ['type', 'in', 'range' => [Document::FILE_INVOICE, Document::FILE_BAP, Document::FILE_SPK, Document::FILE_UNCATEGORIZED]],
            [['files'], 'file', 'skipOnEmpty' => false, 'extensions' => 'pdf, doc, docx, jpg, png', 'maxFiles' => 5],
        ];
    }
    
    public function upload()
    {
        if ($this->validate())
        {
            foreach($this->files as $file){
                $doc = new Document();
                $filename = Yii::$app->security->generateRandomString() . '.' . $file->extension;
                $filepath = 'uploads/documents/' . $filename;
                $file->saveAs($filepath);
                $doc->filename = $filename;
                $doc->uploadname = $file->baseName;
                $doc->owner_id = $this->owner_id;
                $doc->store_id = $this->store_id;
                $doc->ticket_id = $this->ticket_id;
                $doc->action_id = $this->action_id;
                $doc->type = $this->type;
                if ($doc->validate() && $doc->save())
                {
                    continue;
                }
            }          
            return false;
        } 
        else
        {
            return false;
        }
    }
}