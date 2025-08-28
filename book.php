<?php
require 'db.php';
 
if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    echo "<script>window.location='index.php'</script>";
    exit;
}
 
$property_id = isset($_POST['property_id']) ? (int)$_POST['property_id'] : 0;
$guest_name = trim($_POST['guest_name'] ?? '');
$guest_email = trim($_POST['guest_email'] ?? '');
$checkin = $_POST['checkin'] ?? '';
$checkout = $_POST['checkout'] ?? '';
 
if(!$property_id || !$guest_name || !$guest_email || !$checkin || !$checkout){
    echo "<script>alert('Please fill all fields.');history.back();</script>";
    exit;
}
 
// Get price
$stmt = $conn->prepare("SELECT price_per_night FROM properties WHERE id = ?");
$stmt->bind_param("i",$property_id);
$stmt->execute();
$r = $stmt->get_result()->fetch_assoc();
if(!$r){
    echo "<script>alert('Property not found');window.location='index.php'</script>";
    exit;
}
$price = floatval($r['price_per_night']);
$days = (new DateTime($checkout))->diff(new DateTime($checkin))->days;
if($days <= 0){
    echo "<script>alert('Invalid dates');history.back();</script>";
    exit;
}
$total = $price * $days;
 
// Insert booking
$ins = $conn->prepare("INSERT INTO bookings (property_id, guest_name, guest_email, checkin, checkout, total_price) VALUES (?, ?, ?, ?, ?, ?)");
$ins->bind_param("issssd", $property_id, $guest_name, $guest_email, $checkin, $checkout, $total);
$ok = $ins->execute();
 
if(!$ok){
    echo "<script>alert('Failed to create booking. Try again.');history.back();</script>";
    exit;
}
 
// JS redirect to confirmation page (use JS for redirect)
$booking_id = $conn->insert_id;
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Booking Confirmed</title></head>
<body>
  <div style="font-family:Inter,system-ui,Arial;padding:28px;">
    <h2>Booking successful!</h2>
    <p>Thanks, <?php echo htmlspecialchars($guest_name); ?>. Your reservation <?php echo $booking_id; ?> is confirmed.</p>
    <p>Total: ₨<?php echo number_format($total,2); ?></p>
    <p>You will now be redirected to your booking details...</p>
  </div>
 
  <script>
    // JavaScript redirection (as requested)
    setTimeout(function(){
      window.location.href = 'bookings.php?id=<?php echo $booking_id; ?>';
    }, 900);
  </script>
</body>
</html>
