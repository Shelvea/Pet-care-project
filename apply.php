<?php
session_start();
if (!empty($_SESSION['success'])) {
    foreach ($_SESSION['success'] as $msg) echo "<div style='color:green;text-align:center;'>$msg</div>";
    unset($_SESSION['success']);
}
if (!empty($_SESSION['fail'])) {
    foreach ($_SESSION['fail'] as $msg) echo "<div style='color:red;text-align:center;'>$msg</div>";
    unset($_SESSION['fail']);
}

include 'db_connect.php';

$client_id = $_SESSION['customer_id'];//id from users table

$stmt2 = $conn->prepare("SELECT id FROM client_data WHERE user_id=?");
$stmt2->bind_param("i", $client_id);
$stmt2->execute();
$stmt2->bind_result($user_id);//id from client_data
$stmt2->fetch();
$stmt2->close();

$stmt = $conn->prepare("SELECT id, name, gender, type, breed, age, note, picture_path FROM pets_info WHERE client_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$pets = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get IDs of pets with active or future nanny_orders for this client
$bookedPets = [];
if (!empty($pets)) {
    $petIds = array_column($pets, 'id');
    $in  = str_repeat('?,', count($petIds) - 1) . '?';
    

    $sql = "SELECT no.pet_id, bs.id AS service_id, bs.service_type
            FROM nanny_orders no
            JOIN branch_services bs ON no.services_id = bs.id
            WHERE no.customer_id = ? AND no.pet_id IN ($in) AND no.status <> 'service completed'";

    $stmt = $conn->prepare($sql);

    // Bind params dynamically
    $params = array_merge([$user_id], $petIds);

    // Create types string: all integers
    $types = str_repeat('i', count($params));

    // mysqli bind_param requires references
    $bind_names[] = & $types;
    foreach ($params as $key => $value) {
        $bind_names[] = & $params[$key];
    }

    call_user_func_array([$stmt, 'bind_param'], $bind_names);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        // Initialize array if not exists
        if (!isset($bookedPets[$row['pet_id']])) {
            $bookedPets[$row['pet_id']] = [];
        }
        // Store booked service_type
        $bookedPets[$row['pet_id']][] = $row['service_type'];
    }

    $stmt->close();

} else {

    $_SESSION['message'] = "Your Pet Database is empty. Please add your pet first";
}
?>

<?php
// Example values from DB or previous input
$return_pickup_time_val = $pet['return_pickup_time'] ?? '';
$return_time_val = $pet['return_time'] ?? '';


// Format values for input type="datetime-local"
function formatDatetimeLocal($value) {
    return !empty($value) ? date('Y-m-d\TH:i', strtotime($value)) : '';
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Apply Care</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=ZCOOL+KuaiLe&display=swap" rel="stylesheet">
    <style>
    /* Your CSS here, unchanged */
    .pets-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      margin-top: 20px;
      
    }
    .pet-card {
      
      
      border: 2px solid chocolate;
      border-radius: 12px;
      padding: 15px;
      background-color: #fff8f0;
      width: 400px;
      cursor: pointer;
      transition: box-shadow 0.3s;
      text-align: center;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
      position: relative;
      display: block;
    }
    .pet-card:hover {
      box-shadow: 0 0 15px rgba(255,140,0,0.4);
      background-color: #fff1e0;
    }
    .pet-card.selected {
      border: 2px solid green;
      background-color: #f0fff0;
    }

    .pet-content img {
     
      width:300px;
      height:200px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 10px;
    }
    .pet-content h3 {
      margin: 5px 0;
      color: chocolate;
    }
    button {
      background-color: burlywood;
      color: brown;
      padding: 10px 10px;
      border-radius: 10px;
      font-weight: bold;
      border: 1px solid burlywood;
    }
    button:hover {
      color: darkgreen;
      cursor: pointer;
    }
    .back-container {
      padding: 13px 13px;
      border-radius: 10px;
      border: 1px solid burlywood;
      background-color: chocolate;
      color: white;
      font-size: 15px;
      font-weight: bold;
      text-align: center;
      font-family: 'ZCOOL KuaiLe', cursive;
    }
    .back-container:hover {
      color: chocolate;
      background-color: bisque;
    }
    .top-left {
      position: absolute;
      top: 20px;
      left: 20px;
    }
    .care-inputs {
      margin-top: 10px;
      text-align: left;
      font-size: 14px;
    }
    .select-checkbox {
      position: absolute;
      top: 12px;
      left: 12px;
      transform: scale(1.5);
      z-index: 10;
      pointer-events: auto;
      background-color: red;
    }
    .service-container {
      color: chocolate;
    }
    .card-toggle-area {
      cursor: pointer;
    }
    .card-toggle-area:hover {
      background-color: #fff0e0;
    }
    .branch, .send, .return, .textarea{
      width: 200px; 
      margin-top: 10px;
      background-color: burlywood;
      border-radius: 8px;
      border: 1px solid burlywood;
      padding: 5px 5px;
      color:brown;
    }
    .alert {
  background-color: #ffcc80;
  color: #333;
  padding: 10px;
  margin: 10px 0;
  border-radius: 6px;
  font-weight: bold;
  text-align: center;
  margin-top: 80px;
}

.delivery-container{
    border:1px dashed chocolate;
    padding:10px;
    margin-top:10px;
}

@media screen and (max-width: 480px) {
   .pet-card {
        width: 90%;
        max-width: 200px;
        padding: 8px;
        font-size: 12px;
        margin-top: 50px;
    }

    .pet-content img {
        width: 100%; 
        height: auto;
        object-fit: cover;
        border-radius: 4px;
        margin-bottom: 5px;
    }

    .pet-content h3 {
        font-size: 14px;
        margin: 3px 0;
    }

    .pet-card p {
        font-size: 12px;
        margin: 2px 0;
    }

    .textarea,
    .branch,
    .send,
    .return{
        width: 100%;
        max-width: 100%;
        font-size: 12px;
        padding: 3px 3px;
        box-sizing: border-box;
    }



    fieldset {
        padding: 5px;
        font-size: 12px;
        box-sizing: border-box;
        width: 100%; /* prevent overflow */
    }

    legend {
        font-size: 13px;
    }

    button {
        font-size: 12px;
        padding: 6px 8px;
    }
    
    .back-container {
    
    padding: 8px 8px;
      border-radius: 6px;
      border: 1px solid burlywood;
      background-color: chocolate;
      color: white;
      font-size: 12px;
      font-weight: bold;
      text-align: center;
      font-family: 'ZCOOL KuaiLe', cursive;
    }

    .pet-card input[type="datetime-local"],
    .pet-card input[type="text"] {
    width: 100%;           /* make it fit the card */
    max-width: 100%;       /* prevent overflow */
    box-sizing: border-box;/* include padding/border in width */
    padding: 5px;
    border: 1px solid burlywood;
    border-radius: 6px;
    background-color: #fff8f0;
    color: brown;
    font-size: 13px;
}

}
    </style>
</head>
<body>

<?php
if (isset($_SESSION['message'])) {
    echo "<div class='alert'>".$_SESSION['message']."</div>";
    
}
?>


<form action="newUpload.php" method="POST">
    

<div class="pets-container">
<?php foreach ($pets as $pet):
    $pickup_time_val = $pet['pickup_time'] ?? '';
   $isBooked = isset($bookedPets[$pet['id']]) && !empty($bookedPets[$pet['id']]);
  
  ?>
    <div class="pet-card">
        <input type="checkbox" name="selected_pets[]" value="<?= $pet['id'] ?>" class="select-checkbox" data-pet-id="<?= $pet['id'] ?>" <?= $isBooked ? 'disabled' : '' ?>>

        <div class="card-toggle-area">
            <div class="pet-content">
                <img src="uploads/<?= htmlspecialchars($pet['picture_path']) ?>" alt="Pet Image">
                <h3><?= htmlspecialchars($pet['name']) ?></h3>
                <div class="info">
                    <p><strong>性別:</strong> <?= $pet['gender'] ?></p>
                    <p><strong>種類:</strong> <?= $pet['type'] ?></p>
                    <p><strong>品種:</strong> <?= $pet['breed'] ?></p>
                    <p><strong>年齡:</strong> <?= $pet['age'] ?> 歲</p>
                    <p><strong>特性:</strong> <?= htmlspecialchars($pet['note']) ?></p>
                </div>
            </div>
        </div>

    <?php if ($isBooked): ?>
        <p style="color:red;font-weight:bold;">⚠️ 此寵物已有預約中，無法再次申請</p>
    <?php endif; ?>

        <label>備註:</label>
        <textarea class="textarea" name="pets[<?= $pet['id'] ?>][comment]" rows="4" cols="35" <?= $isBooked ? 'disabled' : '' ?>></textarea>
        <br>

        <!-- DELIVERY METHOD START -->
        <fieldset class = "delivery-container"  <?= $isBooked ? 'disabled' : '' ?>>
            <legend style="color:darkred;">交付方式設定</legend>

            <div id="branch_select_<?= $pet['id'] ?>">
                <label>服務分店:</label>
                <select name="pets[<?= $pet['id'] ?>][branch_id]" class="branch" <?= $isBooked ? 'disabled' : '' ?>>
                    <option value="">-- 選擇服務分店 --</option>
                    <?php
                    $branches = $conn->query("SELECT id, city, address FROM shop_branches");
                    while ($b = $branches->fetch_assoc()):
                    ?>
                    <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['city']) ?> - <?= htmlspecialchars($b['address']) ?></option>
                    <?php endwhile; ?>
                </select>
                <br><br>
            </div>

            <div id="service_container_<?= $pet['id'] ?>">
                <label><em>請先選擇分店以顯示服務</em></label>
            </div>
            <br>

            <label>交付方式:<span class="error">*</span></label>
            <select name="pets[<?= $pet['id'] ?>][pickup_method]" onchange="togglePetFields(<?= $pet['id'] ?>)" class="send" <?= $isBooked ? 'disabled' : '' ?>>
                <option value="">-- 選擇方式 --</option>
                <option value="nanny_pickup">保母取件</option>
                <option value="branch_dropoff">送至分店</option>
            </select>
            <br><br>

            <div id="pickup_address_<?= $pet['id'] ?>" style="display:none;">
                <label>取件地址:</label>
                <input type="text" name="pets[<?= $pet['id'] ?>][pickup_address]" placeholder="輸入您的地址" <?= $isBooked ? 'disabled' : '' ?>>
            </div><br><br>

            <div id="pickup_time_container_<?= $pet['id'] ?>" style="display:none;">
                <label>保母取件時間:</label>
                <input type="datetime-local" name="pets[<?= $pet['id'] ?>][pickup_time]" value="<?= formatDatetimeLocal($pickup_time_val) ?>" <?= $isBooked ? 'disabled' : '' ?>>
                <br><br>
            </div>

            <div id="branch_dropoff_time_container_<?= $pet['id'] ?>" style="display:none;">
                <label>傳送時間:</label>
                <input type="datetime-local" name="pets[<?= $pet['id'] ?>][branch_dropoff_time]" <?= $isBooked ? 'disabled' : '' ?>>
                <br><br>
            </div>

        </fieldset>
        <!-- DELIVERY METHOD END -->

    </div>
<?php endforeach; ?>
</div>

<div class="button-container" 
     style="text-align:center; margin-top: 20px; <?php if (!empty($_SESSION['message'])) { echo 'display:none;'; } ?>">
    <button type="submit">申請托育</button>
</div>

<?php unset($_SESSION['message']); // clear after button check ?>

</form>

<div class="top-left">
    <button onclick="window.location.href='clientPage.php'" class="back-container"><i class="fa-solid fa-right-from-bracket"></i> 返回</button>
</div>


<script>
// Checkbox validation
document.querySelector('form').addEventListener('submit', e => {
    const checkboxes = document.querySelectorAll('.select-checkbox:not(:disabled)');
    if (![...checkboxes].some(cb => cb.checked)) {
        e.preventDefault();
        alert("⚠️ 請至少選擇一隻寵物進行申請！");
    }
});
</script><!--ok-->

<script>
function togglePetFields(petId) {
    const methodSelect = document.querySelector(`select[name="pets[${petId}][pickup_method]"]`);
    if (!methodSelect) return;
    const method = methodSelect.value;
    document.getElementById(`pickup_address_${petId}`).style.display = method === "nanny_pickup" ? "block" : "none";
    document.getElementById(`pickup_time_container_${petId}`).style.display = method === "nanny_pickup" ? "block" : "none";
    document.getElementById(`branch_dropoff_time_container_${petId}`).style.display = method === "branch_dropoff" ? "block" : "none";
}



function toggleInputsForPet(petId, enabled) {
    const card = document.querySelector(`.select-checkbox[data-pet-id="${petId}"]`).closest('.pet-card');
    const inputs = Array.from(card.querySelectorAll('input, select, textarea')).filter(input => !input.classList.contains('select-checkbox'));
    inputs.forEach(input => {
        input.disabled = !enabled;
    });

    // Set pickup_method select to required if enabled, remove if not
    const pickupSelect = card.querySelector(`select[name="pets[${petId}][pickup_method]"]`);
    if (pickupSelect) {
        pickupSelect.required = enabled;
    }
}

document.querySelectorAll('.card-toggle-area').forEach(toggleArea => {
    toggleArea.addEventListener('click', function (e) {
        
        const card = this.closest('.pet-card');
        const checkbox = card.querySelector('.select-checkbox');
        const petId = checkbox.value;

        if (checkbox.disabled) return;

        checkbox.checked = !checkbox.checked;

        if (checkbox.checked) {
            card.classList.add('selected');
        } else {
            card.classList.remove('selected');
        }

        toggleInputsForPet(petId, checkbox.checked);
        togglePetFields(petId);

    });
});

window.addEventListener('DOMContentLoaded', () => {
  

  // Initialize pickup method fields (for showing pickup time, branch, etc.)
  document.querySelectorAll('select[name$="[pickup_method]"]').forEach(select => {
    const petId = select.name.match(/\[(\d+)\]/)[1];
    togglePetFields(petId);
  });
});

</script>

<script>
// Branch change → load services dynamically
document.querySelectorAll('.branch').forEach(select => {
    select.addEventListener('change', function() {
        const petId = this.name.match(/pets\[(\d+)\]/)[1];
        const branchId = this.value;
        const serviceContainer = document.getElementById('service_container_' + petId);

        if (!branchId) {
            serviceContainer.innerHTML = "<em>請先選擇分店以顯示服務</em>";
            return;
        }

        fetch('get_branch_services.php?branch_id=' + branchId)
        .then(res => res.json())
        .then(services => {
            if (!services.length) {
                serviceContainer.innerHTML = "<em>此分店暫無服務</em>";
                return;
            }

            let html = '<label>服務類型:</label><div class="service-container">';
            services.forEach(s => {
              const booked = (<?= json_encode($bookedPets) ?>[petId] || []).includes(s.id);
                html += `<label>
                    <input type="checkbox" name="pets[${petId}][service_ids][]"
                           value="${s.id}" ${booked ? 'disabled' : ''}>       
                    ${s.service_type} (${s.fixed_price} NT$ / ${s.unit}) ${booked ? '(已預約)' : ''}   
                </label><br>`;
            });
            html += '</div>';
            serviceContainer.innerHTML = html;
        });
    });
});

</script>

<script>
  // Add hidden price/unit inputs when service type selected
document.addEventListener('change', e => {
    if (!e.target.name.includes('[service_ids]') || !e.target.checked) return;
    const petId = e.target.name.match(/pets\[(\d+)\]/)[1];
    const form = e.target.closest('form');

    ['price','unit'].forEach(f => {
        let existing = form.querySelector(`input[name="pets[${petId}][${f}]"]`);
        if (existing) existing.remove();
    });

    const priceInput = document.createElement('input');
    priceInput.type = 'hidden'; priceInput.name = `pets[${petId}][price]`; priceInput.value = e.target.dataset.price;
    const unitInput = document.createElement('input');
    unitInput.type = 'hidden'; unitInput.name = `pets[${petId}][unit]`; unitInput.value = e.target.dataset.unit;

    form.appendChild(priceInput);
    form.appendChild(unitInput);
});


</script>

</body>
</html>