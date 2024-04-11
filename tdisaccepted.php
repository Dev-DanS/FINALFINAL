<?php
include('../php/session_dispatcher.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dispatcher | Preview</title>
    <link rel="icon" href="../img/logo3.png" type="image/png" />
    <link rel="stylesheet" href="../css/tdisaccepted.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous" />
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/965a209c77.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
</head>

<body>
    <?php
    include('disnav.php');
    include('tdisacceptedinfo.php');
    ?>

    <div id="map" style="width: 100%; height: 60vh;"></div>

    <div class="main">
        <div class="side1">
            <p class="pickup">
                <i class="fa-solid fa-location-dot fa-lg" style="color: #35a235;"></i> <?php echo $pickupAddress; ?>
            </p>
            <div class="change" id="change-location">
                <b>Fare: ₱<?php echo $fare; ?></b>
            </div>
        </div>

        <div class="line">
            <hr>
        </div>

        <div class="side2">
            <p class="destination">
                <i class="fa-solid fa-location-dot fa-lg" style="color: #c81e31;"></i> <?php echo $dropoffAddress; ?>
            </p>
        </div>
    </div>

    <form action="tdisacceptedback.php" method="POST">
        <div class="input-field">
            <!-- <input type="text" class="input" id="platenumber" name="platenumber" required autocomplete="on" />
            <label for="platenumber">platenumber</label> -->
            <div class="form-floating">
                <input type="text" class="form-control" name="platenumber" id="platenumber" placeholder="bodyNumber" required autocomplete="on">
                <label for="bodyNumber">Body Number</label>
            </div>
            <div class="confirm">
                <button type="submit" class="confirm-btn" id="next">
                    Confirm Booking
                </button>
            </div>

        </div>

    </form>

    <!-- <script>
        document.getElementById('confirmButton').addEventListener('click', function () {
            window.location.href = "newscan.php";
        });
    </script> -->

    <br>
    <?php
    include('../db/tdbconn.php');

    if (isset($_SESSION['bookingid'])) {
        $bookingid = $_SESSION['bookingid'];
        $sql = "SELECT pickuppoint, dropoffpoint FROM booking WHERE bookingid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $bookingid);
        $stmt->execute();
        $stmt->bind_result($pickuppoint, $dropoffpoint);
        $stmt->fetch();
        $stmt->close();
        $pickupCoords = explode(',', $pickuppoint);
        $dropoffCoords = explode(',', $dropoffpoint);
    } else {
        echo '<h1>Booking ID not set</h1>';
    }
    ?>

    <script>
        var map = L.map('map', {
            zoomControl: false
        });

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        const greenMarkerIcon = L.icon({
            iconUrl: "https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png",
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34],
        });

        const redMarkerIcon = L.icon({
            iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
            iconSize: [25, 41],
            iconAnchor: [12, 41],
            popupAnchor: [1, -34]
        });

        <?php if (isset($_SESSION['bookingid'])) { ?>
            var pickupMarker = L.marker([<?= $pickupCoords[0] ?>, <?= $pickupCoords[1] ?>], {
                icon: greenMarkerIcon
            }).addTo(map);
            var dropoffMarker = L.marker([<?= $dropoffCoords[0] ?>, <?= $dropoffCoords[1] ?>], {
                icon: redMarkerIcon
            }).addTo(map);

            pickupMarker.bindPopup('Pickup Point').openPopup();
            dropoffMarker.bindPopup('Dropoff Point').openPopup();

            map.setView([<?= $pickupCoords[0] ?>, <?= $pickupCoords[1] ?>], 15);

            // Calculate and display the shortest route
            var routeUrl = `https://router.project-osrm.org/route/v1/driving/${<?= $pickupCoords[1] ?>},${<?= $pickupCoords[0] ?>};${<?= $dropoffCoords[1] ?>},${<?= $dropoffCoords[0] ?>}?overview=full&geometries=geojson`;
            fetch(routeUrl)
                .then(response => response.json())
                .then(data => {
                    L.geoJSON(data.routes[0].geometry, {
                        style: {
                            weight: 5,
                            color: '#03b14e'
                        }
                    }).addTo(map);
                })
                .catch(error => console.error('Error fetching route data:', error));

        <?php } ?>
    </script>
</body>

</html>
