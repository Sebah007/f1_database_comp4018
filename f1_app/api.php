<?php
header('Content-Type: application/json');
require_once 'db.php';

$action = $_POST['action'] ?? '';

try {
    switch ($action) {

        // ─── HOME STATS ───────────────────────────────
        case 'home_stats':
            $stats = [];
            foreach (['RACE'=>'races','DRIVER'=>'drivers','CONSTRUCTOR'=>'constructors',
                      'CIRCUIT'=>'circuits','SEASON'=>'seasons','RESULT'=>'results'] as $table => $key) {
                $stmt = $pdo->query("SELECT COUNT(*) as c FROM `$table`");
                $stats[$key] = $stmt->fetch()['c'];
            }
            echo json_encode(['stats' => $stats]);
            break;

        // ─── RECENT RACES ─────────────────────────────
        case 'recent_races':
            $stmt = $pdo->query("
                SELECT r.round, r.name, r.date, r.year, ci.name AS circuit
                FROM RACE r
                JOIN CIRCUIT ci ON r.circuitID = ci.circuitID
                ORDER BY r.year DESC, r.round DESC
                LIMIT 10
            ");
            echo json_encode(['data' => $stmt->fetchAll()]);
            break;

        // ─── RUN QUERIES ──────────────────────────────
        case 'run_query':
            $qid = intval($_POST['query_id'] ?? 0);
            $queries = [
                1 => "SELECT s.year,
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
                    LIMIT 20",

                2 => "SELECT c.name AS constructor,
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
                    LIMIT 15",

                3 => "SELECT r.name AS race,
                       r.year,
                       CONCAT(p.forename, ' ', p.surname) AS driver,
                       ps.lap,
                       ps.stop,
                       ps.milliseconds
                    FROM PIT_STOP ps
                    JOIN RACE r ON ps.raceID = r.raceID
                    JOIN DRIVER d ON ps.driverID = d.driverID
                    JOIN PERSON p ON d.driverID = p.personID
                    WHERE ps.milliseconds IS NOT NULL
                      AND ps.milliseconds < 60000
                    ORDER BY ps.milliseconds ASC
                    LIMIT 20",

                4 => "SELECT ci.name AS circuit,
                       ci.country,
                       ci.location,
                       COUNT(r.raceID) AS times_hosted,
                       MIN(r.year) AS first_race,
                       MAX(r.year) AS last_race
                    FROM CIRCUIT ci
                    JOIN RACE r ON ci.circuitID = r.circuitID
                    JOIN RESULT res ON r.raceID = res.raceID
                    GROUP BY ci.circuitID, ci.name, ci.country, ci.location
                    HAVING times_hosted >= 5
                    ORDER BY times_hosted DESC
                    LIMIT 15",

                5 => "WITH career AS (
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
                    LIMIT 20"
            ];

            if (!isset($queries[$qid])) {
                echo json_encode(['error' => 'Invalid query']);
                break;
            }
            $stmt = $pdo->query($queries[$qid]);
            echo json_encode(['data' => $stmt->fetchAll()]);
            break;

        // ─── INSERT DRIVER ────────────────────────────
        case 'insert_driver':
            $driverID    = intval($_POST['driverID']);
            $forename    = trim($_POST['forename']);
            $surname     = trim($_POST['surname']);
            $nationality = trim($_POST['nationality']);
            $dob         = $_POST['dob'] ?: null;
            $driverRef   = trim($_POST['driverRef']);
            $number      = $_POST['number'] ? intval($_POST['number']) : null;
            $code        = strtoupper(trim($_POST['code'])) ?: null;

            // Insert into PERSON first (supertype)
            $stmt = $pdo->prepare("INSERT INTO PERSON (personID, forename, surname, nationality, dob) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$driverID, $forename, $surname, $nationality, $dob]);

            // Insert into DRIVER (subtype)
            $stmt = $pdo->prepare("INSERT INTO DRIVER (driverID, driverRef, number, code, url) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$driverID, $driverRef, $number, $code, '']);

            echo json_encode(['success' => true]);
            break;

        // ─── INSERT CONSTRUCTOR ───────────────────────
        case 'insert_constructor':
            $constructorID  = intval($_POST['constructorID']);
            $constructorRef = trim($_POST['constructorRef']);
            $name           = trim($_POST['name']);
            $nationality    = trim($_POST['nationality']);

            $stmt = $pdo->prepare("INSERT INTO CONSTRUCTOR (constructorID, constructorRef, name, nationality, url) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$constructorID, $constructorRef, $name, $nationality, '']);
            echo json_encode(['success' => true]);
            break;

        // ─── GET DRIVER ───────────────────────────────
        case 'get_driver':
            $driverID = intval($_POST['driverID']);
            $stmt = $pdo->prepare("
                SELECT p.forename, p.surname, p.nationality, d.number, d.code
                FROM DRIVER d
                JOIN PERSON p ON d.driverID = p.personID
                WHERE d.driverID = ?
            ");
            $stmt->execute([$driverID]);
            $row = $stmt->fetch();
            echo json_encode($row ? ['data' => $row] : ['error' => 'Not found']);
            break;

        // ─── UPDATE DRIVER ────────────────────────────
        case 'update_driver':
            $driverID    = intval($_POST['driverID']);
            $nationality = trim($_POST['nationality']);
            $number      = $_POST['number'] ? intval($_POST['number']) : null;
            $code        = strtoupper(trim($_POST['code'])) ?: null;

            // Update PERSON (supertype)
            $stmt = $pdo->prepare("UPDATE PERSON SET nationality = ? WHERE personID = ?");
            $stmt->execute([$nationality, $driverID]);

            // Update DRIVER (subtype)
            $stmt = $pdo->prepare("UPDATE DRIVER SET number = ?, code = ? WHERE driverID = ?");
            $stmt->execute([$number, $code, $driverID]);

            echo json_encode(['success' => true, 'rows' => $stmt->rowCount()]);
            break;

        // ─── DELETE DRIVER ────────────────────────────
        case 'delete_driver':
            $driverID = intval($_POST['driverID']);
            // FK constraints will throw exception if driver has results
            $stmt = $pdo->prepare("DELETE FROM DRIVER WHERE driverID = ?");
            $stmt->execute([$driverID]);
            $stmt2 = $pdo->prepare("DELETE FROM PERSON WHERE personID = ?");
            $stmt2->execute([$driverID]);
            echo json_encode(['success' => true]);
            break;

        default:
            echo json_encode(['error' => 'Unknown action']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
