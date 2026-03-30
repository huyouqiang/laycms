package handlers

import (
	"crypto/md5"
	"fmt"
	"laycms/internal/models"
	"laycms/internal/rawsql"
	"laycms/internal/tmpl"
	"strings"

	"github.com/gin-contrib/sessions"
	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

func formsIndex(c *gin.Context) {
	u := CurrentUser(c)
	var forms []models.Form
	gormDB().Preload("FormGroup").Preload("Fields").Order("sort_order ASC").Find(&forms)
	counts := map[int64]int{}
	for _, f := range forms {
		counts[f.ID] = len(f.Fields)
	}
	page := tmpl.BuildPage(c, gormDB(), u, "表单列表", "")
	type frag struct {
		*tmpl.Page
		Forms      []models.Form
		FormCounts map[int64]int
	}
	_ = tmpl.RenderFragmentWith(c, "forms_index", &frag{page, forms, counts}, page)
}

func formsCreatePage(c *gin.Context) {
	u := CurrentUser(c)
	var groups []models.FormGroup
	gormDB().Order("sort_order ASC").Find(&groups)
	page := tmpl.BuildPage(c, gormDB(), u, "新建表单", "")
	type frag struct {
		*tmpl.Page
		FormGroups []models.FormGroup
	}
	_ = tmpl.RenderFragmentWith(c, "forms_create", &frag{page, groups}, page)
}

func formsStore(c *gin.Context) {
	name := c.PostForm("name")
	tableName := strings.ToLower(strings.TrimSpace(c.PostForm("table_name")))
	desc := c.PostForm("description")
	gid := int64(atoi(c.PostForm("form_group_id"), 0))
	if !rawsql.SafeIdent(tableName) {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 1, "msg": "表名只能包含小写字母、数字和下划线"})
			return
		}
		c.Redirect(302, tmpl.URLFor("forms_create"))
		return
	}
	var cnt int64
	gormDB().Model(&models.Form{}).Where("table_name = ?", tableName).Count(&cnt)
	if cnt > 0 {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 1, "msg": "表名已存在"})
			return
		}
		c.Redirect(302, tmpl.URLFor("forms_create"))
		return
	}
	var d *string
	if strings.TrimSpace(desc) != "" {
		d = &desc
	}
	f := models.Form{Name: name, DataTable: tableName, Description: d, FormGroupID: gid}
	gormDB().Create(&f)
	sql := fmt.Sprintf(
		"CREATE TABLE `%s` (`id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY, `created_at` timestamp NULL, `updated_at` timestamp NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
		tableName,
	)
	_ = gormDB().Exec(sql).Error
	if tmpl.WantsJSON(c) {
		c.JSON(200, gin.H{"code": 0, "msg": "创建成功", "data": gin.H{"id": f.ID, "name": f.Name, "table_name": f.DataTable}})
		return
	}
	c.Redirect(302, tmpl.URLFor("forms_index"))
}

func formsRelatedColumns(c *gin.Context) {
	id := atoi(c.Param("id"), 0)
	var f models.Form
	if err := gormDB().First(&f, id).Error; err != nil {
		c.Status(404)
		return
	}
	if !rawsql.TableExists(gormDB(), f.DataTable) {
		c.JSON(200, gin.H{"columns": []string{}})
		return
	}
	c.JSON(200, gin.H{"columns": rawsql.TableColumns(gormDB(), f.DataTable)})
}

func formsEditPage(c *gin.Context) {
	u := CurrentUser(c)
	id := atoi(c.Param("id"), 0)
	var f models.Form
	if err := gormDB().Preload("Fields").Preload("Relations.FormField").Preload("Relations.RelatedForm").
		First(&f, id).Error; err != nil {
		c.Status(404)
		return
	}
	var groups []models.FormGroup
	gormDB().Order("sort_order ASC").Find(&groups)
	var others []models.Form
	gormDB().Where("id <> ?", id).Order("sort_order ASC").Find(&others)
	cols := []string{}
	if rawsql.TableExists(gormDB(), f.DataTable) {
		cols = rawsql.TableColumns(gormDB(), f.DataTable)
	}
	idx := tableIndexes(gormDB(), f.DataTable)
	page := tmpl.BuildPage(c, gormDB(), u, "编辑表单 - "+f.Name, "")
	type frag struct {
		*tmpl.Page
		Form           models.Form
		FormGroups     []models.FormGroup
		OtherForms     []models.Form
		TableColumns   []string
		TableIndexes   []indexRow
	}
	_ = tmpl.RenderFragmentWith(c, "forms_edit", &frag{page, f, groups, others, cols, idx}, page)
}

type indexRow struct {
	Name, Column, Type string
}

func tableIndexes(db *gorm.DB, tableName string) []indexRow {
	if !rawsql.TableExists(db, tableName) {
		return nil
	}
	rows, err := db.Raw(
		"SELECT INDEX_NAME, COLUMN_NAME, INDEX_TYPE, NON_UNIQUE FROM information_schema.STATISTICS WHERE table_schema = DATABASE() AND table_name = ? ORDER BY INDEX_NAME, SEQ_IN_INDEX",
		tableName,
	).Rows()
	if err != nil {
		return nil
	}
	defer rows.Close()
	grouped := map[string]*indexRow{}
	for rows.Next() {
		var iname, col, itype string
		var nonUnique int
		rows.Scan(&iname, &col, &itype, &nonUnique)
		g, ok := grouped[iname]
		if !ok {
			t := "普通"
			if iname == "PRIMARY" {
				t = "主键"
			} else if itype == "FULLTEXT" {
				t = "全文"
			} else if itype == "SPATIAL" {
				t = "空间"
			} else if nonUnique == 0 {
				t = "唯一"
			} else {
				t = fmt.Sprintf("普通 (%s)", itype)
			}
			grouped[iname] = &indexRow{Name: iname, Type: t, Column: col}
		} else {
			g.Column += ", " + col
		}
	}
	var out []indexRow
	for _, g := range grouped {
		out = append(out, *g)
	}
	return out
}

func formsUpdate(c *gin.Context) {
	id := atoi(c.Param("id"), 0)
	var f models.Form
	if err := gormDB().First(&f, id).Error; err != nil {
		c.Status(404)
		return
	}
	tableName := strings.ToLower(strings.TrimSpace(c.PostForm("table_name")))
	if !rawsql.SafeIdent(tableName) {
		c.JSON(400, gin.H{"code": 1, "msg": "表名只能包含小写字母、数字和下划线"})
		return
	}
	var cnt int64
	gormDB().Model(&models.Form{}).Where("table_name = ? AND id <> ?", tableName, id).Count(&cnt)
	if cnt > 0 {
		c.JSON(400, gin.H{"code": 1, "msg": "表名已存在"})
		return
	}
	f.Name = c.PostForm("name")
	f.DataTable = tableName
	desc := c.PostForm("description")
	if strings.TrimSpace(desc) == "" {
		f.Description = nil
	} else {
		f.Description = &desc
	}
	f.SortOrder = int16(atoi(c.PostForm("sort_order"), 0))
	f.FormGroupID = int64(atoi(c.PostForm("form_group_id"), 0))
	gormDB().Save(&f)
	c.JSON(200, gin.H{"code": 0, "msg": "更新成功"})
}

func formsAddIndex(c *gin.Context) {
	id := atoi(c.Param("id"), 0)
	var f models.Form
	if err := gormDB().First(&f, id).Error; err != nil {
		c.Status(404)
		return
	}
	col := strings.TrimSpace(c.PostForm("column_name"))
	if len(col) > 64 {
		col = col[:64]
	}
	if !rawsql.TableExists(gormDB(), f.DataTable) {
		c.JSON(400, gin.H{"code": 1, "msg": "数据表不存在"})
		return
	}
	cols := rawsql.TableColumns(gormDB(), f.DataTable)
	found := false
	for _, x := range cols {
		if x == col {
			found = true
			break
		}
	}
	if !found {
		c.JSON(400, gin.H{"code": 1, "msg": "字段不存在"})
		return
	}
	iname := fmt.Sprintf("idx_%s_%s", f.DataTable, col)
	if len(iname) > 64 {
		h := md5.Sum([]byte(f.DataTable + "_" + col))
		iname = "idx_" + fmt.Sprintf("%x", h)[:32]
	}
	var n int
	gormDB().Raw("SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND column_name = ? LIMIT 1", f.DataTable, col).Scan(&n)
	if n == 1 {
		c.JSON(200, gin.H{"code": 0, "msg": "该字段已有索引"})
		return
	}
	err := gormDB().Exec(fmt.Sprintf("ALTER TABLE `%s` ADD INDEX `%s` (`%s`)", f.DataTable, iname, col)).Error
	if err != nil {
		s := err.Error()
		if strings.Contains(s, "Duplicate") || strings.Contains(s, "already exists") {
			c.JSON(200, gin.H{"code": 0, "msg": "索引已存在"})
			return
		}
		c.JSON(400, gin.H{"code": 1, "msg": s})
		return
	}
	c.JSON(200, gin.H{"code": 0, "msg": "索引添加成功"})
}

func formsDropIndex(c *gin.Context) {
	_ = c.Request.ParseForm()
	id := atoi(c.Param("id"), 0)
	var f models.Form
	if err := gormDB().First(&f, id).Error; err != nil {
		c.Status(404)
		return
	}
	iname := strings.TrimSpace(c.PostForm("index_name"))
	if len(iname) > 64 {
		iname = iname[:64]
	}
	if iname == "PRIMARY" {
		c.JSON(400, gin.H{"code": 1, "msg": "不能删除主键索引"})
		return
	}
	if !rawsql.TableExists(gormDB(), f.DataTable) {
		c.JSON(400, gin.H{"code": 1, "msg": "数据表不存在"})
		return
	}
	var n int
	gormDB().Raw("SELECT 1 FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = ? AND index_name = ? LIMIT 1", f.DataTable, iname).Scan(&n)
	if n == 0 {
		c.JSON(400, gin.H{"code": 1, "msg": "索引不存在"})
		return
	}
	if err := gormDB().Exec(fmt.Sprintf("ALTER TABLE `%s` DROP INDEX `%s`", f.DataTable, iname)).Error; err != nil {
		c.JSON(400, gin.H{"code": 1, "msg": err.Error()})
		return
	}
	c.JSON(200, gin.H{"code": 0, "msg": "索引已删除"})
}

func formsDestroy(c *gin.Context) {
	id := atoi(c.Param("id"), 0)
	var f models.Form
	if err := gormDB().First(&f, id).Error; err != nil {
		c.Status(404)
		return
	}
	err := gormDB().Exec(fmt.Sprintf("DROP TABLE IF EXISTS `%s`", f.DataTable)).Error
	if err != nil {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 1, "msg": err.Error()})
			return
		}
		sess := sessions.Default(c)
		sess.Set("flash_errors", []string{err.Error()})
		_ = sess.Save()
		c.Redirect(302, tmpl.URLFor("forms_index"))
		return
	}
	gormDB().Where("form_id = ?", f.ID).Delete(&models.FormRelation{})
	gormDB().Where("form_id = ?", f.ID).Delete(&models.FormField{})
	gormDB().Delete(&f)
	if tmpl.WantsJSON(c) {
		c.JSON(200, gin.H{"code": 0, "msg": "删除成功"})
		return
	}
	c.Redirect(302, tmpl.URLFor("forms_index"))
}
