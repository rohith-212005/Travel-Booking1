<?php
include 'db-connection.php';
session_start();

// 🔐 Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch logged-in user's name
$stmt = $conn->prepare("SELECT name FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$userName = $stmt->fetchColumn();
$stmt->closeCursor();

// Fetch payment/booking history for this user
$stmt2 = $conn->prepare("SELECT id, ticket_type, price, created_at FROM payments WHERE user_id = ? ORDER BY created_at DESC");
$stmt2->execute([$userId]);
?>
<html>
<head>
    <title>Booking History</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f4f6f9;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 90%;
            margin: auto;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0px 4px 6px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background: #007bff;
            color: white;
        }
        tr:hover {
            background: #f1f1f1;
        }
        .view-ticket-btn {
            background: #28a745;
            color: white;
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
        }
        .view-ticket-btn:hover {
            background: #218838;
        }
    </style>
</head>
<body>
    <h2><?php echo htmlspecialchars($userName); ?>'s Booking History</h2>
    <table>
        <tr>
            <th>Booking ID</th>
            <th>Ticket Type</th>
            <th>Price</th>
            <th>Date</th>
            <th>Ticket</th>
        </tr>
            <?php while ($row = $stmt2->fetch(PDO::FETCH_ASSOC)) { 
                $bookingId = $row['id'];
                $ticketType = $row['ticket_type'];
                $price = $row['price'];
                $createdAt = $row['created_at'];
                $ticketNumber = 'TK-' . str_pad($bookingId, 9, '0', STR_PAD_LEFT); ?>
            <tr>
                <td><?php echo $bookingId; ?></td>
                <td><?php echo htmlspecialchars($ticketType); ?></td>
                <td>₹<?php echo number_format($price, 2); ?></td>
                <td><?php echo $createdAt; ?></td>
                <td>
                    <a class="view-ticket-btn" href="../frontend/ticket.html?ticket=<?php echo $ticketNumber; ?>&name=<?php echo urlencode($userName); ?>&ticketName=<?php echo urlencode($ticketType); ?>&price=<?php echo urlencode(number_format($price,2)); ?>">View Ticket</a>
                </td>
            </tr>
            <?php } ?>
        </table>
        <?php $stmt2->closeCursor(); ?>
    </body>
    </html>
