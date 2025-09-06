<?php
session_start();
include 'includes/dp.php';

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
            $stmt = $conn->prepare("INSERT INTO payments (passenger_name, ticket_type, price) VALUES (?, ?, ?)");
            $stmt->execute([$passengerName, $ticketName, $ticketPrice]);

            // Generate ticket details
            $paymentId = $conn->lastInsertId();
            $ticketNumber = 'TK-' . str_pad($paymentId, 9, '0', STR_PAD_LEFT);
            $seatNumber = "12A"; // You can make this dynamic later

            // Redirect to ticket page with all details
            header("Location: ticket.html?" . http_build_query([
                'name' => $passengerName,
                'ticketName' => $ticketName,
                'price' => '$' . number_format($ticketPrice, 2),
                'seat' => $seatNumber,
                'ticket' => $ticketNumber
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
    <title>Complete Your Booking</title>
    <style>
        .error { color: #dc3545; margin: 10px 0; }
        .payment-container { max-width: 600px; margin: 30px auto; padding: 20px; border: 1px solid #ddd; border-radius: 5px; }
        .payment-form label { display: block; margin: 10px 0 5px; font-weight: bold; }
        .payment-form input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; }
        .payment-form button { background: #0066cc; color: white; border: none; padding: 12px 20px; cursor: pointer; border-radius: 4px; }
        .payment-form button:hover { background: #0052a3; }
        .ticket-summary { 
            background: #f8f9fa; 
            padding: 15px; 
            margin-bottom: 20px; 
            border-radius: 4px;
            border-left: 4px solid #0066cc;
        }
        #ticket-price {
            font-weight: bold;
            color: #0066cc;
        }
    </style>

</head>
<body>
    <div class="payment-container">
        <h1>Complete Your Booking</h1>
        
        <?php if (!empty($error_message)): ?>
            <div class="error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <div class="ticket-summary">
            <h3>Booking Summary</h3>
            <p><strong>Ticket Type:</strong> <?php echo htmlspecialchars($ticketName); ?></p>
            <p><strong>Price:</strong> <span id="ticket-price"><?php 
                echo $ticketPrice ? '$' . number_format(floatval($ticketPrice), 2) : ''; 
            ?></span></p>
        </div>

        <form id="payment-form" method="POST" class="payment-form">
            <h3>Personal Information</h3>
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($passengerName); ?>" required>

            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" required>

            <h3>Payment Details</h3>
            <label for="card-number">Card Number</label>
            <input type="text" id="card-number" name="card_number" placeholder="1234 5678 9012 3456"  maxlength="19" required>

            <div style="display: flex; gap: 15px;">
                <div style="flex: 0.8;">
                    <label for="expiry">Expiry Date</label>
                    <input type="text" id="expiry" name="expiry" placeholder="MM/YY" maxlength="5" required>
                </div>
                <div style="flex: 1;">
                    <label for="cvv">CVV</label>
                    <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="3" required>
                </div>
            </div>

            <!-- Hidden fields to preserve values -->
            <input type="hidden" name="ticketName" value="<?php echo htmlspecialchars($ticketName); ?>">
            <input type="hidden" name="ticketPrice" value="<?php echo htmlspecialchars($ticketPrice); ?>">

            <div class="captcha-wrapper">
                <canvas id="captchaCanvas" width="100" height="40"></canvas>
                <button onclick="generateCaptcha()" class="refresh-btn">↻</button>
            </div>

            <input type="text" id="captchaInput" placeholder="Enter CAPTCHA" class="form-control mb-2" />
            <p id="captchaResult" class="captcha-message"></p>

     


            <button type="submit" onclick="validateCaptcha()">Pay Now</button>
        </form>
    </div>

    <script>
    // Card number formatting - only numbers, max 16 digits (19 characters with spaces)
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

    // Expiry date formatting - exactly MM/YY (4 digits)
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

    // CVV formatting - exactly 3 digits
    document.getElementById('cvv').addEventListener('input', function(e) {
        this.value = this.value.replace(/\D/g, '');
        if (this.value.length > 3) {
            this.value = this.value.substring(0, 3);
        }
    });

    
    </script>
    <script src="payment.js"></script>
</body>
</html>