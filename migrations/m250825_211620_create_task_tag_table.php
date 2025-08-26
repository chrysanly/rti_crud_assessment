<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%task_tag}}`.
 */
class m250825_211620_create_task_tag_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%task_tag}}', [
            'task_id' => $this->integer()->notNull(),
            'tag_id' => $this->integer()->notNull(),
            'PRIMARY KEY(task_id, tag_id)',
        ]);

        // creates index and foreign key for task_id
        $this->createIndex(
            '{{%idx-task_tag-task_id}}',
            '{{%task_tag}}',
            'task_id'
        );
        $this->addForeignKey(
            '{{%fk-task_tag-task_id}}',
            '{{%task_tag}}',
            'task_id',
            '{{%tasks}}',
            'id',
            'CASCADE'
        );

        // creates index and foreign key for tag_id
        $this->createIndex(
            '{{%idx-task_tag-tag_id}}',
            '{{%task_tag}}',
            'tag_id'
        );
        $this->addForeignKey(
            '{{%fk-task_tag-tag_id}}',
            '{{%task_tag}}',
            'tag_id',
            '{{%tag}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('{{%fk-task_tag-task_id}}', '{{%task_tag}}');
        $this->dropIndex('{{%idx-task_tag-task_id}}', '{{%task_tag}}');

        $this->dropForeignKey('{{%fk-task_tag-tag_id}}', '{{%task_tag}}');
        $this->dropIndex('{{%idx-task_tag-tag_id}}', '{{%task_tag}}');

        $this->dropTable('{{%task_tag}}');
    }
}
