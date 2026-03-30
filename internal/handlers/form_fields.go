package handlers

import (
	"encoding/json"
	"fmt"
	"html/template"
	"laycms/internal/models"
	"laycms/internal/rawsql"
	"laycms/internal/tmpl"
	"strings"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

var controlTypes = map[string]struct{}{
	"input": {}, "textarea": {}, "select": {}, "radio": {}, "checkbox": {}, "date": {}, "datetime": {},
	"number": {}, "file": {}, "editor": {}, "relation": {},
}

func formFieldsIndex(c *gin.Context) {
	u := CurrentUser(c)
	fid := atoi(c.Param("form_id"), 0)
	var f models.Form
	if err := gormDB().Preload("Fields", func(db *gorm.DB) *gorm.DB {
		return db.Order("sort_order ASC")
	}).First(&f, fid).Error; err != nil {
		c.Status(404)
		return
	}
	page := tmpl.BuildPage(c, gormDB(), u, "字段配置 - "+f.Name, "")
	var rows []map[string]any
	for _, ff := range f.Fields {
		m := map[string]any{
			"id": ff.ID, "field_name": ff.FieldName, "label": ff.Label,
			"form_control": ff.FormControl, "sort_order": ff.SortOrder,
			"is_required": ff.IsRequired, "is_list_visible": ff.IsListVisible,
		}
		if ff.Options != nil {
			m["options"] = *ff.Options
		}
		rows = append(rows, m)
	}
	b, _ := json.Marshal(rows)
	type frag struct {
		*tmpl.Page
		Form       models.Form
		FieldsJSON template.JS
	}
	_ = tmpl.RenderFragmentWith(c, "form_fields", &frag{page, f, template.JS(b)}, page)
}

func formFieldsStore(c *gin.Context) {
	fid := atoi(c.PostForm("form_id"), 0)
	var f models.Form
	if err := gormDB().First(&f, fid).Error; err != nil {
		c.Status(404)
		return
	}
	fieldName := strings.ToLower(strings.TrimSpace(c.PostForm("field_name")))
	if !rawsql.SafeIdent(fieldName) {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 1, "msg": "字段名只能包含小写字母、数字和下划线"})
			return
		}
		c.Redirect(302, "/form-fields/"+c.PostForm("form_id"))
		return
	}
	ctrl := c.DefaultPostForm("form_control", "input")
	if _, ok := controlTypes[ctrl]; !ok {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 1, "msg": "无效的表单控件类型"})
			return
		}
		c.Redirect(302, "/form-fields/"+c.PostForm("form_id"))
		return
	}
	var cnt int64
	gormDB().Model(&models.FormField{}).Where("form_id = ? AND field_name = ?", f.ID, fieldName).Count(&cnt)
	if cnt > 0 {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 1, "msg": "该字段已存在"})
			return
		}
		c.Redirect(302, "/form-fields/"+c.PostForm("form_id"))
		return
	}
	var maxOrder int16
	gormDB().Raw("SELECT IFNULL(MAX(sort_order),0) FROM cms_form_fields WHERE form_id = ?", f.ID).Scan(&maxOrder)
	so := int16(atoi(c.PostForm("sort_order"), 0))
	if so <= 0 {
		so = maxOrder + 1
	}
	opts := c.PostForm("options")
	attr := c.PostForm("attributes")
	var op, at *string
	if strings.TrimSpace(opts) != "" {
		op = &opts
	}
	if strings.TrimSpace(attr) != "" {
		at = &attr
	}
	ff := models.FormField{
		FormID: f.ID, FieldName: fieldName, Label: c.PostForm("label"), FormControl: ctrl,
		Options: op, Attributes: at, SortOrder: so,
		IsRequired: c.PostForm("is_required") == "true" || c.PostForm("is_required") == "on" || c.PostForm("is_required") == "1",
		IsListVisible: c.PostForm("is_list_visible") != "false" && c.PostForm("is_list_visible") != "0",
	}
	gormDB().Create(&ff)
	addColumnForField(f.DataTable, &ff)
	if tmpl.WantsJSON(c) {
		c.JSON(200, gin.H{"code": 0, "msg": "添加成功", "data": gin.H{"id": ff.ID}})
		return
	}
	c.Redirect(302, tmpl.URLFor("form_fields_index", "form_id", fmt.Sprintf("%d", fid)))
}

func addColumnForField(table string, field *models.FormField) {
	typeMap := map[string]string{
		"number": "BIGINT", "relation": "BIGINT", "date": "DATE", "datetime": "DATETIME",
		"textarea": "TEXT", "editor": "TEXT", "file": "VARCHAR(500)", "radio": "VARCHAR(255)",
		"checkbox": "VARCHAR(255)", "select": "VARCHAR(255)",
	}
	ct := typeMap[field.FormControl]
	if ct == "" {
		ct = "VARCHAR(255)"
	}
	null := "NULL"
	if field.IsRequired {
		null = "NOT NULL"
	}
	_ = gormDB().Exec(fmt.Sprintf("ALTER TABLE `%s` ADD COLUMN `%s` %s %s", table, field.FieldName, ct, null)).Error
}

func formFieldsUpdate(c *gin.Context) {
	fid := atoi(c.Param("field_id"), 0)
	var field models.FormField
	if err := gormDB().Preload("Form").First(&field, fid).Error; err != nil {
		c.Status(404)
		return
	}
	ctrl := c.DefaultPostForm("form_control", "input")
	if _, ok := controlTypes[ctrl]; !ok {
		c.JSON(400, gin.H{"code": 1, "msg": "无效的表单控件类型"})
		return
	}
	oldReq := field.IsRequired
	field.Label = c.PostForm("label")
	field.FormControl = ctrl
	opts := c.PostForm("options")
	attr := c.PostForm("attributes")
	if strings.TrimSpace(opts) == "" {
		field.Options = nil
	} else {
		field.Options = &opts
	}
	if strings.TrimSpace(attr) == "" {
		field.Attributes = nil
	} else {
		field.Attributes = &attr
	}
	field.SortOrder = int16(atoi(c.PostForm("sort_order"), 0))
	field.IsRequired = c.PostForm("is_required") == "true" || c.PostForm("is_required") == "on" || c.PostForm("is_required") == "1"
	field.IsListVisible = c.PostForm("is_list_visible") != "false" && c.PostForm("is_list_visible") != "0"
	gormDB().Save(&field)
	if oldReq != field.IsRequired {
		modifyColumnNullable(field.Form.DataTable, field.FieldName, field.IsRequired)
	}
	c.JSON(200, gin.H{"code": 0, "msg": "更新成功"})
}

func modifyColumnNullable(table, column string, notNull bool) {
	ct := rawsql.ColumnType(gormDB(), table, column)
	null := "NULL"
	if notNull {
		null = "NOT NULL"
	}
	_ = gormDB().Exec(fmt.Sprintf("ALTER TABLE `%s` MODIFY COLUMN `%s` %s %s", table, column, ct, null)).Error
}

func formFieldsDestroy(c *gin.Context) {
	fid := atoi(c.Param("field_id"), 0)
	var field models.FormField
	if err := gormDB().Preload("Form").First(&field, fid).Error; err != nil {
		c.Status(404)
		return
	}
	_ = gormDB().Exec(fmt.Sprintf("ALTER TABLE `%s` DROP COLUMN `%s`", field.Form.DataTable, field.FieldName)).Error
	gormDB().Delete(&field)
	c.JSON(200, gin.H{"code": 0, "msg": "删除成功"})
}
