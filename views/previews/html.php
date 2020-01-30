<? use \Kirby\Toolkit\Html; ?>
<div class="toolbar bar">
  <nav class="buttons">
    <?= Html::a($pattern->patternuipath() . '/' . $file->filename() . '?view=twig', 'Twig', ['class' => r($view == 'twig', 'active')]) ?>
    <?= Html::a($pattern->patternuipath() . '/' . $file->filename() . '?view=htmlpreview', 'HTML', ['class' => r($view == 'htmlpreview', 'active')]) ?>
    <?= Html::a($pattern->patternuipath() . '?view=preview', 'Preview', ['class' => r($view == 'preview', 'active')]) ?>
    <?= Html::a($pattern->url() . '/preview', 'Raw', ['target' => '_blank']) ?>
  </nav>

  <?php if ($view == 'preview'): ?>
  <nav class="buttons buttons--right sg-size-options">
    <input type="text" class="sg-input sg-size-px" value="320">
    <div class="label">px</div>
    <input type="text" class="sg-input sg-size-em" value="20">
    <div class="label">em</div>
    <?= Html::a('#', 'S', [ 'id' => 'sg-size-s']) ?>
    <?= Html::a('#', 'M', [ 'id' => 'sg-size-m']) ?>
    <?= Html::a('#', 'L', [ 'id' => 'sg-size-l']) ?>
    <?= Html::a('#', 'Full', [ 'id' => 'sg-size-full']) ?>
    <?= Html::a('#', 'Hay!', [ 'id' => 'sg-size-hay']) ?>
  </nav>
  <?php endif ?>
</div>

<div class="preview">

  <?php if ($view == 'preview'): ?>
  <div id="sg-vp-wrap">
    <div id="sg-cover"></div>
    <div id="sg-gen-container">
      <div id="sg-viewport">
  <?php endif ?>
      <?= $content ?>

  <?php if ($view == 'preview'): ?>
      </div>
      <div id="sg-rightpull-container">
        <div id="sg-rightpull"></div>
      </div>
    </div>
  </div>
  <?php endif ?>
</div>
