<?php
$hash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plain = $_POST['password'] ?? '';
    if ($plain !== '') {
        $hash = password_hash($plain, PASSWORD_DEFAULT);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Generate Password Hash</title>
  <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body { background-color: #f8f9fa; }
    .container { max-width: 480px; padding-top: 50px; }
    textarea { resize: vertical; }
  </style>
</head>
<body>
  <div class="container">
    <div class="card shadow-sm">
      <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Generate Hash Password</h5>
      </div>
      <div class="card-body">
        <form action="" method="post" novalidate>
          <div class="mb-3">
            <label for="password" class="form-label">Password (teks biasa)</label>
            <input
              type="password"
              id="password"
              name="password"
              class="form-control"
              placeholder="Masukkan password di sini"
              required
            />
          </div>
          <button type="submit" class="btn btn-primary">Generate Hash</button>
        </form>

        <?php if ($hash): ?>
          <hr />
          <div class="mb-3">
            <label for="hash" class="form-label">Hasil Password Hash</label>
            <textarea
              id="hash"
              class="form-control"
              rows="3"
              readonly
            ><?= htmlspecialchars($hash) ?></textarea>
          </div>
          <div class="alert alert-info">
            Salin seluruh teks di atas dan gunakan di kolom <code>password_hash</code> database Anda.
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script src="assets/js/bootstrap.bundle.min.js"></script>
</body>
</html>
