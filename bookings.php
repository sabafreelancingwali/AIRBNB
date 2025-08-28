<?php
require 'db.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
 
if($id){
  $stmt = $conn->prepare("SELECT b.*, p.title, p.city FROM bookings b JOIN properties p ON p.id = b.property_id WHERE b.id = ?");
  $stmt->bind_param("i",$id);
  $stmt->execute();
  $res = $stmt->get_result();
  $booking = $res->fetch_assoc();
} else {
  $res = $conn->query("SELECT b.id, p.title, b.guest_name, b.checkin, b.checkout, b.total_price FROM bookings b JOIN properties p ON p.id = b.property_id ORDER BY b.created_at DESC LIMIT 50");
  $all = $res->fetch_all(MYSQLI_ASSOC);
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Bookings</title>
  <style>
    body{font-family:Inter,system-ui,Arial;background:#f7f8fa;padding:18px}
    .card{background:#fff;padding:12px;border-radius:10px;box-shadow:0 6px 18px rgba(12,20,30,0.06);max-width:900px;margin:12px auto}
    table{width:100%;border-collapse:collapse}
    th,td{padding:8px;border-bottom:1px solid #eee;text-align:left}
  </style>
</head>
<body>
  <div class="card">
    <?php if($id && $booking): ?>
      <h2>Booking #<?php echo $booking['id']; ?></h2>
      <p><strong>Property:</strong> <?php echo htmlspecialchars($booking['title'].' — '.$booking['city']); ?></p>
      <p><strong>Guest:</strong> <?php echo htmlspecialchars($booking['guest_name']); ?> (<?php echo htmlspecialchars($booking['guest_email']); ?>)</p>
      <p><strong>Check-in:</strong> <?php echo htmlspecialchars($booking['checkin']); ?> — <strong>Check-out:</strong> <?php echo htmlspecialchars($booking['checkout']); ?></p>
      <p><strong>Total:</strong> ₨<?php echo number_format($booking['total_price'],2); ?></p>
      <p><a href="index.php">Back to home</a></p>
    <?php else: ?>
      <h2>Recent bookings</h2>
      <table>
        <thead><tr><th>ID</th><th>Property</th><th>Guest</th><th>Dates</th><th>Total</th></tr></thead>
        <tbody>
          <?php foreach($all as $b): ?>
          <tr>
            <td><a href="bookings.php?id=<?php echo $b['id']; ?>"><?php echo $b['id']; ?></a></td>
            <td><?php echo htmlspecialchars($b['title']); ?></td>
            <td><?php echo htmlspecialchars($b['guest_name']); ?></td>
            <td><?php echo htmlspecialchars($b['checkin']); ?> → <?php echo htmlspecialchars($b['checkout']); ?></td>
            <td>₨<?php echo number_format($b['total_price'],2); ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <p style="margin-top:10px"><a href="index.php">Back home</a></p>
    <?php endif; ?>
  </div>
</body>
</html>
