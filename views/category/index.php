<?php

use yii\helpers\Html;
use app\models\Category;
use yii\widgets\Pjax;

$this->title = 'Categories';
// $this->params['breadcrumbs'][] = $this->title;
?>

<div class="category-index">
    <?php Pjax::begin(['id' => 'nested-set']); ?>
        <div id="menu">
            <?= Category::getNestedSet(); ?>
        </div>
    <?php Pjax::end(); ?>

</div>
