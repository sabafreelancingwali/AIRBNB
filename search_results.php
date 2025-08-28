<?php
require 'db.php';
 
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$checkin = isset($_GET['checkin']) ? $_GET['checkin'] : '';
$checkout = isset($_GET['checkout']) ? $_GET['checkout'] : '';
 
$sql = "SELECT id, title, city, price_per_night, bedrooms, image FROM properties WHERE 1=1";
$params = [];
if ($q !== '') {
  $sql .= " AND (title LIKE ? OR city LIKE ?)";
  $term = "%{$q}%";
  $params[] = $term;
  $params[] = $term;
}
$sql .= " ORDER BY price_per_night ASC LIMIT 50";
$stmt = $conn->prepare($sql);
 
if(count($params) === 2) {
  $stmt->bind_param("ss", $params[0], $params[1]);
}
$stmt->execute();
$res = $stmt->get_result();
$props = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Search results</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    /* internal CSS */
    body{font-family:Inter,system-ui,Arial;margin:0;background:#fafafa;color:#222}
    header{padding:18px;background:#fff;box-shadow:0 2px 8px rgba(12,20,30,0.05);display:flex;justify-content:space-between;align-items:center}
    .container{max-width:1100px;margin:18px auto;padding:0 18px}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px}
    .card{background:#fff;border-radius:12px;overflow:hidden;box-shadow:0 8px 18px rgba(12,20,30,0.06)}
    .card img{width:100%;height:180px;object-fit:cover}
    .card-body{padding:12px}
    .price{font-weight:700}
    .filters{display:flex;gap:12px;margin-bottom:14px;align-items:center}
    .filters select, .filters input{padding:8px;border-radius:8px;border:1px solid #e8e9eb}
  </style>
</head>
<body>
  <header>
    <div><strong>Search results</strong></div>
    <div><a href="index.php">Back home</a></div>
  </header>
 
  <div class="container">
    <div style="display:flex;justify-content:space-between;align-items:center">
      <div style="color:#666">Showing <?php echo count($props); ?> stays <?php echo $q ? "for '".htmlspecialchars($q)."'" : ''; ?></div>
      <div class="filters">
        <label>Sort:
          <select id="sort" onchange="doSort()">
            <option value="price_asc">Price low → high</option>
            <option value="price_desc">Price high → low</option>
          </select>
        </label>
      </div>
    </div>
 
    <div class="grid" id="grid">
      <?php foreach($props as $p): ?>
      <div class="card">
        <img src="<?php echo htmlspecialchars($p['image'] ?: 'https://picsum.photos/seed/'.$p['id'].'/800/600'); ?>" alt="">
        <div class="card-body">
          <div style="display:flex;justify-content:space-between;align-items:center">
            <div>
              <div style="font-weight:600"><?php echo htmlspecialchars($p['title']); ?></div>
              <div style="color:#777;font-size:13px"><?php echo htmlspecialchars($p['city']); ?> • <?php echo (int)$p['bedrooms']; ?> bed</div>
            </div>
            <div style="text-align:right">
              <div class="price">₨<?php echo number_format($p['price_per_night'],2); ?> / night</div>
              <button onclick="view(<?php echo $p['id']; ?>)" style="margin-top:8px;padding:7px 10px;border-radius:8px;border:1px solid #ddd;background:#fff;cursor:pointer">View</button>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
 
  <script>
    function view(id){
      window.location.href = 'property.php?id=' + id;
    }
    function doSort(){
      const sort = document.getElementById('sort').value;
      // minimal client-side sort of DOM cards
      const grid = document.getElementById('grid');
      const cards = Array.from(grid.children);
      cards.sort((a,b) => {
        const pa = parseFloat(a.querySelector('.price').innerText.replace(/[^\d\.]/g,''));
        const pb = parseFloat(b.querySelector('.price').innerText.replace(/[^\d\.]/g,''));
        return sort === 'price_desc' ? pb - pa : pa - pb;
      });
      cards.forEach(c => grid.appendChild(c));
    }
  </script>
</body>
</html
