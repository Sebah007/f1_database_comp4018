<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>F1 Database — COMP4018</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;600;700;800&family=Barlow:wght@300;400;500&display=swap" rel="stylesheet">
  <style>
    :root {
      --red: #e8002d;
      --dark: #0a0a0a;
      --card: #111111;
      --border: #222222;
      --text: #f0f0f0;
      --muted: #888888;
      --success: #22c55e;
      --warning: #f59e0b;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: var(--dark);
      color: var(--text);
      font-family: 'Barlow', sans-serif;
      font-weight: 300;
      min-height: 100vh;
    }

    /* NAV */
    nav {
      background: #000;
      border-bottom: 2px solid var(--red);
      padding: 0 2rem;
      display: flex;
      align-items: center;
      gap: 2rem;
      position: sticky;
      top: 0;
      z-index: 100;
      height: 56px;
    }

    .nav-logo {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 800;
      font-size: 20px;
      letter-spacing: 0.05em;
      color: var(--red);
      text-transform: uppercase;
      white-space: nowrap;
    }

    .nav-logo span { color: var(--text); }

    .nav-links { display: flex; gap: 0; flex: 1; }

    .nav-link {
      padding: 0 1rem;
      height: 56px;
      display: flex;
      align-items: center;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 600;
      font-size: 13px;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: var(--muted);
      cursor: pointer;
      border-bottom: 2px solid transparent;
      margin-bottom: -2px;
      transition: color 0.15s, border-color 0.15s;
      user-select: none;
    }

    .nav-link:hover { color: var(--text); }
    .nav-link.active { color: var(--red); border-bottom-color: var(--red); }

    /* MAIN */
    .page { display: none; padding: 2rem; max-width: 1400px; margin: 0 auto; }
    .page.active { display: block; }

    h1 {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 800;
      font-size: 42px;
      text-transform: uppercase;
      letter-spacing: 0.04em;
      line-height: 1;
      margin-bottom: 0.25rem;
    }

    h1 .accent { color: var(--red); }

    .subtitle {
      color: var(--muted);
      font-size: 13px;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      margin-bottom: 2rem;
    }

    /* CARDS */
    .card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 4px;
      padding: 1.5rem;
      margin-bottom: 1.5rem;
    }

    .card-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: 14px;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--red);
      margin-bottom: 1rem;
      padding-bottom: 0.75rem;
      border-bottom: 1px solid var(--border);
    }

    /* STATS ROW */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
    }

    .stat-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-top: 3px solid var(--red);
      padding: 1.25rem;
      border-radius: 4px;
    }

    .stat-value {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 800;
      font-size: 32px;
      color: var(--text);
      line-height: 1;
    }

    .stat-label {
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--muted);
      margin-top: 4px;
    }

    /* TABLES */
    .table-wrap { overflow-x: auto; }

    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 13px;
    }

    th {
      background: #1a1a1a;
      padding: 10px 14px;
      text-align: left;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 600;
      font-size: 11px;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--muted);
      border-bottom: 1px solid var(--border);
      white-space: nowrap;
    }

    td {
      padding: 10px 14px;
      border-bottom: 1px solid #1a1a1a;
      color: var(--text);
      font-weight: 300;
    }

    tr:hover td { background: #161616; }

    .badge {
      display: inline-block;
      padding: 2px 8px;
      border-radius: 2px;
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.05em;
      text-transform: uppercase;
    }

    .badge-red { background: rgba(232,0,45,0.15); color: var(--red); }
    .badge-green { background: rgba(34,197,94,0.15); color: var(--success); }
    .badge-gray { background: #222; color: var(--muted); }

    /* FORMS */
    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .form-group { display: flex; flex-direction: column; gap: 6px; }

    label {
      font-size: 11px;
      text-transform: uppercase;
      letter-spacing: 0.08em;
      color: var(--muted);
      font-weight: 500;
    }

    input, select, textarea {
      background: #1a1a1a;
      border: 1px solid var(--border);
      border-radius: 3px;
      padding: 8px 12px;
      color: var(--text);
      font-family: 'Barlow', sans-serif;
      font-size: 13px;
      transition: border-color 0.15s;
    }

    input:focus, select:focus, textarea:focus {
      outline: none;
      border-color: var(--red);
    }

    select option { background: #1a1a1a; }

    .btn {
      padding: 10px 24px;
      border: none;
      border-radius: 3px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: 13px;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      cursor: pointer;
      transition: opacity 0.15s;
    }

    .btn:hover { opacity: 0.85; }
    .btn-red { background: var(--red); color: #fff; }
    .btn-outline { background: transparent; border: 1px solid var(--border); color: var(--text); }
    .btn-green { background: var(--success); color: #000; }
    .btn-danger { background: #7f1d1d; color: #fff; }

    .btn-row { display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 1rem; }

    /* ALERTS */
    .alert {
      padding: 12px 16px;
      border-radius: 3px;
      font-size: 13px;
      margin-bottom: 1rem;
      display: none;
    }

    .alert.show { display: block; }
    .alert-success { background: rgba(34,197,94,0.1); border: 1px solid rgba(34,197,94,0.3); color: var(--success); }
    .alert-error { background: rgba(232,0,45,0.1); border: 1px solid rgba(232,0,45,0.3); color: var(--red); }
    .alert-warning { background: rgba(245,158,11,0.1); border: 1px solid rgba(245,158,11,0.3); color: var(--warning); }

    /* QUERY SELECTOR */
    .query-tabs {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
      margin-bottom: 1.5rem;
    }

    .query-tab {
      padding: 8px 16px;
      background: #1a1a1a;
      border: 1px solid var(--border);
      border-radius: 3px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 600;
      font-size: 12px;
      letter-spacing: 0.06em;
      text-transform: uppercase;
      color: var(--muted);
      cursor: pointer;
      transition: all 0.15s;
    }

    .query-tab:hover { color: var(--text); border-color: #444; }
    .query-tab.active { background: var(--red); color: #fff; border-color: var(--red); }

    .sql-box {
      background: #0d1117;
      border: 1px solid var(--border);
      border-radius: 3px;
      padding: 1rem;
      font-family: 'Courier New', monospace;
      font-size: 12px;
      color: #7ee787;
      margin-bottom: 1rem;
      white-space: pre-wrap;
      line-height: 1.6;
    }

    /* DIAGRAM PAGE */
    .diagram-img {
      width: 100%;
      border: 1px solid var(--border);
      border-radius: 4px;
    }

    .relation-table { width: 100%; }

    /* LOADING */
    .loading {
      text-align: center;
      padding: 3rem;
      color: var(--muted);
      font-family: 'Barlow Condensed', sans-serif;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      font-size: 13px;
    }

    /* HERO */
    .hero {
      background: linear-gradient(135deg, #0a0a0a 0%, #1a0005 100%);
      border: 1px solid var(--border);
      border-left: 4px solid var(--red);
      padding: 2rem;
      margin-bottom: 2rem;
      border-radius: 4px;
    }

    .hero-title {
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 800;
      font-size: 56px;
      text-transform: uppercase;
      line-height: 0.9;
      margin-bottom: 0.5rem;
    }

    .hero-sub {
      color: var(--muted);
      font-size: 13px;
      letter-spacing: 0.1em;
      text-transform: uppercase;
    }

    /* SCHEMA TABLE */
    .schema-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
      gap: 1rem;
    }

    .schema-card {
      background: var(--card);
      border: 1px solid var(--border);
      border-radius: 4px;
      overflow: hidden;
    }

    .schema-header {
      background: var(--red);
      padding: 8px 14px;
      font-family: 'Barlow Condensed', sans-serif;
      font-weight: 700;
      font-size: 13px;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .schema-row {
      display: flex;
      align-items: center;
      padding: 6px 14px;
      border-bottom: 1px solid #1a1a1a;
      gap: 8px;
      font-size: 12px;
    }

    .schema-row:last-child { border-bottom: none; }
    .schema-key { font-size: 9px; font-weight: 700; padding: 1px 5px; border-radius: 2px; min-width: 28px; text-align: center; }
    .key-pk { background: #3a1800; color: #f59e0b; }
    .key-fk { background: #0a2a00; color: var(--success); }
    .key-empty { min-width: 28px; }
    .schema-col { color: var(--text); flex: 1; }
    .schema-type { color: var(--muted); font-size: 10px; }
  </style>
</head>
<body>

<nav>
  <div class="nav-logo">F1 <span>Database</span></div>
  <div class="nav-links">
    <div class="nav-link active" onclick="showPage('home', this)">Home</div>
    <div class="nav-link" onclick="showPage('schema', this)">Schema</div>
    <div class="nav-link" onclick="showPage('queries', this)">Queries</div>
    <div class="nav-link" onclick="showPage('insert', this)">Insert</div>
    <div class="nav-link" onclick="showPage('modify', this)">Modify</div>
    <div class="nav-link" onclick="showPage('delete', this)">Delete</div>
    <div class="nav-link" onclick="showPage('diagram', this)">E/R Diagram</div>
    <a class="nav-link" href="charts.php" style="text-decoration:none">Charts</a>
  </div>
</nav>

<!-- HOME PAGE -->
<div id="page-home" class="page active">
  <div style="padding-top:1rem">
    <div class="hero">
      <div class="hero-title">Formula <span style="color:var(--red)">1</span><br>World Championship</div>
      <div class="hero-sub" style="margin-top:0.75rem">Database — COMP4018 Final Project · Sebastián Hernández</div>
    </div>

    <div class="stats-grid" id="home-stats">
      <div class="stat-card"><div class="stat-value" id="stat-races">—</div><div class="stat-label">Races</div></div>
      <div class="stat-card"><div class="stat-value" id="stat-drivers">—</div><div class="stat-label">Drivers</div></div>
      <div class="stat-card"><div class="stat-value" id="stat-constructors">—</div><div class="stat-label">Constructors</div></div>
      <div class="stat-card"><div class="stat-value" id="stat-circuits">—</div><div class="stat-label">Circuits</div></div>
      <div class="stat-card"><div class="stat-value" id="stat-seasons">—</div><div class="stat-label">Seasons</div></div>
      <div class="stat-card"><div class="stat-value" id="stat-results">—</div><div class="stat-label">Results</div></div>
    </div>

    <div class="card">
      <div class="card-title">About this database</div>
      <p style="font-size:14px;line-height:1.8;color:#ccc;margin-bottom:1rem">
        This database covers the entire Formula 1 World Championship from <strong>1950 to 2024</strong>, sourced from the Kaggle dataset by rohanrao. It contains 13 entity tables implementing a fully normalized relational schema in 3NF/BCNF.
      </p>
      <p style="font-size:14px;line-height:1.8;color:#ccc">
        The schema features an <strong>ISA inheritance hierarchy</strong> (PERSON → DRIVER, CONSTRUCTOR), many-to-many relationships resolved through intermediate tables (RESULT, QUALIFYING, LAP_TIME, PIT_STOP), and full referential integrity enforced through foreign key constraints.
      </p>
    </div>

    <div class="card">
      <div class="card-title">Recent Races</div>
      <div class="table-wrap">
        <table id="recent-races-table">
          <thead><tr><th>Round</th><th>Race</th><th>Circuit</th><th>Date</th><th>Season</th></tr></thead>
          <tbody><tr><td colspan="5" class="loading">Loading...</td></tr></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<!-- SCHEMA PAGE -->
<div id="page-schema" class="page">
  <h1>Database <span class="accent">Schema</span></h1>
  <div class="subtitle">13 tables · 3NF normalized · Full referential integrity</div>

  <div class="schema-grid">
    <div class="schema-card">
      <div class="schema-header">PERSON <span style="opacity:0.7;font-weight:400;font-size:10px">SUPERTYPE</span></div>
      <div class="schema-row"><span class="schema-key key-pk">PK</span><span class="schema-col">personID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">forename</span><span class="schema-type">VARCHAR</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">surname</span><span class="schema-type">VARCHAR</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">nationality</span><span class="schema-type">VARCHAR</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">dob</span><span class="schema-type">DATE</span></div>
    </div>
    <div class="schema-card">
      <div class="schema-header">DRIVER <span style="opacity:0.7;font-weight:400;font-size:10px">ISA SUBTYPE</span></div>
      <div class="schema-row"><span class="schema-key key-pk">PK/FK</span><span class="schema-col">driverID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">driverRef</span><span class="schema-type">VARCHAR</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">number</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">code</span><span class="schema-type">CHAR(3)</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">url</span><span class="schema-type">VARCHAR</span></div>
    </div>
    <div class="schema-card">
      <div class="schema-header">CONSTRUCTOR <span style="opacity:0.7;font-weight:400;font-size:10px">ISA SUBTYPE</span></div>
      <div class="schema-row"><span class="schema-key key-pk">PK/FK</span><span class="schema-col">constructorID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">constructorRef</span><span class="schema-type">VARCHAR</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">name</span><span class="schema-type">VARCHAR</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">nationality</span><span class="schema-type">VARCHAR</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">url</span><span class="schema-type">VARCHAR</span></div>
    </div>
    <div class="schema-card">
      <div class="schema-header">CIRCUIT</div>
      <div class="schema-row"><span class="schema-key key-pk">PK</span><span class="schema-col">circuitID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">circuitRef</span><span class="schema-type">VARCHAR</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">name</span><span class="schema-type">VARCHAR</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">location</span><span class="schema-type">VARCHAR</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">country</span><span class="schema-type">VARCHAR</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">lat / lng / alt</span><span class="schema-type">FLOAT/INT</span></div>
    </div>
    <div class="schema-card">
      <div class="schema-header">SEASON</div>
      <div class="schema-row"><span class="schema-key key-pk">PK</span><span class="schema-col">year</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">url</span><span class="schema-type">VARCHAR</span></div>
    </div>
    <div class="schema-card">
      <div class="schema-header">RACE</div>
      <div class="schema-row"><span class="schema-key key-pk">PK</span><span class="schema-col">raceID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">year</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">circuitID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">name / round</span><span class="schema-type">VARCHAR/INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">date / time</span><span class="schema-type">DATE/TIME</span></div>
    </div>
    <div class="schema-card">
      <div class="schema-header">RESULT</div>
      <div class="schema-row"><span class="schema-key key-pk">PK</span><span class="schema-col">resultID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">raceID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">driverID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">constructorID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">grid / positionOrder / points / laps / status</span><span class="schema-type">mixed</span></div>
    </div>
    <div class="schema-card">
      <div class="schema-header">QUALIFYING</div>
      <div class="schema-row"><span class="schema-key key-pk">PK</span><span class="schema-col">qualifyID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">raceID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">driverID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">constructorID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">q1 / q2 / q3</span><span class="schema-type">VARCHAR (nullable)</span></div>
    </div>
    <div class="schema-card">
      <div class="schema-header">LAP_TIME</div>
      <div class="schema-row"><span class="schema-key key-pk">PK/FK</span><span class="schema-col">raceID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-pk">PK/FK</span><span class="schema-col">driverID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-pk">PK</span><span class="schema-col">lap</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">position / time / milliseconds</span><span class="schema-type">mixed</span></div>
    </div>
    <div class="schema-card">
      <div class="schema-header">PIT_STOP</div>
      <div class="schema-row"><span class="schema-key key-pk">PK/FK</span><span class="schema-col">raceID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-pk">PK/FK</span><span class="schema-col">driverID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-pk">PK</span><span class="schema-col">stop</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">lap / time / milliseconds</span><span class="schema-type">mixed</span></div>
    </div>
    <div class="schema-card">
      <div class="schema-header">DRIVER_STANDING</div>
      <div class="schema-row"><span class="schema-key key-pk">PK</span><span class="schema-col">driverStandingID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">raceID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">driverID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">points / position / wins</span><span class="schema-type">mixed</span></div>
    </div>
    <div class="schema-card">
      <div class="schema-header">CONSTRUCTOR_STANDING</div>
      <div class="schema-row"><span class="schema-key key-pk">PK</span><span class="schema-col">constructorStandingID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">raceID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">constructorID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">points / position / wins</span><span class="schema-type">mixed</span></div>
    </div>
    <div class="schema-card">
      <div class="schema-header">SPRINT_RESULT</div>
      <div class="schema-row"><span class="schema-key key-pk">PK</span><span class="schema-col">sprintResultId</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">raceID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">driverID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="schema-key key-fk">FK</span><span class="schema-col">constructorID</span><span class="schema-type">INT</span></div>
      <div class="schema-row"><span class="key-empty"></span><span class="schema-col">grid / positionOrder / points / laps / status</span><span class="schema-type">mixed</span></div>
    </div>
  </div>

  <div class="card" style="margin-top:1.5rem">
    <div class="card-title">Functional Dependencies & Normalization</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Table</th><th>Primary Key</th><th>Functional Dependencies</th><th>Normal Form</th></tr></thead>
        <tbody>
          <tr><td>CIRCUIT</td><td>circuitID</td><td>circuitID → circuitRef, name, location, country, lat, lng, alt</td><td><span class="badge badge-green">BCNF</span></td></tr>
          <tr><td>SEASON</td><td>year</td><td>year → url</td><td><span class="badge badge-green">BCNF</span></td></tr>
          <tr><td>RACE</td><td>raceID</td><td>raceID → year, circuitID, round, name, date, time</td><td><span class="badge badge-green">3NF</span></td></tr>
          <tr><td>PERSON</td><td>personID</td><td>personID → forename, surname, nationality, dob</td><td><span class="badge badge-green">BCNF</span></td></tr>
          <tr><td>DRIVER</td><td>driverID</td><td>driverID → driverRef, number, code, url</td><td><span class="badge badge-green">BCNF</span></td></tr>
          <tr><td>CONSTRUCTOR</td><td>constructorID</td><td>constructorID → constructorRef, name, nationality, url</td><td><span class="badge badge-green">BCNF</span></td></tr>
          <tr><td>RESULT</td><td>resultID</td><td>resultID → raceID, driverID, constructorID, grid, positionOrder, points, laps, status</td><td><span class="badge badge-green">3NF</span></td></tr>
          <tr><td>QUALIFYING</td><td>qualifyID</td><td>qualifyID → raceID, driverID, constructorID, position, q1, q2, q3</td><td><span class="badge badge-green">3NF</span></td></tr>
          <tr><td>LAP_TIME</td><td>(raceID, driverID, lap)</td><td>raceID+driverID+lap → position, time, milliseconds</td><td><span class="badge badge-green">BCNF</span></td></tr>
          <tr><td>PIT_STOP</td><td>(raceID, driverID, stop)</td><td>raceID+driverID+stop → lap, time, milliseconds</td><td><span class="badge badge-green">BCNF</span></td></tr>
          <tr><td>DRIVER_STANDING</td><td>driverStandingID</td><td>driverStandingID → raceID, driverID, points, position, wins</td><td><span class="badge badge-green">3NF</span></td></tr>
          <tr><td>CONSTRUCTOR_STANDING</td><td>constructorStandingID</td><td>constructorStandingID → raceID, constructorID, points, position, wins</td><td><span class="badge badge-green">3NF</span></td></tr>
          <tr><td>SPRINT_RESULT</td><td>sprintResultId</td><td>sprintResultId → raceID, driverID, constructorID, grid, positionOrder, points</td><td><span class="badge badge-green">3NF</span></td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- QUERIES PAGE -->
<div id="page-queries" class="page">
  <h1>SQL <span class="accent">Queries</span></h1>
  <div class="subtitle">5 analytical queries · JOINs · Aggregations · Subqueries</div>

  <div class="query-tabs">
    <div class="query-tab active" onclick="runQuery(1, this)">Q1 · Championship Winners</div>
    <div class="query-tab" onclick="runQuery(2, this)">Q2 · Top Constructors</div>
    <div class="query-tab" onclick="runQuery(3, this)">Q3 · Fastest Pit Stops</div>
    <div class="query-tab" onclick="runQuery(4, this)">Q4 · Circuit Stats</div>
    <div class="query-tab" onclick="runQuery(5, this)">Q5 · Driver Career</div>
  </div>

  <div class="card">
    <div class="card-title" id="query-title">Q1 — Season Championship Winners (JOIN 3+ tables)</div>
    <div class="sql-box" id="query-sql">Loading...</div>
    <div class="table-wrap">
      <table id="query-table">
        <thead id="query-thead"></thead>
        <tbody id="query-tbody"><tr><td class="loading">Loading...</td></tr></tbody>
      </table>
    </div>
  </div>
</div>

<!-- INSERT PAGE -->
<div id="page-insert" class="page">
  <h1>Insert <span class="accent">Data</span></h1>
  <div class="subtitle">Add new records to the database</div>

  <div class="card">
    <div class="card-title">Insert New Driver</div>
    <div id="alert-insert" class="alert"></div>
    <div class="form-grid">
      <div class="form-group">
        <label>Driver ID</label>
        <input type="number" id="ins-driverID" placeholder="e.g. 900">
      </div>
      <div class="form-group">
        <label>First Name</label>
        <input type="text" id="ins-forename" placeholder="e.g. Max">
      </div>
      <div class="form-group">
        <label>Last Name</label>
        <input type="text" id="ins-surname" placeholder="e.g. Verstappen">
      </div>
      <div class="form-group">
        <label>Nationality</label>
        <input type="text" id="ins-nationality" placeholder="e.g. Dutch">
      </div>
      <div class="form-group">
        <label>Date of Birth</label>
        <input type="date" id="ins-dob">
      </div>
      <div class="form-group">
        <label>Driver Reference</label>
        <input type="text" id="ins-driverRef" placeholder="e.g. verstappen">
      </div>
      <div class="form-group">
        <label>Number</label>
        <input type="number" id="ins-number" placeholder="e.g. 1">
      </div>
      <div class="form-group">
        <label>Code (3 letters)</label>
        <input type="text" id="ins-code" maxlength="3" placeholder="e.g. VER">
      </div>
    </div>
    <div class="btn-row">
      <button class="btn btn-red" onclick="insertDriver()">Insert Driver</button>
      <button class="btn btn-outline" onclick="clearInsertForm()">Clear</button>
    </div>
  </div>

  <div class="card">
    <div class="card-title">Insert New Constructor</div>
    <div id="alert-constructor" class="alert"></div>
    <div class="form-grid">
      <div class="form-group">
        <label>Constructor ID</label>
        <input type="number" id="ins-constructorID" placeholder="e.g. 250">
      </div>
      <div class="form-group">
        <label>Constructor Ref</label>
        <input type="text" id="ins-constructorRef" placeholder="e.g. red_bull">
      </div>
      <div class="form-group">
        <label>Name</label>
        <input type="text" id="ins-constructor-name" placeholder="e.g. Red Bull">
      </div>
      <div class="form-group">
        <label>Nationality</label>
        <input type="text" id="ins-constructor-nationality" placeholder="e.g. Austrian">
      </div>
    </div>
    <div class="btn-row">
      <button class="btn btn-red" onclick="insertConstructor()">Insert Constructor</button>
    </div>
  </div>
</div>

<!-- MODIFY PAGE -->
<div id="page-modify" class="page">
  <h1>Modify <span class="accent">Data</span></h1>
  <div class="subtitle">Update existing records</div>

  <div class="card">
    <div class="card-title">Update Driver Information</div>
    <div id="alert-modify" class="alert"></div>
    <div class="form-grid">
      <div class="form-group">
        <label>Driver ID to update</label>
        <input type="number" id="mod-driverID" placeholder="e.g. 1">
        <button class="btn btn-outline" style="margin-top:6px" onclick="loadDriver()">Load Driver</button>
      </div>
      <div class="form-group">
        <label>New Nationality</label>
        <input type="text" id="mod-nationality" placeholder="e.g. British">
      </div>
      <div class="form-group">
        <label>New Number</label>
        <input type="number" id="mod-number" placeholder="e.g. 44">
      </div>
      <div class="form-group">
        <label>New Code</label>
        <input type="text" id="mod-code" maxlength="3" placeholder="e.g. HAM">
      </div>
    </div>
    <div id="driver-preview" style="display:none" class="card" style="margin-top:1rem">
      <div class="card-title">Current Driver Data</div>
      <div id="driver-preview-content"></div>
    </div>
    <div class="btn-row">
      <button class="btn btn-red" onclick="updateDriver()">Update Driver</button>
    </div>
  </div>
</div>

<!-- DELETE PAGE -->
<div id="page-delete" class="page">
  <h1>Delete <span class="accent">Data</span></h1>
  <div class="subtitle">Remove records from the database</div>

  <div class="card" style="border-color:#7f1d1d">
    <div class="card-title" style="color:#f87171">⚠ Delete Driver Record</div>
    <div id="alert-delete" class="alert"></div>
    <p style="font-size:13px;color:var(--muted);margin-bottom:1rem">Warning: Deleting a driver will fail if they have associated results, standings, or lap times due to referential integrity constraints.</p>
    <div class="form-grid">
      <div class="form-group">
        <label>Driver ID to delete</label>
        <input type="number" id="del-driverID" placeholder="e.g. 900">
      </div>
    </div>
    <div class="btn-row">
      <button class="btn btn-danger" onclick="deleteDriver()">Delete Driver</button>
    </div>
  </div>
</div>

<!-- DIAGRAM PAGE -->
<div id="page-diagram" class="page">
  <h1>E/R <span class="accent">Diagram</span></h1>
  <div class="subtitle">Entity-Relationship diagram · Crow's foot notation · ISA inheritance</div>
  <div class="card">
    <div class="card-title">Entity-Relationship Diagram</div>
    <p style="font-size:13px;color:var(--muted);margin-bottom:1rem">
      The diagram below shows all 13 entity tables, their attributes with PK/FK labels, and all 19 relationships using crow's foot notation. The ISA inheritance hierarchy (PERSON → DRIVER, CONSTRUCTOR) is shown with a diamond symbol representing partial, overlapping inheritance.
    </p>
    <p style="font-size:13px;color:var(--muted)">
      To view the full diagram, open the <strong>draw.io file</strong> included in the project submission, or view the <strong>f1_erd.html</strong> file in this project folder.
    </p>
  </div>
  <div class="card">
    <div class="card-title">Relational Model</div>
    <div class="table-wrap">
      <table>
        <thead><tr><th>Table</th><th>Primary Key</th><th>Foreign Keys</th><th>Description</th></tr></thead>
        <tbody>
          <tr><td>SEASON</td><td>year</td><td>—</td><td>F1 championship seasons 1950–2024</td></tr>
          <tr><td>CIRCUIT</td><td>circuitID</td><td>—</td><td>Race tracks with location coordinates</td></tr>
          <tr><td>RACE</td><td>raceID</td><td>year → SEASON, circuitID → CIRCUIT</td><td>Individual race events</td></tr>
          <tr><td>PERSON</td><td>personID</td><td>—</td><td>Supertype: shared personal attributes</td></tr>
          <tr><td>DRIVER</td><td>driverID</td><td>driverID → PERSON (ISA)</td><td>Subtype: driver-specific attributes</td></tr>
          <tr><td>CONSTRUCTOR</td><td>constructorID</td><td>—</td><td>Subtype: team/constructor entities</td></tr>
          <tr><td>RESULT</td><td>resultID</td><td>raceID, driverID, constructorID</td><td>Race finish positions and points</td></tr>
          <tr><td>QUALIFYING</td><td>qualifyID</td><td>raceID, driverID, constructorID</td><td>Qualifying session lap times</td></tr>
          <tr><td>LAP_TIME</td><td>(raceID, driverID, lap)</td><td>raceID, driverID</td><td>Individual lap times per driver per race</td></tr>
          <tr><td>PIT_STOP</td><td>(raceID, driverID, stop)</td><td>raceID, driverID</td><td>Pit stop records with duration</td></tr>
          <tr><td>DRIVER_STANDING</td><td>driverStandingID</td><td>raceID, driverID</td><td>Championship standings after each race</td></tr>
          <tr><td>CONSTRUCTOR_STANDING</td><td>constructorStandingID</td><td>raceID, constructorID</td><td>Constructor championship standings</td></tr>
          <tr><td>SPRINT_RESULT</td><td>sprintResultId</td><td>raceID, driverID, constructorID</td><td>Sprint race results (post-2021)</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script>
// ─── NAVIGATION ───────────────────────────────
function showPage(id, el) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
  document.getElementById('page-' + id).classList.add('active');
  el.classList.add('active');
  if (id === 'home') loadHome();
  if (id === 'queries') runQuery(1, document.querySelector('.query-tab'));
}

// ─── API HELPER ───────────────────────────────
async function api(action, params = {}) {
  const body = new URLSearchParams({ action, ...params });
  const res = await fetch('api.php', { method: 'POST', body });
  return res.json();
}

// ─── HOME ─────────────────────────────────────
async function loadHome() {
  const data = await api('home_stats');
  if (data.stats) {
    document.getElementById('stat-races').textContent = Number(data.stats.races).toLocaleString();
    document.getElementById('stat-drivers').textContent = Number(data.stats.drivers).toLocaleString();
    document.getElementById('stat-constructors').textContent = Number(data.stats.constructors).toLocaleString();
    document.getElementById('stat-circuits').textContent = Number(data.stats.circuits).toLocaleString();
    document.getElementById('stat-seasons').textContent = Number(data.stats.seasons).toLocaleString();
    document.getElementById('stat-results').textContent = Number(data.stats.results).toLocaleString();
  }
  const races = await api('recent_races');
  const tbody = document.querySelector('#recent-races-table tbody');
  if (races.data) {
    tbody.innerHTML = races.data.map(r => `
      <tr>
        <td>${r.round}</td>
        <td>${r.name}</td>
        <td>${r.circuit}</td>
        <td>${r.date}</td>
        <td>${r.year}</td>
      </tr>`).join('');
  }
}

// ─── QUERIES ──────────────────────────────────
const queries = {
  1: {
    title: 'Q1 — Season Championship Winners (JOIN 3+ tables + Subquery)',
    sql: `SELECT s.year,
       CONCAT(p.forename, ' ', p.surname) AS driver,
       p.nationality,
       c.name AS constructor,
       ds.points AS total_points,
       ds.wins
FROM DRIVER_STANDING ds
JOIN RACE r ON ds.raceID = r.raceID
JOIN SEASON s ON r.year = s.year
JOIN DRIVER d ON ds.driverID = d.driverID
JOIN PERSON p ON d.driverID = p.personID
JOIN RESULT res ON res.raceID = ds.raceID AND res.driverID = ds.driverID
JOIN CONSTRUCTOR c ON res.constructorID = c.constructorID
WHERE ds.position = 1
  AND r.raceID = (
    SELECT MAX(r2.raceID)
    FROM RACE r2
    WHERE r2.year = r.year
  )
ORDER BY s.year DESC
LIMIT 20`
  },
  2: {
    title: 'Q2 — Top Constructors by Total Points (GROUP BY + aggregation)',
    sql: `SELECT c.name AS constructor,
       c.nationality,
       COUNT(DISTINCT r.year) AS seasons,
       SUM(res.points) AS total_points,
       COUNT(CASE WHEN res.positionOrder = 1 THEN 1 END) AS wins,
       RANK() OVER (ORDER BY SUM(res.points) DESC) AS ranking
FROM CONSTRUCTOR c
JOIN RESULT res ON c.constructorID = res.constructorID
JOIN RACE r ON res.raceID = r.raceID
GROUP BY c.constructorID, c.name, c.nationality
HAVING total_points > 0
ORDER BY total_points DESC
LIMIT 15`
  },
  3: {
    title: 'Q3 — Fastest Pit Stops per Race (JOIN 3 tables)',
    sql: `SELECT r.name AS race,
       r.year,
       CONCAT(p.forename, ' ', p.surname) AS driver,
       ps.lap,
       ps.stop,
       ps.milliseconds,
       MIN(ps.milliseconds) OVER (PARTITION BY ps.raceID) AS fastest_in_race
FROM PIT_STOP ps
JOIN RACE r ON ps.raceID = r.raceID
JOIN DRIVER d ON ps.driverID = d.driverID
JOIN PERSON p ON d.driverID = p.personID
WHERE ps.milliseconds IS NOT NULL
  AND ps.milliseconds < 60000
ORDER BY ps.milliseconds ASC
LIMIT 20`
  },
  4: {
    title: 'Q4 — Circuit Race Statistics (GROUP BY + HAVING)',
    sql: `SELECT ci.name AS circuit,
       ci.country,
       ci.location,
       COUNT(r.raceID) AS times_hosted,
       MIN(r.year) AS first_race,
       MAX(r.year) AS last_race,
       AVG(res.points) AS avg_points_per_result
FROM CIRCUIT ci
JOIN RACE r ON ci.circuitID = r.circuitID
JOIN RESULT res ON r.raceID = res.raceID
GROUP BY ci.circuitID, ci.name, ci.country, ci.location
HAVING times_hosted >= 5
ORDER BY times_hosted DESC
LIMIT 15`
  },
  5: {
    title: 'Q5 — Driver Career Summary with Subquery (WITH clause)',
    sql: `WITH career AS (
  SELECT d.driverID,
         CONCAT(p.forename, ' ', p.surname) AS driver,
         p.nationality,
         COUNT(DISTINCT res.raceID) AS races,
         SUM(res.points) AS total_points,
         COUNT(CASE WHEN res.positionOrder = 1 THEN 1 END) AS wins,
         COUNT(CASE WHEN res.positionOrder <= 3 THEN 1 END) AS podiums,
         MIN(r.year) AS first_year,
         MAX(r.year) AS last_year
  FROM DRIVER d
  JOIN PERSON p ON d.driverID = p.personID
  JOIN RESULT res ON d.driverID = res.driverID
  JOIN RACE r ON res.raceID = r.raceID
  GROUP BY d.driverID, p.forename, p.surname, p.nationality
)
SELECT driver, nationality, races, total_points, wins, podiums,
       first_year, last_year,
       (last_year - first_year + 1) AS career_years
FROM career
WHERE races >= 50
ORDER BY total_points DESC
LIMIT 20`
  }
};

async function runQuery(n, el) {
  document.querySelectorAll('.query-tab').forEach(t => t.classList.remove('active'));
  if (el) el.classList.add('active');
  const q = queries[n];
  document.getElementById('query-title').textContent = q.title;
  document.getElementById('query-sql').textContent = q.sql;
  document.getElementById('query-tbody').innerHTML = '<tr><td colspan="20" class="loading">Running query...</td></tr>';

  const data = await api('run_query', { query_id: n });
  if (data.error) {
    document.getElementById('query-tbody').innerHTML = `<tr><td colspan="20" style="color:var(--red);padding:1rem">${data.error}</td></tr>`;
    return;
  }
  if (!data.data || data.data.length === 0) {
    document.getElementById('query-tbody').innerHTML = '<tr><td colspan="20" class="loading">No results</td></tr>';
    return;
  }
  const cols = Object.keys(data.data[0]);
  document.getElementById('query-thead').innerHTML = '<tr>' + cols.map(c => `<th>${c}</th>`).join('') + '</tr>';
  document.getElementById('query-tbody').innerHTML = data.data.map(row =>
    '<tr>' + cols.map(c => `<td>${row[c] ?? '—'}</td>`).join('') + '</tr>'
  ).join('');
}

// ─── INSERT ───────────────────────────────────
function showAlert(id, msg, type) {
  const el = document.getElementById(id);
  el.textContent = msg;
  el.className = `alert alert-${type} show`;
  setTimeout(() => el.classList.remove('show'), 5000);
}

async function insertDriver() {
  const data = {
    driverID: document.getElementById('ins-driverID').value,
    forename: document.getElementById('ins-forename').value,
    surname: document.getElementById('ins-surname').value,
    nationality: document.getElementById('ins-nationality').value,
    dob: document.getElementById('ins-dob').value,
    driverRef: document.getElementById('ins-driverRef').value,
    number: document.getElementById('ins-number').value,
    code: document.getElementById('ins-code').value,
  };
  if (!data.driverID || !data.forename || !data.surname) {
    showAlert('alert-insert', '⚠ Driver ID, first name, and last name are required.', 'warning');
    return;
  }
  const res = await api('insert_driver', data);
  if (res.success) {
    showAlert('alert-insert', '✓ Driver inserted successfully!', 'success');
    clearInsertForm();
  } else {
    showAlert('alert-insert', '✗ Error: ' + res.error, 'error');
  }
}

async function insertConstructor() {
  const data = {
    constructorID: document.getElementById('ins-constructorID').value,
    constructorRef: document.getElementById('ins-constructorRef').value,
    name: document.getElementById('ins-constructor-name').value,
    nationality: document.getElementById('ins-constructor-nationality').value,
  };
  if (!data.constructorID || !data.name) {
    showAlert('alert-constructor', '⚠ Constructor ID and name are required.', 'warning');
    return;
  }
  const res = await api('insert_constructor', data);
  if (res.success) {
    showAlert('alert-constructor', '✓ Constructor inserted successfully!', 'success');
  } else {
    showAlert('alert-constructor', '✗ Integrity Error: ' + res.error, 'error');
  }
}

function clearInsertForm() {
  ['ins-driverID','ins-forename','ins-surname','ins-nationality','ins-dob','ins-driverRef','ins-number','ins-code']
    .forEach(id => document.getElementById(id).value = '');
}

// ─── MODIFY ───────────────────────────────────
async function loadDriver() {
  const id = document.getElementById('mod-driverID').value;
  if (!id) return;
  const res = await api('get_driver', { driverID: id });
  if (res.data) {
    const d = res.data;
    document.getElementById('driver-preview').style.display = 'block';
    document.getElementById('driver-preview-content').innerHTML = `
      <p style="font-size:13px;color:#ccc"><strong>${d.forename} ${d.surname}</strong> — ${d.nationality} — #${d.number || '—'} — Code: ${d.code || '—'}</p>`;
    document.getElementById('mod-nationality').value = d.nationality || '';
    document.getElementById('mod-number').value = d.number || '';
    document.getElementById('mod-code').value = d.code || '';
  } else {
    showAlert('alert-modify', '✗ Driver not found.', 'error');
  }
}

async function updateDriver() {
  const data = {
    driverID: document.getElementById('mod-driverID').value,
    nationality: document.getElementById('mod-nationality').value,
    number: document.getElementById('mod-number').value,
    code: document.getElementById('mod-code').value,
  };
  if (!data.driverID) {
    showAlert('alert-modify', '⚠ Please enter a Driver ID.', 'warning');
    return;
  }
  const res = await api('update_driver', data);
  if (res.success) {
    showAlert('alert-modify', '✓ Driver updated successfully!', 'success');
  } else {
    showAlert('alert-modify', '✗ Error: ' + res.error, 'error');
  }
}

// ─── DELETE ───────────────────────────────────
async function deleteDriver() {
  const id = document.getElementById('del-driverID').value;
  if (!id) { showAlert('alert-delete', '⚠ Please enter a Driver ID.', 'warning'); return; }
  if (!confirm(`Delete driver ID ${id}? This cannot be undone.`)) return;
  const res = await api('delete_driver', { driverID: id });
  if (res.success) {
    showAlert('alert-delete', '✓ Driver deleted successfully.', 'success');
  } else {
    showAlert('alert-delete', '✗ Integrity Violation: ' + res.error + ' — This driver has associated records and cannot be deleted.', 'error');
  }
}

// ─── INIT ─────────────────────────────────────
loadHome();
</script>
</body>
</html>
