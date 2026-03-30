package handlers

import (
	"crypto/md5"
	"fmt"
	"laycms/internal/models"
	"laycms/internal/rawsql"
	"strings"

	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

func formRelationsStore(c *gin.Context) {
	fid := atoi(c.PostForm("form_id"), 0)
	var form models.Form
	if err := gormDB().First(&form, fid).Error; err != nil {
		c.Status(404)
		return
	}
	rid := atoi(c.PostForm("related_form_id"), 0)
	var related models.Form
	if err := gormDB().First(&related, rid).Error; err != nil {
		c.Status(404)
		return
	}
	if form.ID == related.ID {
		c.JSON(400, gin.H{"code": 1, "msg": "不能关联到自身"})
		return
	}
	refName := strings.TrimSpace(c.PostForm("related_field_name"))
	if len(refName) > 64 {
		refName = refName[:64]
	}
	if rawsql.TableExists(gormDB(), related.DataTable) {
		cols := rawsql.TableColumns(gormDB(), related.DataTable)
		ok := false
		for _, c := range cols {
			if c == refName {
				ok = true
				break
			}
		}
		if !ok {
			c.JSON(400, gin.H{"code": 1, "msg": "关联表中不存在该字段"})
			return
		}
	}
	var formField *models.FormField
	if ffid := atoi(c.PostForm("form_field_id"), 0); ffid > 0 {
		var ff models.FormField
		if err := gormDB().Where("form_id = ? AND id = ?", form.ID, ffid).First(&ff).Error; err != nil {
			c.Status(404)
			return
		}
		var rc int64
		gormDB().Model(&models.FormRelation{}).Where("form_field_id = ?", ff.ID).Count(&rc)
		if rc > 0 {
			c.JSON(400, gin.H{"code": 1, "msg": "该字段已被用于关联"})
			return
		}
		ff.FormControl = "relation"
		gormDB().Save(&ff)
		formField = &ff
	} else {
		fname := strings.ToLower(strings.TrimSpace(c.PostForm("field_name")))
		if fname == "" {
			c.JSON(400, gin.H{"code": 1, "msg": "请选择现有字段或输入新字段名"})
			return
		}
		if !rawsql.SafeIdent(fname) {
			c.JSON(400, gin.H{"code": 1, "msg": "字段名只能包含小写字母、数字和下划线"})
			return
		}
		var ff models.FormField
		err := gormDB().Where("form_id = ? AND field_name = ?", form.ID, fname).First(&ff).Error
		if err == nil {
			var rc int64
			gormDB().Model(&models.FormRelation{}).Where("form_field_id = ?", ff.ID).Count(&rc)
			if rc > 0 {
				c.JSON(400, gin.H{"code": 1, "msg": "该字段已被用于关联"})
				return
			}
			ff.FormControl = "relation"
			gormDB().Save(&ff)
			formField = &ff
		} else {
			var maxOrder int16
			gormDB().Raw("SELECT IFNULL(MAX(sort_order),0) FROM cms_form_fields WHERE form_id = ?", form.ID).Scan(&maxOrder)
			lbl := related.Name + "ID"
			ff = models.FormField{
				FormID: form.ID, FieldName: fname, Label: lbl, FormControl: "relation",
				SortOrder: maxOrder + 1, IsRequired: false, IsListVisible: true,
			}
			gormDB().Create(&ff)
			addColumnForRelation(form.DataTable, related.DataTable, fname, refName)
			formField = &ff
		}
	}
	rel := models.FormRelation{
		FormID: form.ID, FormFieldID: formField.ID, RelatedFormID: related.ID, RelatedFieldName: refName,
	}
	if err := gormDB().Create(&rel).Error; err != nil {
		c.JSON(400, gin.H{"code": 1, "msg": err.Error()})
		return
	}
	if err := addForeignKey(gormDB(), form.DataTable, formField.FieldName, related.DataTable, refName); err != nil {
		gormDB().Delete(&rel)
		c.JSON(400, gin.H{"code": 1, "msg": fmt.Sprintf("MySQL 外键添加失败: %v", err)})
		return
	}
	var out models.FormRelation
	gormDB().Preload("FormField").Preload("RelatedForm").First(&out, rel.ID)
	data := gin.H{
		"id": out.ID, "form_id": out.FormID, "form_field_id": out.FormFieldID,
		"related_form_id": out.RelatedFormID, "related_field_name": out.RelatedFieldName,
	}
	if out.FormField != nil {
		data["form_field"] = gin.H{"id": out.FormField.ID, "field_name": out.FormField.FieldName, "label": out.FormField.Label}
	}
	if out.RelatedForm != nil {
		data["related_form"] = gin.H{"id": out.RelatedForm.ID, "name": out.RelatedForm.Name, "table_name": out.RelatedForm.DataTable}
	}
	c.JSON(200, gin.H{"code": 0, "msg": "关联添加成功", "data": data})
}

func addColumnForRelation(table, relatedTable, column, relatedCol string) {
	if rawsql.ColumnExists(gormDB(), table, column) {
		return
	}
	ct := columnTypeFromRelated(relatedTable, relatedCol)
	_ = gormDB().Exec(fmt.Sprintf("ALTER TABLE `%s` ADD COLUMN `%s` %s NULL", table, column, ct)).Error
}

func columnTypeFromRelated(relatedTable, relatedCol string) string {
	row, err := rawsql.FetchOneMap(gormDB(),
		"SELECT COLUMN_TYPE FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ? LIMIT 1",
		relatedTable, relatedCol)
	if err != nil || row == nil {
		return "bigint"
	}
	if t, ok := row["COLUMN_TYPE"].([]byte); ok {
		return string(t)
	}
	return fmt.Sprint(row["COLUMN_TYPE"])
}

func addForeignKey(db *gorm.DB, table, column, relatedTable, relatedCol string) error {
	ensureColumnTypeForFK(db, table, column, relatedTable, relatedCol)
	var names []string
	rows, err := db.Raw(
		"SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME = ?",
		table, column, relatedTable,
	).Rows()
	if err != nil {
		return err
	}
	defer rows.Close()
	for rows.Next() {
		var n string
		rows.Scan(&n)
		names = append(names, n)
	}
	if len(names) > 0 {
		return nil
	}
	fk := fmt.Sprintf("fk_%s_%s", table, column)
	if len(fk) > 64 {
		h := md5.Sum([]byte(table + "_" + column))
		fk = "fk_" + fmt.Sprintf("%x", h)[:16]
	}
	sql := fmt.Sprintf(
		"ALTER TABLE `%s` ADD CONSTRAINT `%s` FOREIGN KEY (`%s`) REFERENCES `%s` (`%s`) ON DELETE SET NULL ON UPDATE CASCADE",
		table, fk, column, relatedTable, relatedCol,
	)
	return db.Exec(sql).Error
}

func ensureColumnTypeForFK(db *gorm.DB, table, column, relatedTable, relatedCol string) {
	if !rawsql.ColumnExists(db, table, column) {
		return
	}
	cur := rawsql.ColumnType(db, table, column)
	need := rawsql.ColumnType(db, relatedTable, relatedCol)
	if rawsql.NormalizeTypeForCompare(cur, need) {
		return
	}
	_ = db.Exec(fmt.Sprintf("ALTER TABLE `%s` MODIFY COLUMN `%s` %s NULL", table, column, need)).Error
}

func formRelationsDestroy(c *gin.Context) {
	rid := atoi(c.Param("relation_id"), 0)
	var rel models.FormRelation
	if err := gormDB().Preload("Form").Preload("FormField").First(&rel, rid).Error; err != nil {
		c.Status(404)
		return
	}
	dropForeignKey(gormDB(), rel.Form.DataTable, rel.FormField.FieldName)
	gormDB().Delete(&rel)
	c.JSON(200, gin.H{"code": 0, "msg": "关联已删除"})
}

func dropForeignKey(db *gorm.DB, table, column string) {
	rows, err := db.Raw(
		"SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL",
		table, column,
	).Rows()
	if err != nil {
		return
	}
	defer rows.Close()
	for rows.Next() {
		var n string
		rows.Scan(&n)
		_ = db.Exec(fmt.Sprintf("ALTER TABLE `%s` DROP FOREIGN KEY `%s`", table, n)).Error
	}
}

func formRelatedColumnsByForm(c *gin.Context) {
	fid := atoi(c.Param("form_id"), 0)
	var f models.Form
	if err := gormDB().First(&f, fid).Error; err != nil {
		c.Status(404)
		return
	}
	if !rawsql.TableExists(gormDB(), f.DataTable) {
		c.JSON(200, gin.H{"columns": []string{}})
		return
	}
	c.JSON(200, gin.H{"columns": rawsql.TableColumns(gormDB(), f.DataTable)})
}
