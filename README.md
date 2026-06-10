# F1 World Championship Database
## COMP4018 — Sistemas de Bases de Datos
### Sebastián Hernández

## Description
A relational database of the Formula 1 World Championship (1950–2024) built with MySQL, PHP, and Python.

## Database
- 13 entity tables
- 3NF/BCNF normalized
- ISA inheritance hierarchy (PERSON → DRIVER, CONSTRUCTOR)
- Full referential integrity with FK constraints
- CHECK constraints and triggers (L06)

## Files
- `preprocess_f1.py` — Python script to convert CSVs to SQL
- `f1_app/` — PHP web application
- `l06_constraints_triggers.sql` — Constraints and triggers
- `f1_ER_diagram_1.png` — First iteration of the E/R diagram with 12 tables, before SPRINT_RESULT was added
- `f1_ER_diagram_2.png` — Final version of the E/R diagram with all 13 tables including SPRINT_RESULT, cleaner layout

## Dataset
Kaggle: https://www.kaggle.com/datasets/rohanrao/formula-1-world-championship-1950-2020

## How to run
1. Start XAMPP (Apache + MySQL)
2. Import f1_database.sql into phpMyAdmin as f1_db
3. Copy f1_app/ to /Applications/XAMPP/xamppfiles/htdocs/
4. Open http://localhost/f1_app
