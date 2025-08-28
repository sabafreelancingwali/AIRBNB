<?php
require 'db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if(!$id){
  echo "<script>window.location='index.php'</script>";
  exit;
}
$stmt = $conn->prepare("SELECT * FROM properties WHERE id = ?");
$stmt->bind_param("i",$id);
$stmt->execute();
$res = $stmt->get_result();
$prop = $res->fetch_assoc();
if(!$prop){
  echo "<h2>Property not found</h2>";
  exit;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?php echo htmlspecialchars($prop['title']); ?></title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    body{font-family:Inter,system-ui,Arial;background:#f5f6f7;margin:0;color:#222}
    .wrap{max-width:1000px;margin:18px auto;padding:18px}
    .hero{display:flex;gap:18px;align-items:flex-start}
    .hero img{width:460px;height:300px;object-fit:cover;border-radius:12px;box-shadow:0 8px 20px rgba(12,20,30,0.06)}
    .info{flex:1}
    .price{font-size:20px;font-weight:700;color:#111}
    .book{background:#fff;padding:12px;border-radius:12px;box-shadow:0 8px 20px rgba(12,20,30,0.06)}
    input, button {padding:10px;border-radius:8px;border:1px solid #e8e9eb}
    button.bookbtn{background:#ff5a5f;color:#fff;border:none;padding:10px 14px;cursor:pointer;border-radius:8px}
  </style>
</head>
<body>
  <div class="wrap">
    <a href="search_results.php">← Back to results</a>
    <div class="hero" style="margin-top:12px">
      <img src="<?php echo htmlspecialchars($prop['image'] ?: 'https://picsum.photos/seed/'.$prop['id'].'/1200/800'); ?>" alt="">
      <div class="info">
        <h1><?php echo htmlspecialchars($prop['title']); ?></h1>
        <div style="color:#666"><?php echo htmlspecialchars($prop['city']); ?> • <?php echo (int)$prop['bedrooms']; ?> bed • <?php echo htmlspecialchars($prop['amenities']); ?></div>
        <div style="margin-top:10px" class="price">₨<?php echo number_format($prop['price_per_night'],2); ?> / night</div>
 
        <div style="margin-top:18px" class="book">
          <h3>Book this stay</h3>
          <form id="bookForm" method="post" action="book.php" onsubmit="return submitBooking(event)">
            <input type="hidden" name="property_id" value="<?php echo $prop['id']; ?>">
            <div style="display:flex;gap:8px;margin-bottom:8px">
              <input name="guest_name" placeholder="Your full name" required style="flex:1">
              <input name="guest_email" placeholder="Email" type="email" required style="flex:1">
            </div>
            <div style="display:flex;gap:8px;margin-bottom:8px">
              <input type="date" name="checkin" required>
              <input type="date" name="checkout" required>
            </div>
            <div style="margin-bottom:8px">Total estimate: <span id="estimate">₨0.00</span></div>
            <button class="bookbtn" type="submit">Reserve</button>
          </form>
        </div>
      </div>
    </div>
 
    <section style="margin-top:18px;background:#fff;padding:12px;border-radius:12px;box-shadow:0 6px 18px rgba(12,20,30,0.05)">
      <h3>About this place</h3>
      <p><?php echo nl2br(htmlspecialchars($prop['description'])); ?></p>
    </section>
  </div>
 
  <script>
    const price = <?php echo floatval($prop['price_per_night']); ?>;
    const form = document.getElementById('bookForm');
    function daysBetween(a,b){
      const da = new Date(a);
      const db = new Date(b);
      if(isNaN(da) || isNaN(db)) return 0;
      const diff = (db - da) / (1000*60*60*24);
      return diff > 0 ? diff : 0;
    }
    form.addEventListener('input', function(){
      const ci = form.checkin.value;
      const co = form.checkout.value;
      const d = daysBetween(ci,co);
      document.getElementById('estimate').innerText = '₨' + ( (d * price).toFixed(2) );
    });
    function submitBooking(e){
      // basic client validation
      const ci = form.checkin.value;
      const co = form.checkout.value;
      if(daysBetween(ci,co) <= 0){
        alert("Checkout must be after check-in.");
        return false;
      }
      // allow normal POST to book.php; book.php will redirect via JS
      return true;
    }
  </script>
</body>
</html>
