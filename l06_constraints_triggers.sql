-- =============================================
-- L06: Constraints & Triggers
-- COMP4018 Final Project — F1 Database
-- =============================================

USE f1_db;

-- Check constraints to validate data at the attribute/tuple level

-- Points must be >= 0 in RESULT
ALTER TABLE RESULT
  ADD CONSTRAINT chk_result_points
  CHECK (points >= 0);

-- positionOrder must be > 0 in RESULT
ALTER TABLE RESULT
  ADD CONSTRAINT chk_result_position
  CHECK (positionOrder > 0);

-- Grid position must be >= 0 (0 means pit lane start)
ALTER TABLE RESULT
  ADD CONSTRAINT chk_result_grid
  CHECK (grid >= 0);

-- Laps must be >= 0
ALTER TABLE RESULT
  ADD CONSTRAINT chk_result_laps
  CHECK (laps >= 0);

-- Points and wins must be >= 0 in DRIVER_STANDING
ALTER TABLE DRIVER_STANDING
  ADD CONSTRAINT chk_dstand_points
  CHECK (points >= 0);

ALTER TABLE DRIVER_STANDING
  ADD CONSTRAINT chk_dstand_wins
  CHECK (wins >= 0);

-- Points and wins must be >= 0 in CONSTRUCTOR_STANDING
ALTER TABLE CONSTRUCTOR_STANDING
  ADD CONSTRAINT chk_cstand_points
  CHECK (points >= 0);

ALTER TABLE CONSTRUCTOR_STANDING
  ADD CONSTRAINT chk_cstand_wins
  CHECK (wins >= 0);

-- Lap number must be > 0 in LAP_TIME
ALTER TABLE LAP_TIME
  ADD CONSTRAINT chk_lap_number
  CHECK (lap > 0);

-- Stop number must be > 0 in PIT_STOP
ALTER TABLE PIT_STOP
  ADD CONSTRAINT chk_stop_number
  CHECK (stop > 0);

-- Driver code must be exactly 3 characters if provided
ALTER TABLE DRIVER
  ADD CONSTRAINT chk_driver_code
  CHECK (code IS NULL OR CHAR_LENGTH(code) = 3);

-- Audit log table to track inserts into RESULT
CREATE TABLE IF NOT EXISTS RESULT_AUDIT_LOG (
  logID         INT AUTO_INCREMENT PRIMARY KEY,
  action_type   VARCHAR(10) NOT NULL,
  resultID      INT,
  raceID        INT,
  driverID      INT,
  constructorID INT,
  points        FLOAT,
  positionOrder INT,
  logged_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Before insert trigger: blocks invalid data before it enters RESULT
DROP TRIGGER IF EXISTS before_result_insert;

DELIMITER $$

CREATE TRIGGER before_result_insert
BEFORE INSERT ON RESULT
FOR EACH ROW
BEGIN
  IF NEW.points < 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'INTEGRITY VIOLATION: points cannot be negative in RESULT';
  END IF;

  IF NEW.positionOrder <= 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'INTEGRITY VIOLATION: positionOrder must be greater than 0';
  END IF;

  IF NEW.laps < 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'INTEGRITY VIOLATION: laps cannot be negative';
  END IF;
END$$

DELIMITER ;

-- After insert trigger: logs every successful insert into RESULT_AUDIT_LOG
DROP TRIGGER IF EXISTS after_result_insert;

DELIMITER $$

CREATE TRIGGER after_result_insert
AFTER INSERT ON RESULT
FOR EACH ROW
BEGIN
  INSERT INTO RESULT_AUDIT_LOG (
    action_type, resultID, raceID, driverID,
    constructorID, points, positionOrder
  ) VALUES (
    'INSERT', NEW.resultID, NEW.raceID, NEW.driverID,
    NEW.constructorID, NEW.points, NEW.positionOrder
  );
END$$

DELIMITER ;

-- Before update trigger: same validation applied to updates on RESULT
DROP TRIGGER IF EXISTS before_result_update;

DELIMITER $$

CREATE TRIGGER before_result_update
BEFORE UPDATE ON RESULT
FOR EACH ROW
BEGIN
  IF NEW.points < 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'INTEGRITY VIOLATION: points cannot be negative on UPDATE';
  END IF;

  IF NEW.positionOrder <= 0 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'INTEGRITY VIOLATION: positionOrder must be > 0 on UPDATE';
  END IF;
END$$

DELIMITER ;

-- Transaction example: inserting a driver requires both PERSON and DRIVER
-- to be inserted together. If one fails, neither is committed.
START TRANSACTION;

INSERT INTO PERSON (personID, forename, surname, nationality, dob)
VALUES (9999, 'Test', 'Driver', 'Puerto Rican', '2000-01-01');

INSERT INTO DRIVER (driverID, driverRef, number, code, url)
VALUES (9999, 'test_driver', 99, 'TST', '');

COMMIT;

-- Cleaning up the test driver
START TRANSACTION;

DELETE FROM DRIVER WHERE driverID = 9999;
DELETE FROM PERSON WHERE personID = 9999;

COMMIT;

-- Verify all triggers were created
SHOW TRIGGERS FROM f1_db;

-- Testing the before insert trigger
-- Try inserting a result with negative points -- this should fail
-- INSERT INTO RESULT (resultID, raceID, driverID, constructorID, grid, positionOrder, points, laps, status)
-- VALUES (99999, 1, 1, 1, 1, 1, -5, 10, '1');

-- To check what gets logged after a real insert:
-- SELECT * FROM RESULT_AUDIT_LOG ORDER BY logged_at DESC LIMIT 10;
