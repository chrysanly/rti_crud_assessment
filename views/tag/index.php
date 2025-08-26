<?php
use yii\helpers\Html;

$this->title = 'Tags';
?>
<h1><?= Html::encode($this->title) ?></h1>

<p>
    <?= Html::a('Create Tag', ['create'], ['class' => 'btn btn-success']) ?>
</p>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="tagTableBody">
        <?php if (!empty($tags)): ?>
            <?php foreach ($tags as $tag): ?>
                <tr>
                    <td><?= $tag->id ?></td>
                    <td><?= Html::encode($tag->name) ?></td>
                    <td>
                        <?= Html::a('Edit', ['update', 'id' => $tag->id], ['class' => 'btn btn-sm btn-warning']) ?>
                        <?= Html::a('Delete', ['delete', 'id' => $tag->id], [
                            'class' => 'btn btn-sm btn-danger',
                            'data-method' => 'post',
                            'data-confirm' => 'Are you sure?'
                        ]) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="3" class="text-center">No tags available.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
