<?php
session_start();
include 'db-connection.php';

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

date_default_timezone_set("Asia/Kolkata");
$currentDate = date("Y-m-d");
$currentTime = date("h:i A");


$passengerName = '';
$ticketName = '';
$ticketPrice = '';
$error_message = '';

// Handle GET request to populate form fields from URL parameters
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $ticketName = isset($_GET['ticketName']) ? trim($_GET['ticketName']) : '';
    $ticketPrice = isset($_GET['ticketPrice']) ? trim(str_replace(['$', '₹'], '', $_GET['ticketPrice'])) : '';
}

// Handle form submission (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $passengerName = trim($_POST['name'] ?? '');
    $ticketName = trim($_POST['ticketName'] ?? '');
    $ticketPrice = trim(str_replace(['$', '₹'], '', $_POST['ticketPrice'] ?? ''));
    $method = $_POST['method'] ?? 'Unknown'; // ✅ Added to capture payment method

    // Validate required fields

    $captchaInput = trim($_POST['captcha'] ?? '');

    if (empty($passengerName) || empty($ticketName) || empty($ticketPrice)) {
        $error_message = "Error: All fields including captcha are required.";
    } else {
        unset($_SESSION['captcha']);
        try {
            // Convert ticketPrice to a float for database storage
            $ticketPrice = floatval(preg_replace('/[^0-9.]/', '', $ticketPrice));

            if ($ticketPrice <= 0) {
                $error_message = "Error: Invalid price value.";
                throw new Exception("Invalid price: " . $ticketPrice);
            }

            // Insert payment details
            date_default_timezone_set("Asia/Kolkata"); // set your timezone

            $current_time = date("Y-m-d H:i:s");
            $stmt = $conn->prepare("INSERT INTO payments (user_id, passenger_name, ticket_type, price, method, created_at) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $passengerName, $ticketName, $ticketPrice, $method, $current_time]);


            // Generate ticket details
            $paymentId = $conn->lastInsertId();
            $ticketNumber = 'TK-' . str_pad($paymentId, 9, '0', STR_PAD_LEFT);
            $seatNumber = "12A"; // You can make this dynamic later

            // Redirect to ticket page with all details
            header("Location: ../frontend/ticket.html?" . http_build_query([
                'name' => $passengerName,
                'ticketName' => $ticketName,
                'price' => '$' . number_format($ticketPrice, 2),
                'seat' => $seatNumber,
                'ticket' => $ticketNumber

                'from' => $_POST['fromLocation'] ?? $_GET['fromLocation'] ?? '',
                'to' => $_POST['toLocation'] ?? $_GET['toLocation'] ?? '',
                'date' => $_POST['travelDate'] ?? $_GET['travelDate'] ?? ''
                            
            ]));
            exit();

        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        } catch (Exception $e) {
            $error_message = $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Payment - BookMyTickets</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f7fa;
      margin: 0;
      padding: 0;
    }
    .container {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      min-height: 100vh;
      padding: 30px;
      gap: 25px;
    }
    .payment-section, .summary-section {
      background: white;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      padding: 25px;
    }
    .payment-section {
      flex: 2;
      max-width: 600px;
    }
    .summary-section {
      flex: 1;
      max-width: 350px;
    }
    h2 {
      color: #0b66ff;
      margin-bottom: 10px;
    }
    .form-group {
      margin-bottom: 15px;
    }
    label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
    }
    input, select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 14px;
    }
    .btn {
      background-color: #0b66ff;
      color: white;
      border: none;
      border-radius: 8px;
      padding: 12px;
      width: 100%;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      transition: 0.3s;
    }
    .btn:hover {
      background-color: #054dcc;
    }
    .tabs {
      display: flex;
      gap: 10px;
      margin-bottom: 20px;
    }
    .tab {
      flex: 1;
      text-align: center;
      background: #eaeaea;
      padding: 10px;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }
    .tab.active {
      background: #0b66ff;
      color: white;
    }
    .tab-content {
      display: none;
    }
    .tab-content.active {
      display: block;
    }
    .summary-section h3 {
      color: #333;
      margin-top: 0;
    }
    .summary-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 10px;
      font-size: 15px;
    }
    .total {
      font-weight: bold;
      border-top: 1px solid #ccc;
      padding-top: 10px;
      margin-top: 10px;
    }
    .date-time {
      margin-top: 15px;
      font-size: 14px;
      color: #555;
    }
    .summary-section {
  background: #fff;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.summary-section h3 {
  color: #0b66ff;
  margin-bottom: 12px;
}

.summary-item {
  display: flex;
  justify-content: space-between;
  margin: 6px 0;
  font-size: 15px;
}

.summary-item.total {
  font-weight: 600;
  border-top: 1px solid #eee;
  padding-top: 8px;
}

.date-time {
  margin-top: 10px;
  font-size: 14px;
  color: #333;
}

.muted {
  font-size: 13px;
  color: #777;
}

.logos img {
  filter: grayscale(0.2);
  transition: transform 0.2s;
}

.logos img:hover {
  transform: scale(1.05);
}
footer{ text-align:center; padding:18px; color:var(--muted); font-size:0.9rem }
    @media (max-width:900px){
      .container{flex-direction:column}
      .summary, .form-wrap{ width:100% }
    }
    .logos{ display:flex; gap:10px; margin-top:12px; flex-wrap:wrap;}
    .logos img{ width:64px; height:auto; border-radius:6px; background:white; padding:6px; box-shadow:0 2px 6px rgba(2,10,30,0.03); }
  </style>
</head>
<body>

<div class="container">
  <!-- Payment Form -->
  <div class="payment-section">
    <h2>Complete Payment</h2>
    <br>
    <div class="tabs">
      <div class="tab active" data-tab="card">💳 Card Payment</div>
      <div class="tab" data-tab="upi">📱 UPI / QR</div>
    </div>

    <div id="card" class="tab-content active">
      <form id="cardForm" method="POST">
        <input type="hidden" name="method" value="Card"> <!-- ✅ Added -->
        <input type="hidden" name="ticketName" value="<?= htmlspecialchars($ticketName) ?>">
        <input type="hidden" name="ticketPrice" value="<?= htmlspecialchars($ticketPrice) ?>">

        <input type="hidden" name="fromLocation" value="<?= $_GET['fromLocation'] ?? '' ?>">
        <input type="hidden" name="toLocation" value="<?= $_GET['toLocation'] ?? '' ?>">
        <input type="hidden" name="travelDate" value="<?= $_GET['travelDate'] ?? '' ?>">

        <div class="form-group">
          <label>Passenger Name</label>
          <input type="text" name="name" required>
        </div>

        <div class="form-group">
          <label>Card Holder Name</label>
          <input type="text" name="cardName" required>
        </div>
        <div class="form-group">
          <label>Card Number</label>
          <input id="card-number" type="text" inputmode="numeric" name="cardNumber" maxlength="19" placeholder="XXXX XXXX XXXX XXXX" required>
        </div>
        <div class="form-group" style="margin-top:6px;">
          <label>Expiry Date</label>
          <input id="expiry" type="text" maxlength="5" placeholder="MM/YY" required>
        </div>
        <div class="form-group" >
          <label>CVV</label>
          <input id="cvv" type="password" name="cardCVV" maxlength="3" placeholder="***" required>
        </div>

        <div style="margin-top:12px; display:flex; gap:12px; align-items:center;">
        <button type="submit" class="btn">Pay Now ₹<?= htmlspecialchars($ticketPrice) ?></button>
        <button class="btn secondary" type="button" id="save-card-btn">Save card (optional)</button>
        <div id="card-msg" style="margin-left:8px" class="muted"></div>
        </div>
      </form>
    </div>

    <div id="upi" class="tab-content">
      <form id="upiForm" method="POST">
        <input type="hidden" name="method" value="UPI"> <!-- ✅ Added -->
        <input type="hidden" name="ticketName" value="<?= htmlspecialchars($ticketName) ?>">
        <input type="hidden" name="ticketPrice" value="<?= htmlspecialchars($ticketPrice) ?>">

        <input type="hidden" name="fromLocation" value="<?= $_GET['fromLocation'] ?? '' ?>">
        <input type="hidden" name="toLocation" value="<?= $_GET['toLocation'] ?? '' ?>">
        <input type="hidden" name="travelDate" value="<?= $_GET['travelDate'] ?? '' ?>">

        <div class="form-group">
          <label>Passenger Name</label>
          <input type="text" name="name" required>
        </div>
        <div class="form-group">
          <label>Enter UPI ID</label>
          <input type="text" name="upiId" placeholder="example@upi" required>
        </div>

        <div style="font-weight:700">Scan QR to pay</div>

        <div style="text-align:center; margin:5px 0;">
          <img src="../assets/img/Screenshot 2025-10-27 154328.png" alt="UPI QR" style="width:210px; height:auto; border-radius:8px;">
        </div>
        <div style="flex:1">
              <div style="background:#fbfdff;border-radius:10px;padding:12px;border:1px solid rgba(11,102,255,0.06);">
                <h4 style="margin:0 0 6px">How to pay</h4>
                <ol style="margin:0 0 0 18px;color:var(--muted)">
                  <li>Open your UPI app (GPay / Paytm / PhonePe).</li>
                  <li>Scan the QR code shown on the Top.</li>
                  <li>Complete the payment for the exact amount.</li>
                  <li>Once successful, click <strong>Proceed to Ticket</strong> to confirm.</li>
                </ol>
                <div class="notice">If you close this page mid-payment, contact support with your booking reference.</div>
              </div>
            </div>
        <br><br>
        <button type="submit" class="btn">Proceed to Pay ₹<?= htmlspecialchars($ticketPrice) ?></button>
      </form>
    </div>
  </div>

  <!-- Booking Summary -->
  <div class="summary-section">
    <h3>Booking Summary</h3>
    <div class="summary-item">
      <span>Event</span>
      <span><?= htmlspecialchars($ticketName) ?></span>
    </div>
    <div class="summary-item">
      <span>Price</span>
      <span>₹<?= htmlspecialchars($ticketPrice) ?></span>
    </div>
    <div class="summary-item">
      <span>Service Fee</span>
      <span>₹10</span>
    </div>
    <div class="summary-item total">
      <span>Total</span>
      <span>₹<?= htmlspecialchars($ticketPrice + 10) ?></span>
    </div>
    <div class="date-time">
      <strong>Date:</strong> <?= $currentDate ?><br>
      <strong>Time:</strong> <?= $currentTime ?>
    </div>

    <div style="margin-top:18px;">
    <div class="muted">Accepted Cards & UPI</div>
    <div class="logos" style="margin-top:8px; display:flex; flex-wrap:wrap; gap:8px; align-items:center;">
      <img src="https://upload.wikimedia.org/wikipedia/commons/0/04/Visa.svg" alt="Visa" height="22">
      <img src="https://upload.wikimedia.org/wikipedia/commons/2/2a/Mastercard-logo.svg" alt="Mastercard" height="22">
      <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAQsAAAC9CAMAAACTb6i8AAABAlBMVEX///83OYv0byAJekQ1N4ocH4IvMYgjJoQzNYkxM4keIYInKYUsLocpK4YgI4O3t9H19fnt7fQAdz/0ZwDk5O4Aby8ZHIHY2Ob9bh3f3+vq6vLz8/iUlbw6PI1gYZ92d6tqa6TJydyDhLJVVpmur8yio8RHSZNzdKn/bh1OT5adncFBQpBCkGb0+feys86HiLRYWZvBcisQFIBkZaH96+LQ4tn2kmH4onr83tD5s5TExNr+9vE4eUD70b76w6r1gkV2q44AAHyJuaDA18ultpv0dCjHciqwcy4rhlecdDKGdTZdnXpvdjlZeD2jxrMmeUHfcCTRcSjd6uONiln4qobzVQA4K1r1AAAK10lEQVR4nO2bCXfbNhLHJZn3Jeqwblm3HMmJ7dqOndDbpFd2e+xmr/b7f5XlAWAGpI5H0t1WefN77bNDiSD4x2AwM4ArFYIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCOI06Xf7/fZg0BgOff+P7ssfjaN6lhpjqZZnO1p1vN7c3l/OR8tZIXHmd55u267jaKZSHY+v1pvF7f10frMK2i/d9xemrVYxihL+ZxiGaWqOq3cuC3R/HDUREremxK0lzdmquf1Tm97SrR7A7GzzNpgSV1bateu/x0u8EPfmIS2qVXWUs8HD4lbzi/t/w7eUw1pUO918LR4TtzN74Vd4eGC/fFeyoZl+RIqqOc3VoG8fEddYl+xymg9P7Jfzt+UaunGOaaHouRqceccaVF/YMB5/e45/vj1/Xa6hK+NY16tqLt8/Oiquk9cDHaHVSgzju2Y5w0g7/Wg5LKfF+qi45kWZHmd416q1YsN43WyWMgzs9O0o1DLHY8ez8NjmmyMDJK4ZBW8hqmVjf/rCDuN9qEVkGG/Oz85KGcYUOqkHDW4A7a2Nur7I02Ad7jQvB8OoRX/YDi7c302Lj7VarRUuJd+HWpQyDHD6ioavr8AyYHr7fjZjGfq+dHmCxMU+8hKuS3Nk2L0eTe5vN2HYP51v63KY63MOPPG5FWkRGsY3zbNShoGcvjbHH3ThAy95pYlj67puu+bVEn1v0tEjbNseJheQuDp+hT560g2/2FgtPM8N8xYjjtM1x1ah9bYWPzGkg7tWv0ue6N4N4n9/HWkRGcZZRAnDQE5fD/Zo4SaqdWKvGv6vX8PXllby2ooxZvdZu4e/MoAPbNZAY6K6GUdreAumaldVmB93blBDVSN5osMEeurFWjxF7qKUYSCnbw3xBxCCsY6Aap0G6hi3Am5VeG6tcINIXDWZCEtVSwuRaGj6qTs0pMWS+yPejU+1mNYPzXKGgZx+yqHB+qIOZNXwF2disHnKdQvielLsvnJTT5qr++JT7TatBZojXGvuxR5aiRa1r87OShkGcvpY+5AFfyc3uT7oVFOdiABjYYr5IK7sLpAFunFyNkGxf+grJF2SDKgthEZabLmkNmv9A9ei95ezUoaBnL57jT+45j3lfuBaqIZXhzF/BeMquRDAGxq3uMEApkgnkm0L/3b09WJTxSFN8ggwWtBiqLMn2nwCPvZqL2MYKI3Cr+iP+JAobG7Dkqh4MNwQtHKrugEXIEXaDU08KU71usLMFHUbO6rBDdio1Y/vAbuY8HZ49sSHKAzAuRQlDQM5/bCLs36/350Fy9FUjJEi8nWXvwteHcCp8EVoDOK6qGhT18CNxE2uI2mjEprpCq8CRmrFcvvCxoQWwlQsvui9Ay3KGcYWh9qm7lmWp+uuY4r3tnlHwY+5aHWASkUnWYRwdqPNu6G23Vl9NXF0kCipAGwc3fN0VzM3sHqJJcjYJBeE1Zpci0tmdqYIhd8jLUoZxuJwGuVuxOoJThKvDuLNee+lkpYWaetF0Rm6qDAnuwOhLJ9wRlqLLn8glJc+1movYhjDA4XJEB2tZBuumuLCRRSDMN8wPVLSCqVcVvaQXZ+vhBaXyYUL1rx2yW96bmEtShhGcLCkhRdZcGPmPVwFR8kd77GSVtWZ7uvMyhP3cn8tBoBV1mZs7BRV2OvXkhYlDGO+O+5LDUYExCHYXUClQ00uHC1pObtT3kEw191sksjNgGvBQxQXVqinnqTFq8KGUT08ijYYxkSohtwFrEJ8bTlW0nJv013w+8Ho3rRsDXVFrBpTWQse8+CE+lOt9iKG0T/sLsL3FmKIlQUHk7AKcWM5XNJSVCkTrjTqNxvbsmHZ4q3xqE+MQKI1d6UoN3xopbQo6jEkp2+Ypmmky3sd5sT6wgJwMCmmc9VKjKVhVfdj2ApOhPujtWo7xi7LtHgNQ/ij+Kkj1l2+ZkV8SGsBhvF9Li3wPoa+ub+/3Vzpqo2Hlts+qIaCSRRLMKNF2U1KB9NVx3gBmS06zj4bUkz+rREOOBp8Hwdv1zz20loUMwwfBUAeH7JhcIUU4lkG1KRsGFtIPPkEnyBfrET7p64dxm+qZS4mK5yz+veqFHJoru6hqtck84Qos52yxjXk0isZs6jV/lrEMNAmkYl9Gtok4FqAS1ShBgfOgccDyHMa2sV8tLquz7p9VO1IGJhYM8caz5ezNkQmkCQKc1TGYj2Vqifvslr0mBbNs//k0AI5fWm/F0UdbI7AbEAlcZTLsFCyj9yFdmAbwQAbMLzxKM7DkOkJdwFzTtFEbufijO99RotXf2NS/PgmhxRo/BUPl7SgbMBzZfADypX4GkwIXtyBSZMuhkjMYQzchUiOhZUphvhmAM91FK4KbupjRoqfCi0jqKQlFybR8LK1ErYZ4ZttkXQLf4p8sbd/lxCKQlUbOWKIVcAfoFnMXZuHLfg5bRavfma+4pdcUmCn70o5AuoBC61vxUvCeoZCCfYttGGv2PufC1MTh/MQAqPOdDNhrCnFrakAvNb7O5Pim3xSYKevSpsSKOywEj/loAUnee/uGmyAF3ewL76s7AWmJqoeobAvqeMkT8nEK/Lxh1QAXvsHkyL30QOIepWx9AGIpFTjCw0Unyr6TRAsLywUHOwIwOV6oQSamhZk72jtQg6hndYC6lsxqRnCIovzz3mlQCMhbxKh0hQzYtlUNVuXChIiAN+g0tXeGgVepTxuAf4CbBQf9hik970lH58KwD/9s1DAGYFmgi1tEiErYDsjR06rqMkrDfduL0hs4bnmNJqCje5WQ9riPLiR0sKWNlykALz3L7aAFKnvoapLR1IbDRxzYzu0UCCf4msguu/Q+Qp89MX0HM22PAcnJbhslqo1GVdyUygA7/2bSdHME2FxoOqCc53Krl3FrAtz7Mk6vWGGXvLAippK65XMYQ981sOXtUgf50EV8K94sJkrwmIgH5AaRWQwbIz81AaX0bnxYY7xCjgssgfPa1xn8jfF9UAfaWBkLdLHxiAALxhscpC70GW58Q4rW2tH2Hkq9jpyECIg6CQDOUR7yqlOSww7KWF1ZdUGY5ECVkmLTNFYBOAFg03BtBMdE4i5kz/ZqOKTb3kaNFFFhOxYyfHMOzvMQm1dV1n0M/sWGtxb3o2o36H1WFMXsVlZUVIbtmdLhz8lLdz0sVAegPcKBpuC/iwI6gmB/MkQPoEooT623CgFt7RR4miHy+XyOrx3Nmvz22bitmHlELOxp8WHLVzPnTNPuZ1sV8v4XhxMYd9pjFPN8AD81X8LBpvF6S6j3uY89bqHYLKIDuGsjrWGY63MOUgWgLcefmwWCzZPC7SYa9P0h0+xFr2H5AhK/mDzxICETVEz8y6qgPc+PVc+nxcLNk+LFUqYM+44CsBbH8NffmkWCzZPh3YwGqPDkdktpjAAbz1GvzQLBpunwkRXdRSZK2r2D3oee8nB57fnxYLNUyFIxWPeKvudVuvX+Ofn82LB5qkwkfd5s2tIGID/9j755XXJPw34syPvyhrVHTX1Xz8kP9984VLI+7yGvuuv/96xn1/0/Kik9nlNu3/8ji8XdCxE61wcTm6+dC48N0padUvVJi+TAJ0ug+VqFW3FZndiCYIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgCIIgTon/AU26zyO78FOeAAAAAElFTkSuQmCC" alt="RuPay" height="22">
      <img src="data:image/png;base64,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" alt="Paytm" height="22">
      <img src="data:image/jpeg;base64,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" alt="GPay" height="22">
      <img src="data:image/png;base64,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" alt="PhonePe" height="22">
    </div>

    <div style="margin-top:14px;">
      <div class="muted">Security</div>
      <div style="margin-top:6px;">
        <small style="color:#555;">
          Payments are secure. We never store full card details or CVV. 
          For production, integrate a PCI-compliant payment gateway (Razorpay / Stripe / PayU).
        </small>
      </div>
    </div>
  </div>

  </div>
</div>
<footer>Need help? Contact support at <a href="mailto:support@bookmytickets.example">support@TravelBuddy.com</a></footer>
<script>
    // === Tab Switcher ===
    document.querySelectorAll('.tab').forEach(tab => {
      tab.addEventListener('click', () => {
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(tc => tc.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById(tab.dataset.tab).classList.add('active');
      });
    });

    // === Card Validation ===
    const cardForm = document.getElementById("cardForm");
    const cardMsg = document.getElementById("cardMsg");

    cardForm.addEventListener("submit", e => {
      const cardNumber = cardForm.cardNumber.value.trim();
      const expiry = cardForm.expiry.value.trim();
      const cvv = cardForm.cvv.value.trim();
      const name = cardForm.name.value.trim();

      const cardNumberRegex = /^[0-9]{16}$/;
      const expiryRegex = /^(0[1-9]|1[0-2])\/[0-9]{2}$/;
      const cvvRegex = /^[0-9]{3}$/;

      cardMsg.textContent = "";
      if (!name || !cardNumberRegex.test(cardNumber) || !expiryRegex.test(expiry) || !cvvRegex.test(cvv)) {
        e.preventDefault();
        cardMsg.textContent = "Invalid card details. Please check your input.";
        return false;
      }

      function formatCardNumberInput(value) {
  // remove non-digits
  const digits = value.replace(/\D/g, '');
  // group into 4s
  return digits.replace(/(.{4})/g, '$1 ').trim();
}


      const [mm, yy] = expiry.split("/").map(Number);
      const expDate = new Date(2000 + yy, mm);
      if (expDate < new Date()) {
        e.preventDefault();
        cardMsg.textContent = "Card expired. Please use a valid card.";
        return false;
      }
    });

    document.getElementById('card-number').addEventListener('input', function(e) {
        // Remove all non-digits
        let value = this.value.replace(/\D/g, '');
        // Limit to 16 digits
        if (value.length > 16) {
            value = value.substring(0, 16);
        }
        // Add space every 4 digits
        this.value = value.replace(/(\d{4})/g, '$1 ').trim();
    });

    document.getElementById('expiry').addEventListener('input', function(e) {
        let value = this.value.replace(/\D/g, '');
        
        // Limit to 4 digits
        if (value.length > 4) {
            value = value.substring(0, 4);
        }
        
        // Add slash after 2 digits
        if (value.length > 2) {
            value = value.substring(0, 2) + '/' + value.substring(2);
        }
        
        this.value = value;
        
        // Validate month (01-12)
        if (value.length >= 2) {
            const month = parseInt(value.substring(0, 2), 10);
            if (month < 1 || month > 12) {
                this.setCustomValidity('Month must be between 01 and 12');
            } else {
                this.setCustomValidity('');
            }
        }
    });

    // === UPI Validation ===
    const upiForm = document.getElementById("upiForm");
    const upiMsg = document.getElementById("upiMsg");

    upiForm.addEventListener("submit", e => {
      const name = upiForm.passengerName.value.trim();
      const upiId = upiForm.upiId.value.trim();
      const upiRegex = /^[\w.-]+@[\w.-]+$/;

      upiMsg.textContent = "";
      if (!name || !upiRegex.test(upiId)) {
        e.preventDefault();
        upiMsg.textContent = "Invalid UPI ID format.";
        return false;
      }
    });
  </script>
</body>
</html>