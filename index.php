<?php
require 'db.php';
 
// Fetch some featured properties
$stmt = $conn->prepare("SELECT id, title, city, price_per_night, bedrooms, image FROM properties ORDER BY created_at DESC LIMIT 6");
$stmt->execute();
$res = $stmt->get_result();
$featured = $res->fetch_all(MYSQLI_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>MiniBnB — Home</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    /* Internal CSS — polished, modern look */
    :root{--accent:#ff5a5f;--muted:#6b6b6b;--card:#ffffff;--bg:#f6f7f8;font-family:Inter, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial;}
    *{box-sizing:border-box}
    body{margin:0;background:linear-gradient(180deg,#f7f8fa 0%, #ffffff 100%);color:#222}
    header{background:#fff;padding:22px 28px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 2px 6px rgba(12,20,30,0.06)}
    .brand{font-weight:700;color:var(--accent);font-size:20px}
    .search {background:var(--card);padding:18px;border-radius:12px;max-width:920px;margin:28px auto;box-shadow:0 6px 18px rgba(16,24,40,0.06);display:flex;gap:12px;align-items:center}
    .search input, .search button {padding:12px 14px;border-radius:8px;border:1px solid #e6e7ea;font-size:15px}
    .search .dates{display:flex;gap:8px}
    .search button{background:var(--accent);color:#fff;border:none;cursor:pointer}
    .container{max-width:1100px;margin:18px auto;padding:0 18px}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px}
    .card{background:var(--card);border-radius:12px;overflow:hidden;box-shadow:0 8px 20px rgba(12,20,30,0.06);display:flex;flex-direction:column}
    .card img{width:100%;height:170px;object-fit:cover}
    .card-body{padding:12px}
    .meta{color:var(--muted);font-size:13px;margin-bottom:8px}
    .price{font-weight:700;color:#111}
    footer{text-align:center;padding:22px;color:#666;font-size:14px;margin-top:36px}
    @media (max-width:600px){ .search{flex-direction:column;align-items:stretch} .search .dates{flex-direction:column}}
  </style>
</head>
<body>
  <header>
    <div class="brand">MiniBnB</div>
    <div>Welcome — search stays around the world</div>
  </header>
 
  <form id="searchForm" class="search" onsubmit="doSearch(event)">
    <input type="text" id="destination" placeholder="Where are you going? (e.g., Lahore, Karachi)" />
    <div class="dates">
      <input type="date" id="checkin" />
      <input type="date" id="checkout" />
    </div>
    <button type="submit">Search</button>
  </form>
 
  <div class="container">
    <h2>Featured stays</h2>
    <div class="grid">
      <?php foreach($featured as $p): ?>
        <div class="card">
          <img src="<?php echo htmlspecialchars($p['image'] ?: 'https://picsum.photos/seed/'.$p['id'].'/800/600'); ?>" alt="">
          <div class="card-body">
            <div class="meta"><?php echo htmlspecialchars($p['city']); ?> • <?php echo (int)$p['bedrooms']; ?> bed</div>
            <div style="display:flex;justify-content:space-between;align-items:center">
              <div>
                <div style="font-weight:600"><?php echo htmlspecialchars($p['title']); ?></div>
                <div class="price">₨<?php echo number_format($p['price_per_night'],2); ?> / night</div>
              </div>
              <button onclick="goToProperty(<?php echo $p['id']; ?>)" style="padding:8px 12px;border-radius:8px;border:1px solid #ddd;background:#fff;cursor:pointer">View</button>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
 
  <footer>Made with ♥ — MiniBnB clone</footer>
 
  <script>
    function doSearch(e){
      e.preventDefault();
      const q = document.getElementById('destination').value.trim();
      const ci = document.getElementById('checkin').value;
      const co = document.getElementById('checkout').value;
      // JS redirect to search_results.php with query params
      const params = new URLSearchParams();
      if(q) params.set('q', q);
      if(ci) params.set('checkin', ci);
      if(co) params.set('checkout', co);
      window.location.href = 'search_results.php?' + params.toString();
    }
    function goToProperty(id){
      window.location.href = 'property.php?id=' + encodeURIComponent(id);
    }
  </script>
</body>
</html>
