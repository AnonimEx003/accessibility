<?php
// Initialize variables to avoid "Undefined variable" warnings
$electricity_emissions = 0;
$car_emissions = 0;
$flight_emissions = 0;
$diet_annual_emissions = 0;
$total_emissions = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture user inputs
    $electricity_kwh = isset($_POST["electricity-consumption"]) ? floatval($_POST["electricity-consumption"]) : 0;
    $car_km = isset($_POST["car-mileage"]) ? floatval($_POST["car-mileage"]) : 0;
    $flights_km = isset($_POST["flight-mileage"]) ? floatval($_POST["flight-mileage"]) : 0;
    $diet = isset($_POST["diet-type"]) ? $_POST["diet-type"] : "mixed";

    // Carbon emission factors (kg CO2e per unit)
    $diet_emissions = [
        'vegan' => 1.5,       // kg CO2e per day
        'vegetarian' => 1.7,  // kg CO2e per day
        'mixed' => 2.2,       // kg CO2e per day
        'meat-based' => 2.5   // kg CO2e per day
    ];

    $flight_emission_factor = 0.25; // kg CO2e per km
    $car_emission_factor = 0.12; // kg CO2e per km
    $electricity_emission_factor = 0.5; // kg CO2e per kWh

    // Calculate emissions
    $diet_annual_emissions = $diet_emissions[$diet] * 365;
    $flight_emissions = $flights_km * $flight_emission_factor * 12;
    $car_emissions = $car_km * $car_emission_factor * 12;
    $electricity_emissions = $electricity_kwh * $electricity_emission_factor * 12;

    // Total carbon footprint
    $total_emissions = $diet_annual_emissions + $flight_emissions + $car_emissions + $electricity_emissions;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carbon Footprint Results</title>
    <link rel="stylesheet" href="css/footprint.css">
</head>
<body>

    <h1 class="page-title">Your Carbon Footprint Results</h1>

    <div class="results-container">
        <h2>Breakdown of Your Carbon Footprint</h2>
        <p><strong>Electricity Emissions:</strong> <?php echo number_format($electricity_emissions, 2); ?> kg CO2e</p>
        <p><strong>Car Emissions:</strong> <?php echo number_format($car_emissions, 2); ?> kg CO2e</p>
        <p><strong>Flight Emissions:</strong> <?php echo number_format($flight_emissions, 2); ?> kg CO2e</p>
        <p><strong>Diet Emissions:</strong> <?php echo number_format($diet_annual_emissions, 2); ?> kg CO2e</p>
        <hr>
        <h2><strong>Total Carbon Footprint:</strong> <?php echo number_format($total_emissions, 2); ?> kg CO2e per year</h2>
        
        <a href="footprint.php" class="calculate-button">Recalculate</a>
    </div>

</body>
</html>
