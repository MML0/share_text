<?php
// sharedpad.php

$file = __DIR__ . "/shared_text.txt";

// Create file if not exists
if (!file_exists($file)) {
    file_put_contents($file, "Welcome! This is a shared pad.\nEdit and hit Save.");
}

// Quick check: only return filesize (for JS polling)
if (isset($_GET['filesize'])) {
    echo filesize($file);
    exit;
}

// Handle save
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['content'])) {
    file_put_contents($file, $_POST['content']);
    header("Location: " . $_SERVER['PHP_SELF']); // reload to prevent resubmission
    exit;
}

// Load current content
$content = file_get_contents($file);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>🍏 Shared Pad</title>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      background: linear-gradient(180deg, #f5f5f7, #e0e0e5);
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 40px;
    }
    .container {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
      width: 90%;
      max-width: 800px;
      padding: 20px;
    }
    h1 {
      text-align: center;
      font-weight: 600;
      color: #333;
      margin-bottom: 20px;
    }
    textarea {
      width: 90%;
      height: 400px;
      border: none;
      border-radius: 12px;
      padding: 15px;
      margin: auto;
      resize: vertical;
      font-size: 16px;
      font-family: monospace;
      box-shadow: inset 0 2px 6px rgba(0,0,0,0.1);
      outline: none;
    }
    .btns {
      display: flex;
      justify-content: center;
      gap: 15px;
      margin-top: 20px;
    }
    button {
      padding: 10px 20px;
      font-size: 15px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 500;
      transition: all 0.2s ease;
    }
    .save {
      background: #007aff;
      color: #fff;
    }
    .save:hover { background: #005ecb; }
    .reload {
      background: #f0f0f5;
      color: #333;
    }
    .reload:hover { background: #e0e0eb; }
  </style>
  <script>
let lastSize = <?php echo filesize($file); ?>; // initial size
function showToast(msg) {
  let div = document.createElement("div");
  div.textContent = msg;
  div.style.position = "fixed";
  div.style.bottom = "20px";
  div.style.right = "20px";
  div.style.background = "#333";
  div.style.color = "#fff";
  div.style.padding = "10px 15px";
  div.style.borderRadius = "8px";
  div.style.opacity = "0.9";
  document.body.appendChild(div);
  setTimeout(() => div.remove(), 1500); // disappears in 1.5s
}
setInterval(() => {
  fetch("index.php?filesize=1")
    .then(res => res.text())
    .then(size => {
      if (parseInt(size) !== lastSize) {
        lastSize = parseInt(size);
        // alert("📢 New data available!");
        new Audio("https://actions.google.com/sounds/v1/alarms/beep_short.ogg").play();
        showToast("📢 New data!");
        setTimeout(() =>location.reload(), 2000);
         
      }
    });
}, 5000); // check every 5s
</script>
</head>
<body>
  <div class="container">
    <h1>🍏 WeNodes Shared Pad </h1>
    <form method="post">
      <textarea name="content"><?php echo htmlspecialchars($content); ?></textarea>
      <div class="btns">
        <button type="submit" class="save">💾 Save</button>
        <button type="button" class="reload" onclick="location.reload()">🔄 Reload</button>
      </div>
    </form>
  </div>
</body>
</html>
