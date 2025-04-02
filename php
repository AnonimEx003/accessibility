<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy Tracker - Green Future</title>
    <link rel="stylesheet" href="css/tracker.css">
    <?php include 'header.php'; ?>
</head>
<body>

    <!-- Page Title -->
    <h1 class="page-title">Energy Tracker</h1>

    <!-- Energy Tracker Container -->
    <div class="tracker-container">

        <!-- Left Side: Energy Usage Form -->
        <div class="tracker-form-container">
            <form action="energy-tracker-result.php" method="POST" class="tracker-form">
                
                <!-- Energy Consumption Section -->
                <div class="input-field">
                    <label for="electricity-consumption">Electricity Consumption (kWh per month)</label>
                    <input type="number" id="electricity-consumption" name="electricity-consumption" placeholder="Enter your electricity consumption in kWh" required>
                </div>

                <!-- Heating Consumption Section -->
                <div class="input-field">
                    <label for="heating-consumption">Heating Consumption (kWh per month)</label>
                    <input type="number" id="heating-consumption" name="heating-consumption" placeholder="Enter your heating consumption in kWh" required>
                </div>

                <!-- Water Heating Section -->
                <div class="input-field">
                    <label for="water-heating-consumption">Water Heating Consumption (kWh per month)</label>
                    <input type="number" id="water-heating-consumption" name="water-heating-consumption" placeholder="Enter your water heating consumption in kWh" required>
                </div>

                <!-- Appliances Consumption Section -->
                <div class="input-field">
                    <label for="appliances-consumption">Appliances Consumption (kWh per month)</label>
                    <input type="number" id="appliances-consumption" name="appliances-consumption" placeholder="Enter your appliances consumption in kWh" required>
                </div>

                <button type="submit" class="calculate-button">Track Energy Usage</button>
            </form>
        </div>

        <!-- Right Side: Energy Usage Results -->
        <div class="results-container">
            <h2>Energy Usage Results</h2>
            <p><strong>Total Energy Consumption: </strong><span id="total-energy">0</span> kWh</p>
            <p><strong>Status: </strong><span id="energy-status">Please enter your data to calculate</span></p>

            <!-- Tips Section -->
            <h3>Energy Saving Tips</h3>
            <ul>
                <li>Switch off electrical appliances when not in use.</li>
                <li>Upgrade to energy-efficient appliances.</li>
                <li>Use smart thermostats to control heating and cooling.</li>
                <li>Insulate your home to reduce heating and cooling costs.</li>
            </ul>

            <!-- Previous Reports Section -->
            <h3>Previous Energy Reports</h3>
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Total Energy (kWh)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Display previous reports (this is an example, you should query from the database)
                    // Assuming a connection to the database is available and reports are stored in `energy_reports` table
                    include('db_connection.php');
                    $result = $conn->query("SELECT * FROM energy_reports ORDER BY date DESC LIMIT 5");
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['date']}</td>
                                <td>{$row['total_energy']} kWh</td>
                                <td>{$row['status']}</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <?php include 'footer.php'; ?>

</body>
</html>
<?php
session_start();
include('db_connection.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<p>Please log in to track your energy usage.</p>";
    exit;
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $electricity = $_POST['electricity-consumption'];
    $heating = $_POST['heating-consumption'];
    $water_heating = $_POST['water-heating-consumption'];
    $appliances = $_POST['appliances-consumption'];

    // Calculate total energy usage
    $total_energy = $electricity + $heating + $water_heating + $appliances;
    $date = date('Y-m-d');

    // Determine status based on energy consumption
    if ($total_energy < 200) {
        $status = "Low Consumption";
    } elseif ($total_energy <= 500) {
        $status = "Moderate Consumption";
    } else {
        $status = "High Consumption";
    }

    // Insert data into the database
    $stmt = $conn->prepare("INSERT INTO energy_reports (user_id, date, electricity, heating, water_heating, appliances, total_energy, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isiiiiis", $user_id, $date, $electricity, $heating, $water_heating, $appliances, $total_energy, $status);
    $stmt->execute();
    $stmt->close();
}

// Fetch previous energy records
$result = $conn->query("SELECT * FROM energy_reports WHERE user_id = $user_id ORDER BY date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Energy Tracker - Results</title>
    <link rel="stylesheet" href="css/tracker.css">
</head>
<body>

    <h1>Energy Tracker Results</h1>

    <p><strong>Total Energy Consumption: </strong><?= $total_energy ?> kWh</p>
    <p><strong>Status: </strong><?= $status ?></p>

    <h2>Previous Energy Usage</h2>
    <table border="1">
        <tr>
            <th>Date</th>
            <th>Electricity</th>
            <th>Heating</th>
            <th>Water Heating</th>
            <th>Appliances</th>
            <th>Total Energy</th>
            <th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $row['date'] ?></td>
                <td><?= $row['electricity'] ?> kWh</td>
                <td><?= $row['heating'] ?> kWh</td>
                <td><?= $row['water_heating'] ?> kWh</td>
                <td><?= $row['appliances'] ?> kWh</td>
                <td><strong><?= $row['total_energy'] ?> kWh</strong></td>
                <td><?= $row['status'] ?></td>
            </tr>
        <?php } ?>
    </table>

</body>
</html>

