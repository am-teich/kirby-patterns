<div class="info">
  <ul class="nav">
    <?php foreach($pattern->files() as $file): ?>
    <li><?= Html::a($pattern->url() . '/' . $file->filename(), $file->filename(), ['class' => ($currentFile && $currentFile->filename() == $file->filename()) ? 'active' : '']) ?></li>
    <?php endforeach ?>
  </ul>
</div>
