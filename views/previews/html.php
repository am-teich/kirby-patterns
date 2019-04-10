<?
    use \Kirby\Toolkit\Html;
    use Kirby\Patterns\Lab;

    $baseUrl = $pattern->url() . '/' . $file->filename();
?>
<div class="toolbar bar">
  <nav class="buttons">
    <?= Html::a($baseUrl . '?view=php', 'PHP', ['class' => r($view == 'php', 'active')]) ?>
    <?= Html::a($baseUrl . '?view=html', 'HTML', ['class' => r($view == 'html', 'active')]) ?>
    <?
        if (Lab::$mode !== 'test') {
            echo Html::a($baseUrl . '?view=htmlpreview', 'HTML Preview', ['class' => r($view == 'htmlpreview', 'active')]);
        }
    ?>
    <?= Html::a($baseUrl . '?view=preview', 'Preview', ['class' => r($view == 'preview', 'active')]) ?>
    <?
        if (Lab::$mode === 'test') {
            echo Html::a($pattern->url() . '/test', 'Raw', ['target' => '_blank']);
        } else {
            echo Html::a($pattern->url() . '/preview', 'Raw', ['target' => '_blank']);
        }
    ?>
  </nav>
</div>

<div class="preview">  
  <?= $content ?>
</div>
