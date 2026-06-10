import pandas as pd
import math

CSV_PATH = "f1_data/sprint_results.csv"
OUTPUT_FILE = "sprint_result_inserts.sql"

def clean_val(val):
    if val is None:
        return "NULL"
    if isinstance(val, float) and math.isnan(val):
        return "NULL"
    if str(val).strip() in ("\\N", "NA", "N/A", "nan", ""):
        return "NULL"
    val = str(val).replace("'", "\\'")
    return f"'{val}'"

print("Loading sprint_results.csv...")
df = pd.read_csv(CSV_PATH)
df.replace("\\N", pd.NA, inplace=True)

# Rename to match our table
df.rename(columns={"resultId": "sprintResultId"}, inplace=True)

cols = ", ".join(df.columns.tolist())
rows = []
for _, row in df.iterrows():
    vals = ", ".join(clean_val(v) for v in row)
    rows.append(f"  ({vals})")

sql = "USE f1_db;\n\n"
batch_size = 500
for i in range(0, len(rows), batch_size):
    batch = rows[i:i+batch_size]
    sql += f"INSERT INTO `SPRINT_RESULT` ({cols}) VALUES\n"
    sql += ",\n".join(batch) + ";\n\n"

with open(OUTPUT_FILE, "w", encoding="utf-8") as f:
    f.write(sql)

print(f"✅ Done! {len(df)} sprint results written to {OUTPUT_FILE}")
