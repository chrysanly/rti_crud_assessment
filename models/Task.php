<?php

namespace app\models;

use yii\db\ActiveRecord;

class Task extends ActiveRecord
{
    public static function tableName()
    {
        return 'tasks';
    }

    public function rules()
    {
        return [
            [['title'], 'required'],
            [['description'], 'string'],
            [['status'], 'in', 'range' => ['pending', 'in_progress', 'completed']],
            [['priority'], 'in', 'range' => ['low', 'medium', 'high']],
            [['is_deleted'], 'boolean'],
            [['due_date'], 'date', 'format' => 'php:Y-m-d'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'description' => 'Description',
            'status' => 'Status',
            'priority' => 'Priority',
            'is_deleted' => 'Is Deleted',
            'due_date' => 'Due Date',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Get filtered and paginated tasks.
     *
     * @param array $params
     * @return array
     */
    public static function getTasks(array $params = []): array
    {
        $query = self::find();

        // Handle deleted/active filter
        if (!empty($params['showDeleted']) && empty($params['hideDeleted'])) {
            $query->where(['is_deleted' => true]);
        } elseif (empty($params['showDeleted']) && !empty($params['hideDeleted'])) {
            $query->where(['is_deleted' => false]);
        } else {
            $query->andWhere(['is_deleted' => false]);
        }

        // Filtering
        if (!empty($params['status'])) {
            $query->andWhere(['status' => $params['status']]);
        }

        if (!empty($params['priority'])) {
            $query->andWhere(['priority' => $params['priority']]);
        }

        if (!empty($params['from']) && !empty($params['to'])) {
            $query->andWhere(['between', 'due_date', $params['from'], $params['to']]);
        }

        if (!empty($params['keyword'])) {
            $query->andFilterWhere(['like', 'title', $params['keyword']]);
        }

        // Sorting
        $sort = $params['sort'] ?? 'created_at';
        $order = strtoupper($params['order'] ?? 'DESC');
        $query->orderBy([$sort => $order === 'ASC' ? SORT_ASC : SORT_DESC]);

        // Pagination
        $page = (int) ($params['page'] ?? 0);
        $limit = (int) ($params['limit'] ?? 10);

        $count = $query->count();
        $totalPages = (int) ceil($count / $limit);

        $models = $query->offset($page * $limit)->limit($limit)->all();

        return [
            'items' => $models,
            '_meta' => [
                'totalCount' => $count,
                'pageCount' => $totalPages,
                'currentPage' => $page,
                'perPage' => $limit,
            ],
        ];
    }

    public function getTags()
    {
        return $this->hasMany(Tag::class, ['id' => 'tag_id'])
                    ->viaTable('task_tag', ['task_id' => 'id']);
    }

    public function getTagNames()
    {
        return implode(', ', array_column($this->tags, 'name'));
    }
}