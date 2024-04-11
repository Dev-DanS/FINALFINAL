<?php
session_start();
require_once "../db/tdbconn.php";

$bookingid = $_SESSION["bookingid"];
$toda = $_SESSION["toda"];
$status = 'accepted';

if(isset($_POST['platenumber'])) {
    $bodyNumber = $_POST['platenumber'];
    $currentDay = date('N');

    $dayRanges = [
        1 => [1, 2],   // Monday
        2 => [3, 4],   // Tuesday
        3 => [5, 6],   // Wednesday
        4 => [7, 8],   // Thursday
        5 => [9, 0]    // Friday
    ];

   
    if(in_array(substr($bodyNumber, -1), $dayRanges[$currentDay])) {
        echo "Please choose another driver. Today's coding for last digit is: " . implode(", ", $dayRanges[$currentDay]);

    } else {
        // Insert the booking
        $stmt = $conn->prepare("SELECT platenumber FROM driverinfo WHERE bodynumber = ?");
        $stmt->bind_param("s", $bodyNumber);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $platenumber = $row['platenumber'];

            $updateStmt = $conn->prepare("UPDATE booking SET status = ?, platenumber = ? WHERE bookingid = ?");
            $updateStmt->bind_param("ssi", $status, $platenumber, $bookingid);
            $updateStmt->execute();
            if($updateStmt->affected_rows > 0) {
                echo "Booking confirmed successfully.";
            } else {
                echo "Failed to confirm booking.";
            }
            $updateStmt->close();
        } else {
            echo "Driver not found.";
        }
        $stmt->close();
    }
}
?>
