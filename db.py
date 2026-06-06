"""
db.py – Helpers base de données
"""

import psycopg2
import psycopg2.extras
import os

# ============================================================
# CONFIGURATION BASE DE DONNÉES
# ============================================================
DB_CONFIG = {
    "host":     os.environ.get("DB_HOST",   "localhost"),
    "port":     int(os.environ.get("DB_PORT", 5432)),
    "dbname":   os.environ.get("DB_NAME",   "projet"),
    "user":     os.environ.get("DB_USER",   "anonyme"),
    "password": os.environ.get("DB_PASS",   "anonyme"),
}

# ============================================================
# HELPERS
# ============================================================
def get_connection():
    return psycopg2.connect(**DB_CONFIG)

def get_cursor(conn):
    return conn.cursor(cursor_factory=psycopg2.extras.RealDictCursor)

def query_all(sql: str, params: tuple = ()) -> list:
    try:
        conn = get_connection()
        cur  = get_cursor(conn)
        cur.execute(sql, params)
        rows = cur.fetchall()
        cur.close(); conn.close()
        return rows
    except Exception as e:
        print(f"[DB ERROR] query_all: {e}")
        return []

def query_one(sql: str, params: tuple = ()):
    try:
        conn = get_connection()
        cur  = get_cursor(conn)
        cur.execute(sql, params)
        row = cur.fetchone()
        cur.close(); conn.close()
        return row
    except Exception as e:
        print(f"[DB ERROR] query_one: {e}")
        return None