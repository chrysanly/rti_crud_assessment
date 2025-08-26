<?php
namespace app\models;

use yii\db\ActiveRecord;

class Tag extends ActiveRecord
{
    public static function tableName()
    {
        return 'tag';
    }

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 50],
            [['name'], 'unique'],
        ];
    }

    public function getTasks()
    {
        return $this->hasMany(Task::class, ['id' => 'task_id'])
                    ->viaTable('task_tag', ['tag_id' => 'id']);
    }
}
