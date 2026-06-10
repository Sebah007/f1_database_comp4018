import pandas as pd
import os
import math


CSV_DIR = "f1_data"
OUTPUT_FILE = "f1_database.sql"

# ─────────────────────────────────────────────
# HELPER FUNCTIONS
# ─────────────────────────────────────────────

def clean_val(val):
    """Convert a value to a MySQL-safe string."""
    if val is None:
        return "NULL"
    if isinstance(val, float) and math.isnan(val):
        return "NULL"
    if str(val).strip() in ("\\N", "NA", "N/A", "nan", ""):
        return "NULL"
    # Escape single quotes
    val = str(val).replace("'", "\\'")
    return f"'{val}'"

def generate_inserts(df, table_name):
    """Generate INSERT statements for a dataframe."""
    lines = []
    cols = ", ".join(df.columns.tolist())
    for _, row in df.iterrows():
        vals = ", ".join(clean_val(v) for v in row)
        lines.append(f"  ({vals})")
    
    # Batch in groups of 500 for performance
    inserts = []
    batch_size = 500
    rows_list = [f"  ({', '.join(clean_val(v) for v in row)})" for _, row in df.iterrows()]
    
    for i in range(0, len(rows_list), batch_size):
        batch = rows_list[i:i+batch_size]
        inserts.append(
            f"INSERT INTO `{table_name}` ({cols}) VALUES\n" +
            ",\n".join(batch) + ";"
        )
    return "\n".join(inserts)

# ─────────────────────────────────────────────
# LOAD CSVs
# ─────────────────────────────────────────────


print("Loading CSVs...")

circuits       = pd.read_csv(f"{CSV_DIR}/circuits.csv")
seasons        = pd.read_csv(f"{CSV_DIR}/seasons.csv")
races          = pd.read_csv(f"{CSV_DIR}/races.csv")
drivers        = pd.read_csv(f"{CSV_DIR}/drivers.csv")
constructors   = pd.read_csv(f"{CSV_DIR}/constructors.csv")
results        = pd.read_csv(f"{CSV_DIR}/results.csv")
qualifying     = pd.read_csv(f"{CSV_DIR}/qualifying.csv")
lap_times      = pd.read_csv(f"{CSV_DIR}/lap_times.csv")
pit_stops      = pd.read_csv(f"{CSV_DIR}/pit_stops.csv")
driver_std     = pd.read_csv(f"{CSV_DIR}/driver_standings.csv")
constructor_std= pd.read_csv(f"{CSV_DIR}/constructor_standings.csv")

print("CSVs loaded successfully!")


# ─────────────────────────────────────────────
# PREPROCESSING — clean known issues
# ─────────────────────────────────────────────


# Replace \N (Kaggle null marker) with NaN across all dataframes
all_dfs = [circuits, seasons, races, drivers, constructors,
           results, qualifying, lap_times, pit_stops,
           driver_std, constructor_std]

for df in all_dfs:
    df.replace("\\N", pd.NA, inplace=True)

# PERSON table — derived from drivers (forename, surname, nationality, dob)
person = drivers[["driverId", "forename", "surname", "nationality", "dob"]].copy()
person.rename(columns={"driverId": "personID"}, inplace=True)

# DRIVER table — driver-specific columns only
driver = drivers[["driverId", "driverRef", "number", "code", "url"]].copy()
driver.rename(columns={"driverId": "driverID"}, inplace=True)

# CONSTRUCTOR table — keep as is, rename for consistency
constructor = constructors.copy()
constructor.rename(columns={
    "constructorId": "constructorID",
    "constructorRef": "constructorRef"
}, inplace=True)

# CIRCUIT — rename for consistency
circuit = circuits.copy()
circuit.rename(columns={"circuitId": "circuitID"}, inplace=True)

# SEASON — rename
season = seasons.copy()
season.rename(columns={}, inplace=True)

# RACE — rename
race = races.copy()
race.rename(columns={
    "raceId": "raceID",
    "circuitId": "circuitID"
}, inplace=True)
# Keep only needed columns
race = race[["raceID", "year", "round", "circuitID", "name", "date", "time", "url"]]

# RESULT
result = results.copy()
result.rename(columns={
    "resultId": "resultID",
    "raceId": "raceID",
    "driverId": "driverID",
    "constructorId": "constructorID",
    "positionOrder": "positionOrder"
}, inplace=True)
result = result[["resultID", "raceID", "driverID", "constructorID",
                  "grid", "positionOrder", "points", "laps", "statusId"]]
result.rename(columns={"statusId": "status"}, inplace=True)

# QUALIFYING
qual = qualifying.copy()
qual.rename(columns={
    "qualifyId": "qualifyID",
    "raceId": "raceID",
    "driverId": "driverID",
    "constructorId": "constructorID"
}, inplace=True)
qual = qual[["qualifyID", "raceID", "driverID", "constructorID",
              "number", "position", "q1", "q2", "q3"]]

# LAP_TIME
lap = lap_times.copy()
lap.rename(columns={
    "raceId": "raceID",
    "driverId": "driverID"
}, inplace=True)
lap = lap[["raceID", "driverID", "lap", "position", "time", "milliseconds"]]

# PIT_STOP
pit = pit_stops.copy()
pit.rename(columns={
    "raceId": "raceID",
    "driverId": "driverID"
}, inplace=True)
pit = pit[["raceID", "driverID", "stop", "lap", "time", "milliseconds"]]

# DRIVER_STANDING
d_stand = driver_std.copy()
d_stand.rename(columns={
    "driverStandingsId": "driverStandingID",
    "raceId": "raceID",
    "driverId": "driverID"
}, inplace=True)
d_stand = d_stand[["driverStandingID", "raceID", "driverID",
                    "points", "position", "wins"]]

# CONSTRUCTOR_STANDING
c_stand = constructor_std.copy()
c_stand.rename(columns={
    "constructorStandingsId": "constructorStandingID",
    "raceId": "raceID",
    "constructorId": "constructorID"
}, inplace=True)
c_stand = c_stand[["constructorStandingID", "raceID", "constructorID",
                    "points", "position", "wins"]]

print("Preprocessing complete!")



# ─────────────────────────────────────────────
# GENERATE SQL FILE
# ─────────────────────────────────────────────


print(f"Writing SQL to {OUTPUT_FILE}...")

sql = """-- =============================================
-- F1 World Championship Database
-- Generated by preprocess_f1.py
-- COMP4018 Final Project
-- =============================================

DROP DATABASE IF EXISTS f1_db;
CREATE DATABASE f1_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE f1_db;

SET FOREIGN_KEY_CHECKS = 0;

-- ─────────────────────────────────────────────
-- TABLE DEFINITIONS
-- ─────────────────────────────────────────────

CREATE TABLE `SEASON` (
  `year` INT NOT NULL,
  `url` VARCHAR(255),
  PRIMARY KEY (`year`)
) ENGINE=InnoDB;

CREATE TABLE `CIRCUIT` (
  `circuitID` INT NOT NULL,
  `circuitRef` VARCHAR(100),
  `name` VARCHAR(255),
  `location` VARCHAR(100),
  `country` VARCHAR(100),
  `lat` FLOAT,
  `lng` FLOAT,
  `alt` INT,
  `url` VARCHAR(255),
  PRIMARY KEY (`circuitID`)
) ENGINE=InnoDB;

CREATE TABLE `RACE` (
  `raceID` INT NOT NULL,
  `year` INT NOT NULL,
  `round` INT,
  `circuitID` INT NOT NULL,
  `name` VARCHAR(255),
  `date` DATE,
  `time` TIME,
  `url` VARCHAR(255),
  PRIMARY KEY (`raceID`),
  CONSTRAINT `fk_race_season` FOREIGN KEY (`year`) REFERENCES `SEASON`(`year`),
  CONSTRAINT `fk_race_circuit` FOREIGN KEY (`circuitID`) REFERENCES `CIRCUIT`(`circuitID`)
) ENGINE=InnoDB;

CREATE TABLE `PERSON` (
  `personID` INT NOT NULL,
  `forename` VARCHAR(100),
  `surname` VARCHAR(100),
  `nationality` VARCHAR(100),
  `dob` DATE,
  PRIMARY KEY (`personID`)
) ENGINE=InnoDB;

CREATE TABLE `DRIVER` (
  `driverID` INT NOT NULL,
  `driverRef` VARCHAR(100),
  `number` INT,
  `code` CHAR(3),
  `url` VARCHAR(255),
  PRIMARY KEY (`driverID`),
  CONSTRAINT `fk_driver_person` FOREIGN KEY (`driverID`) REFERENCES `PERSON`(`personID`)
) ENGINE=InnoDB;

CREATE TABLE `CONSTRUCTOR` (
  `constructorID` INT NOT NULL,
  `constructorRef` VARCHAR(100),
  `name` VARCHAR(255),
  `nationality` VARCHAR(100),
  `url` VARCHAR(255),
  PRIMARY KEY (`constructorID`)
) ENGINE=InnoDB;

CREATE TABLE `RESULT` (
  `resultID` INT NOT NULL,
  `raceID` INT NOT NULL,
  `driverID` INT NOT NULL,
  `constructorID` INT NOT NULL,
  `grid` INT,
  `positionOrder` INT,
  `points` FLOAT,
  `laps` INT,
  `status` VARCHAR(100),
  PRIMARY KEY (`resultID`),
  CONSTRAINT `fk_result_race` FOREIGN KEY (`raceID`) REFERENCES `RACE`(`raceID`),
  CONSTRAINT `fk_result_driver` FOREIGN KEY (`driverID`) REFERENCES `DRIVER`(`driverID`),
  CONSTRAINT `fk_result_constructor` FOREIGN KEY (`constructorID`) REFERENCES `CONSTRUCTOR`(`constructorID`)
) ENGINE=InnoDB;

CREATE TABLE `QUALIFYING` (
  `qualifyID` INT NOT NULL,
  `raceID` INT NOT NULL,
  `driverID` INT NOT NULL,
  `constructorID` INT NOT NULL,
  `number` INT,
  `position` INT,
  `q1` VARCHAR(20),
  `q2` VARCHAR(20),
  `q3` VARCHAR(20),
  PRIMARY KEY (`qualifyID`),
  CONSTRAINT `fk_qual_race` FOREIGN KEY (`raceID`) REFERENCES `RACE`(`raceID`),
  CONSTRAINT `fk_qual_driver` FOREIGN KEY (`driverID`) REFERENCES `DRIVER`(`driverID`),
  CONSTRAINT `fk_qual_constructor` FOREIGN KEY (`constructorID`) REFERENCES `CONSTRUCTOR`(`constructorID`)
) ENGINE=InnoDB;

CREATE TABLE `LAP_TIME` (
  `raceID` INT NOT NULL,
  `driverID` INT NOT NULL,
  `lap` INT NOT NULL,
  `position` INT,
  `time` VARCHAR(20),
  `milliseconds` INT,
  PRIMARY KEY (`raceID`, `driverID`, `lap`),
  CONSTRAINT `fk_lap_race` FOREIGN KEY (`raceID`) REFERENCES `RACE`(`raceID`),
  CONSTRAINT `fk_lap_driver` FOREIGN KEY (`driverID`) REFERENCES `DRIVER`(`driverID`)
) ENGINE=InnoDB;

CREATE TABLE `PIT_STOP` (
  `raceID` INT NOT NULL,
  `driverID` INT NOT NULL,
  `stop` INT NOT NULL,
  `lap` INT,
  `time` VARCHAR(20),
  `milliseconds` INT,
  PRIMARY KEY (`raceID`, `driverID`, `stop`),
  CONSTRAINT `fk_pit_race` FOREIGN KEY (`raceID`) REFERENCES `RACE`(`raceID`),
  CONSTRAINT `fk_pit_driver` FOREIGN KEY (`driverID`) REFERENCES `DRIVER`(`driverID`)
) ENGINE=InnoDB;

CREATE TABLE `DRIVER_STANDING` (
  `driverStandingID` INT NOT NULL,
  `raceID` INT NOT NULL,
  `driverID` INT NOT NULL,
  `points` FLOAT,
  `position` INT,
  `wins` INT,
  PRIMARY KEY (`driverStandingID`),
  CONSTRAINT `fk_dstand_race` FOREIGN KEY (`raceID`) REFERENCES `RACE`(`raceID`),
  CONSTRAINT `fk_dstand_driver` FOREIGN KEY (`driverID`) REFERENCES `DRIVER`(`driverID`)
) ENGINE=InnoDB;

CREATE TABLE `CONSTRUCTOR_STANDING` (
  `constructorStandingID` INT NOT NULL,
  `raceID` INT NOT NULL,
  `constructorID` INT NOT NULL,
  `points` FLOAT,
  `position` INT,
  `wins` INT,
  PRIMARY KEY (`constructorStandingID`),
  CONSTRAINT `fk_cstand_race` FOREIGN KEY (`raceID`) REFERENCES `RACE`(`raceID`),
  CONSTRAINT `fk_cstand_constructor` FOREIGN KEY (`constructorID`) REFERENCES `CONSTRUCTOR`(`constructorID`)
) ENGINE=InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- ─────────────────────────────────────────────
-- DATA INSERTS (order matters for FK constraints)
-- ─────────────────────────────────────────────

"""

# Inserts in FK-SAFE order
tables = [
    ("SEASON",               season),
    ("CIRCUIT",              circuit),
    ("RACE",                 race),
    ("PERSON",               person),
    ("DRIVER",               driver),
    ("CONSTRUCTOR",          constructor),
    ("RESULT",               result),
    ("QUALIFYING",           qual),
    ("LAP_TIME",             lap),
    ("PIT_STOP",             pit),
    ("DRIVER_STANDING",      d_stand),
    ("CONSTRUCTOR_STANDING", c_stand),
]

for table_name, df in tables:
    print(f"  Writing {table_name} ({len(df)} rows)...")
    sql += f"\n-- {table_name}\n"
    sql += generate_inserts(df, table_name)
    sql += "\n"

with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
    f.write(sql)
    
# STEP TO REMEMEBER POUR LA PRESENTATION

print(f"\n Done! SQL file saved to: {OUTPUT_FILE}")
print("Next step: open XAMPP, start MySQL, then run:")
print(f"  mysql -u root -p < {OUTPUT_FILE}")
