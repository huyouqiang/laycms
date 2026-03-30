package handlers

import (
	"database/sql"
	"encoding/json"
	"fmt"
	"html/template"
	"strconv"
	"strings"
	"time"

	"laycms/internal/models"
	"laycms/internal/rawsql"
	"laycms/internal/tabledata"
	"laycms/internal/tmpl"

	"github.com/gin-contrib/sessions"
	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

func jsonRow(m map[string]any) {
	for k, v := range m {
		m[k] = normalizeCellForJSON(v)
	}
}

// normalizeCellForJSON 将 Raw 查询结果转为可被 JSON 正确展示的值（尤其是 DATETIME/DATE 与 *time.Time、NullTime）。
func normalizeCellForJSON(v any) any {
	if v == nil {
		return nil
	}
	switch t := v.(type) {
	case []byte:
		s := string(t)
		if s == "" {
			return nil
		}
		return s
	case time.Time:
		if t.IsZero() {
			return nil
		}
		return t.Format("2006-01-02 15:04:05")
	case *time.Time:
		if t == nil || t.IsZero() {
			return nil
		}
		return t.Format("2006-01-02 15:04:05")
	case sql.NullTime:
		if !t.Valid || t.Time.IsZero() {
			return nil
		}
		return t.Time.Format("2006-01-02 15:04:05")
	}
	if nt, ok := v.(sql.Null[time.Time]); ok {
		if !nt.Valid || nt.V.IsZero() {
			return nil
		}
		return nt.V.Format("2006-01-02 15:04:05")
	}
	return v
}

// listColumnSkipsSchemaPK 表头已有固定 ID 列；表单里若对主键字段勾选「列表显示」则不在动态列里再渲染一列。
func listColumnSkipsSchemaPK(fieldName string) bool {
	s := strings.TrimSpace(fieldName)
	s = strings.Trim(s, "`\"'")
	return strings.EqualFold(s, "id")
}

func tableRelationOptions(c *gin.Context) {
	table := c.Query("table")
	ref := c.Query("ref")
	display := c.Query("display")
	q := strings.TrimSpace(c.Query("q"))
	if !rawsql.SafeIdent(table) || !rawsql.SafeIdent(ref) || !rawsql.SafeIdent(display) {
		c.JSON(200, gin.H{"data": []any{}})
		return
	}
	var cnt int64
	gormDB().Model(&models.Form{}).Where("table_name = ?", table).Count(&cnt)
	if cnt == 0 || !rawsql.TableExists(gormDB(), table) {
		c.JSON(200, gin.H{"data": []any{}})
		return
	}
	tbl := rawsql.QTable(table)
	refC := rawsql.QCol(ref)
	dispC := rawsql.QCol(display)
	var rows []map[string]any
	var err error
	if q != "" {
		pat := "%" + q + "%"
		rows, err = rawsql.FetchAllMaps(gormDB(),
			fmt.Sprintf("SELECT %s, %s FROM %s WHERE %s LIKE ? OR %s LIKE ? ORDER BY id LIMIT 50", refC, dispC, tbl, dispC, refC),
			pat, pat)
	} else {
		rows, err = rawsql.FetchAllMaps(gormDB(),
			fmt.Sprintf("SELECT %s, %s FROM %s ORDER BY id LIMIT 50", refC, dispC, tbl))
	}
	if err != nil {
		c.JSON(200, gin.H{"data": []any{}})
		return
	}
	var data []gin.H
	for _, r := range rows {
		v0, v1 := r[ref], r[display]
		label := fmt.Sprint(v1)
		if v1 == nil {
			label = fmt.Sprint(v0)
		}
		data = append(data, gin.H{"value": v0, "label": label})
	}
	c.JSON(200, gin.H{"data": data})
}

func tableDataIndex(c *gin.Context) {
	u := CurrentUser(c)
	tableName := c.Param("table_name")
	search := strings.TrimSpace(c.Query("search"))
	page := atoi(c.DefaultQuery("page", "1"), 1)
	limit := atoi(c.DefaultQuery("limit", "15"), 15)
	if limit > 100 {
		limit = 100
	}
	if page < 1 {
		page = 1
	}

	var form models.Form
	if err := gormDB().Preload("Fields", func(db *gorm.DB) *gorm.DB {
		return db.Order("sort_order ASC")
	}).Where("table_name = ?", tableName).First(&form).Error; err != nil {
		c.Status(404)
		return
	}
	if !rawsql.TableExists(gormDB(), tableName) {
		c.Status(404)
		return
	}
	tbl := rawsql.QTable(tableName)
	whereClause := ""
	var args []any
	if search != "" {
		if strings.HasPrefix(strings.ToLower(search), "where ") {
			rawWhere := strings.TrimSpace(search[6:])
			allowed := rawsql.TableColumns(gormDB(), tableName)
			wsql, wargs := tabledata.ParseSafeWhere(rawWhere, allowed, nil)
			if wsql != "" {
				whereClause = " WHERE (" + wsql + ")"
				args = wargs
			}
		} else {
			var conds []string
			for _, f := range form.Fields {
				if !f.IsListVisible {
					continue
				}
				args = append(args, "%"+search+"%")
				if f.FormControl == "number" || f.FormControl == "relation" {
					conds = append(conds, "CAST("+rawsql.QCol(f.FieldName)+" AS CHAR) LIKE ?")
				} else {
					conds = append(conds, rawsql.QCol(f.FieldName)+" LIKE ?")
				}
			}
			if len(conds) > 0 {
				whereClause = " WHERE (" + strings.Join(conds, " OR ") + ")"
			}
		}
	}
	countQ := "SELECT COUNT(*) FROM " + tbl + whereClause
	total, err := rawsql.Count(gormDB(), countQ, args...)
	if err != nil {
		c.Status(500)
		return
	}
	offset := (page - 1) * limit
	args2 := append(append([]any{}, args...), limit, offset)
	dataQ := "SELECT * FROM " + tbl + whereClause + " ORDER BY id DESC LIMIT ? OFFSET ?"
	rows, err := rawsql.FetchAllMaps(gormDB(), dataQ, args2...)
	if err != nil {
		c.Status(500)
		return
	}
	for _, m := range rows {
		jsonRow(m)
	}
	if tmpl.WantsJSON(c) || page > 1 {
		c.JSON(200, gin.H{"code": 0, "count": total, "data": rows, "msg": ""})
		return
	}
	var listFields []gin.H
	for _, f := range form.Fields {
		if !f.IsListVisible {
			continue
		}
		if listColumnSkipsSchemaPK(f.FieldName) {
			continue
		}
		listFields = append(listFields, gin.H{
			"name":    f.FieldName,
			"label":   f.Label,
			"control": f.FormControl,
			"opts":    f.OptionsMap(),
		})
	}
	b, _ := json.Marshal(listFields)
	pageObj := tmpl.BuildPage(c, gormDB(), u, form.Name+" - 数据管理", tableName)
	type frag struct {
		*tmpl.Page
		Form           models.Form
		ListFieldsJSON template.JS
	}
	_ = tmpl.RenderFragmentWith(c, "table_data_index", &frag{pageObj, form, template.JS(b)}, pageObj)
}

func atoi(s string, def int) int {
	n, err := strconv.Atoi(strings.TrimSpace(s))
	if err != nil {
		return def
	}
	return n
}

func validateRequired(form *models.Form, c *gin.Context) string {
	for _, field := range form.Fields {
		if !field.IsRequired {
			continue
		}
		if field.FormControl == "checkbox" {
			if len(c.PostFormArray(field.FieldName+"[]")) == 0 {
				return field.Label + "不能为空"
			}
		} else {
			v := strings.TrimSpace(c.PostForm(field.FieldName))
			if v == "" {
				return field.Label + "不能为空"
			}
		}
	}
	return ""
}

func tableDataCreatePage(c *gin.Context) {
	u := CurrentUser(c)
	tableName := c.Param("table_name")
	var form models.Form
	if err := gormDB().Preload("Fields.Relation.RelatedForm.Fields").Where("table_name = ?", tableName).First(&form).Error; err != nil {
		c.Status(404)
		return
	}
	page := tmpl.BuildPage(c, gormDB(), u, "新增 - "+form.Name, tableName)
	type frag struct {
		*tmpl.Page
		Form      models.Form
		TableName string
		Row       map[string]any
		IsEdit    bool
	}
	_ = tmpl.RenderFragmentWith(c, "table_data_form", &frag{page, form, tableName, nil, false}, page)
}

func tableDataStore(c *gin.Context) {
	tableName := c.Param("table_name")
	var form models.Form
	if err := gormDB().Preload("Fields").Where("table_name = ?", tableName).First(&form).Error; err != nil {
		c.Status(404)
		return
	}
	if msg := validateRequired(&form, c); msg != "" {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 1, "msg": msg})
			return
		}
		sess := sessions.Default(c)
		sess.Set("flash_errors", []string{msg})
		_ = sess.Save()
		c.Redirect(302, tmpl.URLFor("table_data_create", "table_name", tableName))
		return
	}
	data := collectFormData(c, &form)
	data["created_at"] = time.Now()
	data["updated_at"] = time.Now()
	if err := insertDynamic(gormDB(), tableName, data); err != nil {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 1, "msg": err.Error()})
			return
		}
		return
	}
	if tmpl.WantsJSON(c) {
		c.JSON(200, gin.H{"code": 0, "msg": "添加成功"})
		return
	}
	sess := sessions.Default(c)
	sess.Set("flash_success", "添加成功")
	_ = sess.Save()
	c.Redirect(302, tmpl.URLFor("table_data_index", "table_name", tableName))
}

func tableDataEditPage(c *gin.Context) {
	u := CurrentUser(c)
	tableName := c.Param("table_name")
	id := atoi(c.Param("id"), 0)
	var form models.Form
	if err := gormDB().Preload("Fields.Relation.RelatedForm.Fields").Where("table_name = ?", tableName).First(&form).Error; err != nil {
		c.Status(404)
		return
	}
	tbl := rawsql.QTable(tableName)
	row, err := rawsql.FetchOneMap(gormDB(), "SELECT * FROM "+tbl+" WHERE id = ?", id)
	if err != nil {
		c.Status(404)
		return
	}
	jsonRow(row)
	page := tmpl.BuildPage(c, gormDB(), u, "编辑 - "+form.Name, tableName)
	type frag struct {
		*tmpl.Page
		Form      models.Form
		TableName string
		Row       map[string]any
		IsEdit    bool
	}
	_ = tmpl.RenderFragmentWith(c, "table_data_form", &frag{page, form, tableName, row, true}, page)
}

func tableDataUpdate(c *gin.Context) {
	tableName := c.Param("table_name")
	id := atoi(c.Param("id"), 0)
	var form models.Form
	if err := gormDB().Preload("Fields").Where("table_name = ?", tableName).First(&form).Error; err != nil {
		c.Status(404)
		return
	}
	if msg := validateRequired(&form, c); msg != "" {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 1, "msg": msg})
			return
		}
		sess := sessions.Default(c)
		sess.Set("flash_errors", []string{msg})
		_ = sess.Save()
		c.Redirect(302, tmpl.URLFor("table_data_edit", "table_name", tableName, "id", fmt.Sprintf("%d", id)))
		return
	}
	data := collectFormData(c, &form)
	data["updated_at"] = time.Now()
	tbl := rawsql.QTable(tableName)
	if err := updateDynamic(gormDB(), tbl, id, data); err != nil {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 1, "msg": err.Error()})
			return
		}
		return
	}
	if tmpl.WantsJSON(c) {
		c.JSON(200, gin.H{"code": 0, "msg": "更新成功"})
		return
	}
	sess := sessions.Default(c)
	sess.Set("flash_success", "更新成功")
	_ = sess.Save()
	c.Redirect(302, tmpl.URLFor("table_data_index", "table_name", tableName))
}

func tableDataDestroyNoID(c *gin.Context) {
	if tmpl.WantsJSON(c) {
		c.JSON(400, gin.H{"code": 1, "msg": "删除需要指定记录 ID，请使用 DELETE /table-data/{table_name}/{id}"})
		return
	}
	tableName := c.Param("table_name")
	c.Redirect(302, tmpl.URLFor("table_data_index", "table_name", tableName))
}

func tableDataDestroy(c *gin.Context) {
	tableName := c.Param("table_name")
	id := atoi(c.Param("id"), 0)
	tbl := rawsql.QTable(tableName)
	err := gormDB().Exec("DELETE FROM "+tbl+" WHERE id = ?", id).Error
	if err != nil {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 1, "msg": err.Error()})
			return
		}
		sess := sessions.Default(c)
		sess.Set("flash_errors", []string{err.Error()})
		_ = sess.Save()
		c.Redirect(302, tmpl.URLFor("table_data_index", "table_name", tableName))
		return
	}
	if tmpl.WantsJSON(c) {
		c.JSON(200, gin.H{"code": 0, "msg": "删除成功"})
		return
	}
	c.Redirect(302, tmpl.URLFor("table_data_index", "table_name", tableName))
}

