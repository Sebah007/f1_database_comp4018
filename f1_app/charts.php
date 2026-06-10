<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>F1 Charts — Statistics</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    :root {
      --red: #e8002d;
      --dark: #0a0a0a;
      --card: #111111;
      --border: #222222;
      --text: #f0f0f0;
      --muted: #888888;
    }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { background: var(--dark); color: var(--text); font-family: 'Barlow', sans-serif; font-weight: 300; }
    nav {
      background: #000; border-bottom: 2px solid var(--red);
      padding: 0 2rem; display: flex; align-items: center; gap: 2rem;
      position: sticky; top: 0; z-index: 100; height: 56px;
    }
    .nav-logo { font-family: 'Formula1', 'Barlow Condensed', sans-serif; font-weight: 800; font-size: 20px; color: var(--red); text-transform: uppercase; }
    .nav-logo span { color: var(--text); }
    .nav-links { display: flex; gap: 0; }
    .nav-link {
      padding: 0 1rem; height: 56px; display: flex; align-items: center;
      font-family: 'Formula1', 'Barlow Condensed', sans-serif; font-weight: 600; font-size: 13px;
      letter-spacing: 0.08em; text-transform: uppercase; color: var(--muted);
      cursor: pointer; text-decoration: none;
      border-bottom: 2px solid transparent; margin-bottom: -2px;
      transition: color 0.15s;
    }
    .nav-link:hover { color: var(--text); }
    .nav-link.active { color: var(--red); border-bottom-color: var(--red); }
    .page { padding: 2rem; max-width: 1400px; margin: 0 auto; }
    h1 { font-family: 'Formula1', 'Barlow Condensed', sans-serif; font-weight: 800; font-size: 42px; text-transform: uppercase; letter-spacing: 0.04em; line-height: 1; margin-bottom: 0.25rem; }
    h1 .accent { color: var(--red); }
    .subtitle { color: var(--muted); font-size: 13px; letter-spacing: 0.06em; text-transform: uppercase; margin-bottom: 2rem; }
    .charts-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
    .chart-card { background: var(--card); border: 1px solid var(--border); border-radius: 4px; padding: 1.5rem; }
    .chart-title { font-family: 'Formula1', 'Barlow Condensed', sans-serif; font-weight: 700; font-size: 14px; letter-spacing: 0.1em; text-transform: uppercase; color: var(--red); margin-bottom: 1rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border); }
    .chart-wrap { position: relative; height: 300px; }
    @media (max-width: 900px) { .charts-grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body>

<nav>
  <div class="nav-logo">F1 <span>Database</span></div>
  <div class="nav-links">
    <a class="nav-link" href="index.php">Home</a>
    <a class="nav-link" href="index.php#schema">Schema</a>
    <a class="nav-link" href="index.php#queries">Queries</a>
    <a class="nav-link" href="index.php#insert">Insert</a>
    <a class="nav-link" href="index.php#modify">Modify</a>
    <a class="nav-link" href="index.php#delete">Delete</a>
    <a class="nav-link" href="index.php#diagram">E/R Diagram</a>
    <a class="nav-link active" href="charts.php">Charts</a>
  </div>
</nav>

<div class="page">
  <h1>Statistical <span class="accent">Charts</span></h1>
  <div class="subtitle">Visual analytics from the F1 database · 1950–2024</div>

  <div class="charts-grid">
    <div class="chart-card">
      <div class="chart-title">Top 10 Drivers by Total Points</div>
      <div class="chart-wrap"><canvas id="chart-driver-points"></canvas></div>
    </div>
    <div class="chart-card">
      <div class="chart-title">Top 10 Constructors by Total Points</div>
      <div class="chart-wrap"><canvas id="chart-constructor-points"></canvas></div>
    </div>
    <div class="chart-card">
      <div class="chart-title">Races per Season (Last 20 Seasons)</div>
      <div class="chart-wrap"><canvas id="chart-races-season"></canvas></div>
    </div>
    <div class="chart-card">
      <div class="chart-title">Top 10 Circuits by Times Hosted</div>
      <div class="chart-wrap"><canvas id="chart-circuits"></canvas></div>
    </div>
    <div class="chart-card">
      <div class="chart-title">Top 10 Drivers by Race Wins</div>
      <div class="chart-wrap"><canvas id="chart-driver-wins"></canvas></div>
    </div>
    <div class="chart-card">
      <div class="chart-title">Nationalities of Drivers (Top 10)</div>
      <div class="chart-wrap"><canvas id="chart-nationalities"></canvas></div>
    </div>
  </div>
</div>

<?php
require_once 'db.php';

// Chart 1: Top 10 drivers by total points
$stmt = $pdo->query("
  SELECT CONCAT(p.forename, ' ', p.surname) AS driver, SUM(r.points) AS total
  FROM RESULT r
  JOIN DRIVER d ON r.driverID = d.driverID
  JOIN PERSON p ON d.driverID = p.personID
  GROUP BY d.driverID, p.forename, p.surname
  ORDER BY total DESC LIMIT 10
");
$driverPoints = $stmt->fetchAll();

// Chart 2: Top 10 constructors by total points
$stmt = $pdo->query("
  SELECT c.name, SUM(r.points) AS total
  FROM RESULT r
  JOIN CONSTRUCTOR c ON r.constructorID = c.constructorID
  GROUP BY c.constructorID, c.name
  ORDER BY total DESC LIMIT 10
");
$constructorPoints = $stmt->fetchAll();

// Chart 3: Races per season (last 20)
$stmt = $pdo->query("
  SELECT year, COUNT(*) AS races
  FROM RACE
  GROUP BY year
  ORDER BY year DESC LIMIT 20
");
$racesSeason = array_reverse($stmt->fetchAll());

// Chart 4: Top 10 circuits by times hosted
$stmt = $pdo->query("
  SELECT ci.name, COUNT(r.raceID) AS hosted
  FROM CIRCUIT ci
  JOIN RACE r ON ci.circuitID = r.circuitID
  GROUP BY ci.circuitID, ci.name
  ORDER BY hosted DESC LIMIT 10
");
$circuits = $stmt->fetchAll();

// Chart 5: Top 10 drivers by wins
$stmt = $pdo->query("
  SELECT CONCAT(p.forename, ' ', p.surname) AS driver,
         COUNT(*) AS wins
  FROM RESULT r
  JOIN DRIVER d ON r.driverID = d.driverID
  JOIN PERSON p ON d.driverID = p.personID
  WHERE r.positionOrder = 1
  GROUP BY d.driverID, p.forename, p.surname
  ORDER BY wins DESC LIMIT 10
");
$driverWins = $stmt->fetchAll();

// Chart 6: Driver nationalities top 10
$stmt = $pdo->query("
  SELECT nationality, COUNT(*) AS cnt
  FROM PERSON p
  JOIN DRIVER d ON p.personID = d.driverID
  GROUP BY nationality
  ORDER BY cnt DESC LIMIT 10
");
$nationalities = $stmt->fetchAll();
?>

<script>
const colors = [
  '#e8002d','#378ADD','#22c55e','#f59e0b','#a855f7',
  '#06b6d4','#f97316','#84cc16','#ec4899','#14b8a6'
];

const chartDefaults = {
  plugins: { legend: { display: false } },
  scales: {
    x: { ticks: { color: '#888', font: { family: 'Barlow Condensed', size: 11 } }, grid: { color: '#1a1a1a' } },
    y: { ticks: { color: '#888', font: { family: 'Barlow Condensed', size: 11 } }, grid: { color: '#1a1a1a' } }
  }
};

// Chart 1: Driver points
new Chart(document.getElementById('chart-driver-points'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_column($driverPoints, 'driver')) ?>,
    datasets: [{ data: <?= json_encode(array_column($driverPoints, 'total')) ?>, backgroundColor: colors }]
  },
  options: { ...chartDefaults, responsive: true, maintainAspectRatio: false }
});

// Chart 2: Constructor points
new Chart(document.getElementById('chart-constructor-points'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_column($constructorPoints, 'name')) ?>,
    datasets: [{ data: <?= json_encode(array_column($constructorPoints, 'total')) ?>, backgroundColor: colors }]
  },
  options: { ...chartDefaults, responsive: true, maintainAspectRatio: false }
});

// Chart 3: Races per season
new Chart(document.getElementById('chart-races-season'), {
  type: 'line',
  data: {
    labels: <?= json_encode(array_column($racesSeason, 'year')) ?>,
    datasets: [{
      data: <?= json_encode(array_column($racesSeason, 'races')) ?>,
      borderColor: '#e8002d', backgroundColor: 'rgba(232,0,45,0.1)',
      tension: 0.3, fill: true, pointBackgroundColor: '#e8002d'
    }]
  },
  options: { ...chartDefaults, responsive: true, maintainAspectRatio: false }
});

// Chart 4: Circuits
new Chart(document.getElementById('chart-circuits'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_column($circuits, 'name')) ?>,
    datasets: [{ data: <?= json_encode(array_column($circuits, 'hosted')) ?>, backgroundColor: colors }]
  },
  options: { ...chartDefaults, responsive: true, maintainAspectRatio: false, indexAxis: 'y' }
});

// Chart 5: Driver wins
new Chart(document.getElementById('chart-driver-wins'), {
  type: 'bar',
  data: {
    labels: <?= json_encode(array_column($driverWins, 'driver')) ?>,
    datasets: [{ data: <?= json_encode(array_column($driverWins, 'wins')) ?>, backgroundColor: colors }]
  },
  options: { ...chartDefaults, responsive: true, maintainAspectRatio: false }
});

// Chart 6: Nationalities (pie)
new Chart(document.getElementById('chart-nationalities'), {
  type: 'doughnut',
  data: {
    labels: <?= json_encode(array_column($nationalities, 'nationality')) ?>,
    datasets: [{ data: <?= json_encode(array_column($nationalities, 'cnt')) ?>, backgroundColor: colors }]
  },
  options: {
    responsive: true, maintainAspectRatio: false,
    plugins: { legend: { display: true, position: 'right', labels: { color: '#888', font: { family: 'Barlow Condensed', size: 11 } } } }
  }
});
</script>
</body>
</html>
