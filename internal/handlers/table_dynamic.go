package handlers

import (
	"encoding/json"
	"fmt"
	"strings"

	"laycms/internal/models"
	"laycms/internal/rawsql"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

func collectFormData(c *gin.Context, form *models.Form) map[string]any {
	data := map[string]any{}
	for _, field := range form.Fields {
		var val any
		if field.FormControl == "checkbox" {
			arr := c.PostFormArray(field.FieldName + "[]")
			b, _ := json.Marshal(arr)
			val = string(b)
		} else {
			s := c.PostForm(field.FieldName)
			val = s
		}
		if field.FormControl == "number" || field.FormControl == "relation" {
			s := strings.TrimSpace(fmt.Sprint(val))
			if s == "" {
				continue
			}
			var n int64
			if _, err := fmt.Sscanf(s, "%d", &n); err == nil {
				val = n
			}
		}
		if val != nil && fmt.Sprint(val) != "" {
			data[field.FieldName] = val
		}
	}
	return data
}

func insertDynamic(db *gorm.DB, table string, data map[string]any) error {
	if !rawsql.SafeIdent(table) {
		return fmt.Errorf("invalid table")
	}
	var cols, ph []string
	var args []any
	for k, v := range data {
		if !rawsql.SafeIdent(k) {
			continue
		}
		cols = append(cols, rawsql.QCol(k))
		ph = append(ph, "?")
		args = append(args, v)
	}
	if len(cols) == 0 {
		return fmt.Errorf("no data")
	}
	q := "INSERT INTO " + rawsql.QTable(table) + " (" + strings.Join(cols, ", ") + ") VALUES (" + strings.Join(ph, ", ") + ")"
	return db.Exec(q, args...).Error
}

func updateDynamic(db *gorm.DB, tblQuoted string, id int, data map[string]any) error {
	var sets []string
	var args []any
	for k, v := range data {
		if !rawsql.SafeIdent(k) {
			continue
		}
		sets = append(sets, rawsql.QCol(k)+" = ?")
		args = append(args, v)
	}
	if len(sets) == 0 {
		return nil
	}
	args = append(args, id)
	q := "UPDATE " + tblQuoted + " SET " + strings.Join(sets, ", ") + " WHERE id = ?"
	return db.Exec(q, args...).Error
}
