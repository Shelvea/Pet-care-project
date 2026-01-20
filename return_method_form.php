<?php
session_start();
require 'db_connect.php';

// Get order ID from query string
$order_id = intval($_GET['order_id'] ?? 0);

$client_id = $_SESSION['customer_id'] ?? 0;//id from users

if (!$client_id){ 
  die("Unauthorized access");
}

$stmt2 = $conn->prepare("SELECT id FROM client_data WHERE user_id = ?");
$stmt2->bind_param("i",$client_id);
$stmt2->execute();

// bind result column(s) to variable(s)
$stmt2->bind_result($client_data_id);
$stmt2->fetch();
$stmt2->close();

$stmt = $conn->prepare("SELECT p.id
    FROM nanny_orders no
    JOIN pets_info p ON no.pet_id = p.id
    WHERE no.customer_id = ? AND
    no.status = 'service completed' AND no.id = ?
");

$stmt->bind_param("ii", $client_data_id, $order_id);
$stmt->execute();
$result = $stmt->get_result(); 
$pet = $result->fetch_assoc();
$stmt->close();

?>

<?php

    $message = "";  // create empty message holder

    if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pets'])){
        //Loop through each pet's return data
        foreach($_POST['pets'] as $petId => $petData){
          $returnMethod = $petData['return_method'] ?? '';
          $returnTime = $petData['return_time'] ?? null;
          $returnAddress = $petData['return_address'] ?? '';
          $returnPickupTime = $petData['return_pickup_time'] ?? null;

          if($returnMethod === 'nanny_return'){
            if(empty($returnTime) || empty($returnAddress)){
              $message .= "Pet ID $petId is missing return time or address.<br>";
              continue;
            }
          }elseif($returnMethod === 'branch_pickup'){
            if(empty($returnPickupTime)){
              $message .= "Pet ID $petId is missing return pickup time.<br>";
              continue;
            }
          }else{
            $message .= "Pet ID $petId has no valid return method.<br>";
            continue;
          }

          //insert into DB
          $stmt = $conn->prepare("UPDATE nanny_orders SET return_method = ?, return_address = ?, return_pickup_time = ?, return_time = ? WHERE id = ?");
          $stmt->bind_param("ssssi", $returnMethod, $returnAddress, $returnPickupTime, $returnTime, $order_id);
          $stmt->execute();
        }

        if ($message === "") {
        $message = "✅ Return data processed successfully.";
      }
    }
?>

<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=ZCOOL+KuaiLe&display=swap" rel="stylesheet">
  <style>
    body{
      background-image: url('https://img.freepik.com/free-vector/cat-lover-pattern-background-design_53876-100662.jpg');
      background-size: cover;
      background-repeat: no-repeat;
      background-attachment: fixed;
      font-family: Arial, sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .form-container {
      background: rgba(255, 255, 255, 0.9);
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0px 4px 12px rgba(0,0,0,0.2);
      width: 400px;
      text-align: center;
    }

    .form-container h2 {
      margin-bottom: 20px;
      color: #444;
    }

     label {
      display: block;
      text-align: left;
      margin-bottom: 6px;
      font-weight: bold;
      color: #333;
    }

    select, input[type="text"], input[type="datetime-local"] {
      width: 100%;
      padding: 10px;
      border-radius: 8px;
      border: 1px solid #ccc;
      margin-bottom: 15px;
      font-size: 14px;
      box-sizing: border-box;
    }

    .confirm-container {
      text-align: center;
    }

    .confirm-return {
      background-color: #4CAF50;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: 0.3s;
    }

    .confirm-return:hover {
      background-color: #45a049;
      transform: scale(1.05);
    }

    .error {
      color: red;
      font-weight: normal;
    }

     .back-container{
            padding: 13px 13px;
            border-radius: 10px;
            border: 1px solid burlywood; 
            background-color:chocolate; 
            color: white;
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            font-family: 'ZCOOL KuaiLe', cursive; /* 👈 added this line */
            
        }
        .back-container:hover{
            color:chocolate;
            background-color: bisque;
        }
        .top-left {
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .status-message {
        margin-bottom: 15px;
        padding: 12px;
        border-radius: 8px;
        background-color: #e7f7e7;
        border: 1px solid #4CAF50;
        color: #2e7d32;
        font-weight: bold;
        text-align: center;
        }


  </style>
</head>

<body>
  <div class="form-container">
  <h2>設定寵物歸還方式</h2>

<!-- Return Method Select -->
<?php if(!empty($message)): ?>
    <div class="status-message"><?= $message ?></div>
  <?php endif; ?>

<form method="post" action="">
      <label>歸還方式:<span class="error">*</span></label>
      <select name="pets[<?= $pet['id'] ?>][return_method]" onchange="toggleReturnFields(<?= $pet['id'] ?>)"  class="return">
        <option value="">-- 選擇方式 --</option>
        <option value="nanny_return">保母送回</option>
        <option value="branch_pickup">至分店取回</option>
      </select>
      <br><br>

      <div id="nanny_return_time_container_<?= $pet['id'] ?>" style="display:none;">
      <label>保母送回時間:</label>
      <input type="datetime-local" name="pets[<?= $pet['id'] ?>][return_time]"  >
      <br><br>
      </div>

      <div id="return_address_container_<?= $pet['id'] ?>" style="display:none;">
      <label>送回地址:</label>
      <input type="text" name="pets[<?= $pet['id'] ?>][return_address]" placeholder="輸入送回地址" >
      <br><br>
      </div>


    <div id="return_pickup_time_container_<?= $pet['id'] ?>" style="display:none;">
    <label>取回時間:</label>
    <input type="datetime-local" name="pets[<?= $pet['id'] ?>][return_pickup_time]"  >
    </div><br><br>

      <div class="confirm-container">
        <input type="submit" name="set_return" value="提交" class="confirm-return">
      </div>

</form>
  </div>

<script>
function togglePetFields(petId) {

    const pickupTimeContainer = document.getElementById(`pickup_time_container_${petId}`);
const branchDropoffTimeContainer = document.getElementById(`branch_dropoff_time_container_${petId}`);
 

    if (method === "nanny_pickup") {
        
        pickupTimeContainer.style.display = "block"; // show pickup time
        branchDropoffTimeContainer.style.display = "none";

    } else if (method === "branch_dropoff") {
        
        pickupTimeContainer.style.display = "none"; // hide pickup time
        branchDropoffTimeContainer.style.display = "block"; // ✅ show 送交時間
        
    } else {
        
        pickupTimeContainer.style.display = "none"; // hide by default
        branchDropoffTimeContainer.style.display = "none";
       
    }
}

function toggleReturnFields(petId) {
  const returnMethod = document.querySelector(`select[name="pets[${petId}][return_method]"]`).value;

  const nannyReturnTime = document.getElementById(`nanny_return_time_container_${petId}`);
  const returnAddress = document.getElementById(`return_address_container_${petId}`);
  const branchPickupTime = document.getElementById(`return_pickup_time_container_${petId}`);

  if (returnMethod === "nanny_return") {
    nannyReturnTime.style.display = "block";
    returnAddress.style.display = "block";
    if (branchPickupTime) branchPickupTime.style.display = "none";
  } 
  else if (returnMethod === "branch_pickup") {
    nannyReturnTime.style.display = "none";
    returnAddress.style.display = "none";
    if (branchPickupTime) branchPickupTime.style.display = "block";
  } 
  else {
    nannyReturnTime.style.display = "none";
    returnAddress.style.display = "none";
    if (branchPickupTime) branchPickupTime.style.display = "none";
  }
}

// Run once on page load & on dropdown change
window.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('select[name$="[return_method]"]').forEach(select => {
    const petId = select.name.match(/\[(\d+)\]/)[1];
    toggleReturnFields(petId);
    select.addEventListener('change', () => toggleReturnFields(petId));
  });
});


function toggleInputsForPet(petId, enabled) {
    const card = document.querySelector(`.select-checkbox[data-pet-id="${petId}"]`).closest('.pet-card');
    const inputs = Array.from(card.querySelectorAll('input, select, textarea')).filter(input => !input.classList.contains('select-checkbox'));
    inputs.forEach(input => {
        input.disabled = !enabled;
    });
}

</script>

<div class="top-left">
<button  onclick="window.location.href='notifications.php'" class="back-container" ><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
</div>

</body>
</html>