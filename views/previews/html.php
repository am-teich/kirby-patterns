<? use \Kirby\Toolkit\Html; ?>
<div class="toolbar bar">
  <nav class="buttons">
    <?= Html::a('?view=php', 'PHP', ['class' => r($view == 'php', 'active')]) ?>
    <?= Html::a('?view=html', 'HTML', ['class' => r($view == 'html', 'active')]) ?>
    <?= Html::a('?view=htmlpreview', 'HTML Preview', ['class' => r($view == 'htmlpreview', 'active')]) ?>
    <?= Html::a('?view=preview', 'Preview', ['class' => r($view == 'preview', 'active')]) ?>
    <?= Html::a($pattern->url() . '/preview', 'Raw', ['target' => '_blank']) ?>
  </nav>
</div>

<div class="preview">  
  <?= $content ?>
</div>
