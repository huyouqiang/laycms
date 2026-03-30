package rawsql

import (
	"database/sql"
	"strings"

	"gorm.io/gorm"
)

func TableExists(db *gorm.DB, table string) bool {
	var n int
	err := db.Raw(
		"SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1",
		table,
	).Scan(&n).Error
	return err == nil && n == 1
}

func ColumnExists(db *gorm.DB, table, column string) bool {
	var n int
	err := db.Raw(
		"SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ? LIMIT 1",
		table, column,
	).Scan(&n).Error
	return err == nil && n == 1
}

func TableColumns(db *gorm.DB, table string) []string {
	var names []string
	rows, err := db.Raw(
		"SELECT column_name FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? ORDER BY ordinal_position",
		table,
	).Rows()
	if err != nil {
		return nil
	}
	defer rows.Close()
	for rows.Next() {
		var c string
		if rows.Scan(&c) == nil {
			names = append(names, c)
		}
	}
	return names
}

func ColumnType(db *gorm.DB, table, column string) string {
	var t sql.NullString
	db.Raw(
		"SELECT COLUMN_TYPE FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ? LIMIT 1",
		table, column,
	).Scan(&t)
	if t.Valid {
		return t.String
	}
	return "varchar(255)"
}

func FetchOneMap(db *gorm.DB, query string, args ...any) (map[string]any, error) {
	rows, err := db.Raw(query, args...).Rows()
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	cols, _ := rows.Columns()
	if !rows.Next() {
		return nil, sql.ErrNoRows
	}
	vals := make([]any, len(cols))
	ptrs := make([]any, len(cols))
	for i := range vals {
		ptrs[i] = &vals[i]
	}
	if err := rows.Scan(ptrs...); err != nil {
		return nil, err
	}
	out := make(map[string]any)
	for i, c := range cols {
		out[c] = vals[i]
	}
	return out, nil
}

func FetchAllMaps(db *gorm.DB, query string, args ...any) ([]map[string]any, error) {
	rows, err := db.Raw(query, args...).Rows()
	if err != nil {
		return nil, err
	}
	defer rows.Close()
	cols, _ := rows.Columns()
	var list []map[string]any
	for rows.Next() {
		vals := make([]any, len(cols))
		ptrs := make([]any, len(cols))
		for i := range vals {
			ptrs[i] = &vals[i]
		}
		if err := rows.Scan(ptrs...); err != nil {
			return nil, err
		}
		m := make(map[string]any)
		for i, c := range cols {
			m[c] = vals[i]
		}
		list = append(list, m)
	}
	return list, nil
}

func Count(db *gorm.DB, query string, args ...any) (int64, error) {
	var n int64
	err := db.Raw(query, args...).Scan(&n).Error
	return n, err
}

// SafeIdent only allows [a-z][a-z0-9_]*
func SafeIdent(s string) bool {
	if s == "" {
		return false
	}
	for i, r := range s {
		if i == 0 {
			if r < 'a' || r > 'z' {
				return false
			}
			continue
		}
		if (r >= 'a' && r <= 'z') || (r >= '0' && r <= '9') || r == '_' {
			continue
		}
		return false
	}
	return true
}

func QTable(t string) string {
	if !SafeIdent(t) {
		return "`invalid`"
	}
	return "`" + t + "`"
}

func QCol(c string) string {
	if !SafeIdent(c) {
		return "`invalid`"
	}
	return "`" + c + "`"
}

func NormalizeTypeForCompare(a, b string) bool {
	return strings.ReplaceAll(strings.ToLower(a), " ", "") == strings.ReplaceAll(strings.ToLower(b), " ", "")
}
