package handlers

import (
	"laycms/internal/models"
	"laycms/internal/tmpl"

	"github.com/gin-contrib/sessions"
	"github.com/gin-gonic/gin"
)

func formGroupsIndex(c *gin.Context) {
	u := CurrentUser(c)
	var groups []models.FormGroup
	gormDB().Preload("Forms").Order("sort_order ASC, id ASC").Find(&groups)
	page := tmpl.BuildPage(c, gormDB(), u, "表单分组", "")
	type frag struct {
		*tmpl.Page
		Groups []models.FormGroup
	}
	_ = tmpl.RenderFragmentWith(c, "form_groups", &frag{page, groups}, page)
}

func formGroupsStore(c *gin.Context) {
	g := models.FormGroup{Name: c.PostForm("name"), SortOrder: int16(atoi(c.PostForm("sort_order"), 0))}
	gormDB().Create(&g)
	if tmpl.WantsJSON(c) {
		c.JSON(200, gin.H{"code": 0, "msg": "分组创建成功"})
		return
	}
	sess := sessions.Default(c)
	sess.Set("flash_success", "分组创建成功")
	_ = sess.Save()
	c.Redirect(302, tmpl.URLFor("form_groups_index"))
}

func formGroupsUpdate(c *gin.Context) {
	id := atoi(c.Param("group_id"), 0)
	var g models.FormGroup
	if err := gormDB().First(&g, id).Error; err != nil {
		c.Status(404)
		return
	}
	g.Name = c.PostForm("name")
	g.SortOrder = int16(atoi(c.PostForm("sort_order"), 0))
	gormDB().Save(&g)
	if tmpl.WantsJSON(c) {
		c.JSON(200, gin.H{"code": 0, "msg": "更新成功"})
		return
	}
	sess := sessions.Default(c)
	sess.Set("flash_success", "更新成功")
	_ = sess.Save()
	c.Redirect(302, tmpl.URLFor("form_groups_index"))
}

func formGroupsDeleteNoID(c *gin.Context) {
	if tmpl.WantsJSON(c) {
		c.JSON(400, gin.H{"code": 1, "msg": "删除需要指定分组 ID，请使用 DELETE /form-groups/{id}"})
		return
	}
	c.Redirect(302, tmpl.URLFor("form_groups_index"))
}

func formGroupsDestroy(c *gin.Context) {
	id := atoi(c.Param("group_id"), 0)
	var g models.FormGroup
	if err := gormDB().Preload("Forms").First(&g, id).Error; err != nil {
		c.Status(404)
		return
	}
	if len(g.Forms) > 0 {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 1, "msg": "该分组下存在表单，请先移出或删除表单"})
			return
		}
		c.Redirect(302, tmpl.URLFor("form_groups_index"))
		return
	}
	gormDB().Delete(&g)
	if tmpl.WantsJSON(c) {
		c.JSON(200, gin.H{"code": 0, "msg": "删除成功"})
		return
	}
	c.Redirect(302, tmpl.URLFor("form_groups_index"))
}
