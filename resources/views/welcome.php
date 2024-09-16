<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome</title>
</head>

<body>
  <h1>Welcome to simple_php, where building your app becomes easy</h1>
  <h2>Go to <a href="<?= route('hphp.index', ['testvalue' => "Execution time = '" . date('d-m-Y H:i:s') . "'"]) ?>">HPHP file</a></h2>
</body>

</html>