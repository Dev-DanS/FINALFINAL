<?php
session_start();
require_once "../db/tdbconn.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_platenumber = $_POST["platenumber"];
    $bookingid = $_SESSION["bookingid"];
    $toda = $_SESSION["toda"];
    $status = 'accepted';

    // Get platenumber from driverinfo
    $sql_select = "SELECT platenumber FROM driverinfo WHERE bodynumber = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        // Bind parameters
        $stmt_select->bind_param("s", $input_platenumber);

        // Execute the statement
        if ($stmt_select->execute()) {
            // Bind the result variable
            $stmt_select->bind_result($platenumber);

            // Fetch the result
            if ($stmt_select->fetch()) {
                // Free the result set
                $stmt_select->free_result();

                // Update booking table with the fetched platenumber
                $sql_update = "UPDATE booking SET status = ?, platenumber = ? WHERE bookingid = ?";
                if ($stmt_update = $conn->prepare($sql_update)) {
                    // Bind parameters
                    $stmt_update->bind_param("ssi", $status, $platenumber, $bookingid);

                    // Execute the statement
                    if ($stmt_update->execute()) {
                        // The update was successful
                        echo "Update successful.";
                        // Redirect to tdispending.php
                        header("Location: tdispending.php");
                        exit; // Ensure that no further code is executed after the redirect
                    } else {
                        // Handle the update error
                        echo "Update failed: " . $stmt_update->error;
                    }

                    // Close the statement
                    $stmt_update->close();
                } else {
                    // Handle the statement preparation error
                    echo "Statement preparation error: " . $conn->error;
                }
            } else {
                echo "No driver found with the given bodynumber.";
            }

            // Close the statement
            $stmt_select->close();
        } else {
            // Handle the select error
            echo "Select failed: " . $stmt_select->error;
        }
    } else {
        // Handle the statement preparation error
        echo "Statement preparation error: " . $conn->error;
    }

    // Close the database connection
    $conn->close();
}
?>
