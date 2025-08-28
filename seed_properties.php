<?php
require 'db.php';
$demo = [
  ['Cozy studio in Lahore','A compact, sunny studio in the heart of Lahore.','Lahore',2500.00,1,1,'https://picsum.photos/seed/1/1200/800','WiFi,Kitchen,Air conditioning'],
  ['Seaside apartment Karachi','Modern 2-bedroom with sea view.','Karachi',4800.00,2,1,'https://picsum.photos/seed/2/1200/800','WiFi,Sea view,Parking'],
  ['Islamabad Hills Retreat','Quiet retreat with mountain views.','Islamabad',6500.00,3,2,'https://picsum.photos/seed/3/1200/800','WiFi,Fireplace,Hot tub'],
  ['Designer Flat near Mall','Stylish flat near shopping area.','Lahore',3200.00,2,1,'https://picsum.photos/seed/4/1200/800','WiFi,AC,Washer'],
];
$stmt = $conn->prepare("INSERT INTO properties (title,description,city,price_per_night,bedrooms,baths,image,amenities) VALUES (?,?,?,?,?,?,?,?)");
foreach($demo as $d){
  $stmt->bind_param("sssdiiss",$d[0],$d[1],$d[2],$d[3],$d[4],$d[5],$d[6],$d[7]);
  $stmt->execute();
}
echo "Seeded demo properties.";
