package handlers

import (
	"laycms/internal/models"
	"laycms/internal/tmpl"

	"github.com/gin-gonic/gin"
)

func userGroupsIndex(c *gin.Context) {
	u := CurrentUser(c)
	var groups []models.UserGroup
	gormDB().Preload("Users").Order("id ASC").Find(&groups)
	counts := map[int64]int{}
	for _, g := range groups {
		counts[g.ID] = len(g.Users)
	}
	page := tmpl.BuildPage(c, gormDB(), u, "用户组", "")
	type frag struct {
		*tmpl.Page
		Groups      []models.UserGroup
		GroupCounts map[int64]int
	}
	_ = tmpl.RenderFragmentWith(c, "user_groups", &frag{page, groups, counts}, page)
}

func userGroupsStore(c *gin.Context) {
	name := c.PostForm("name")
	desc := c.PostForm("description")
	var d *string
	if desc != "" {
		d = &desc
	}
	g := models.UserGroup{Name: name, Description: d}
	gormDB().Create(&g)
	if tmpl.WantsJSON(c) {
		c.JSON(200, gin.H{"code": 0, "msg": "用户组创建成功"})
		return
	}
	c.Redirect(302, tmpl.URLFor("user_groups_index"))
}

func userGroupsUpdate(c *gin.Context) {
	id := atoi(c.Param("group_id"), 0)
	var g models.UserGroup
	if err := gormDB().First(&g, id).Error; err != nil {
		c.Status(404)
		return
	}
	g.Name = c.PostForm("name")
	desc := c.PostForm("description")
	if desc == "" {
		g.Description = nil
	} else {
		g.Description = &desc
	}
	gormDB().Save(&g)
	c.JSON(200, gin.H{"code": 0, "msg": "更新成功"})
}

func userGroupsDestroy(c *gin.Context) {
	id := atoi(c.Param("group_id"), 0)
	var g models.UserGroup
	if err := gormDB().Preload("Users").First(&g, id).Error; err != nil {
		c.Status(404)
		return
	}
	if len(g.Users) > 0 {
		c.JSON(400, gin.H{"code": 1, "msg": "该用户组下存在用户，无法删除"})
		return
	}
	gormDB().Where("user_group_id = ?", id).Delete(&models.GroupPermission{})
	gormDB().Delete(&g)
	c.JSON(200, gin.H{"code": 0, "msg": "删除成功"})
}
