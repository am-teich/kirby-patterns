<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= $pattern->name() ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Pattern description">
  <?= Bnomei\Fingerprint::css($css) ?>
  <?php if($background): ?>
  <style>
    html, body {
      background: <?= $background ?> !important;
    }
  </style>
  <?php endif ?>
</head>
<body <?= $bodyattributes ?>>
  <?= $html ?>
  <?= Bnomei\Fingerprint::js($js) ?>
</body>
</html>