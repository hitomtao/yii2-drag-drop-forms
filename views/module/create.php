<?php
use yii\helpers\Html;
use masihfathi\form\FormBuilder;

$this->title = 'Form generator Yii2';
$this->params['breadcrumbs'][] = ['label' => Yii::t('builder', 'All forms') , 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('builder', 'Your forms') , 'url' => ['user']];
$this->params['breadcrumbs'][] = Yii::t('builder', 'Form create');
?>

<h1 class="header"><?= Yii::t('builder', 'Form Builder') ?></h1>

<?= FormBuilder::widget([
		'test_mode' => $testMode ?? false,
		'easy_mode' => $easyMode ?? true
]);

?>
