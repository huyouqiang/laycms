package handlers

import (
	"encoding/json"
	"html/template"
	"laycms/internal/models"
	"laycms/internal/tmpl"

	"github.com/gin-gonic/gin"
)

func usersIndex(c *gin.Context) {
	u := CurrentUser(c)
	var users []models.CmsUser
	gormDB().Preload("UserGroup").Order("id ASC").Find(&users)
	var groups []models.UserGroup
	gormDB().Order("id ASC").Find(&groups)
	page := tmpl.BuildPage(c, gormDB(), u, "用户列表", "")
	var urows []map[string]any
	for _, x := range users {
		m := map[string]any{"id": x.ID, "username": x.Username, "is_active": x.IsActive, "is_root": x.IsRoot}
		if x.Nickname != nil {
			m["nickname"] = *x.Nickname
		}
		if x.UserGroupID != nil {
			m["user_group_id"] = *x.UserGroupID
		}
		urows = append(urows, m)
	}
	uj, _ := json.Marshal(urows)
	type frag struct {
		*tmpl.Page
		Users     []models.CmsUser
		Groups    []models.UserGroup
		UsersJSON template.JS
	}
	_ = tmpl.RenderFragmentWith(c, "users", &frag{page, users, groups, template.JS(uj)}, page)
}

func usersStore(c *gin.Context) {
	username := c.PostForm("username")
	password := c.PostForm("password")
	var cnt int64
	gormDB().Model(&models.CmsUser{}).Where("username = ?", username).Count(&cnt)
	if cnt > 0 {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 1, "msg": "用户名已存在"})
			return
		}
		c.Redirect(302, tmpl.URLFor("users_index"))
		return
	}
	if len(password) < 6 {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 1, "msg": "密码至少6位"})
			return
		}
		c.Redirect(302, tmpl.URLFor("users_index"))
		return
	}
	gid := int64(atoi(c.PostForm("user_group_id"), 0))
	nick := c.PostForm("nickname")
	var nn *string
	if nick != "" {
		nn = &nick
	}
	nu := models.CmsUser{
		Username: username, Nickname: nn, UserGroupID: &gid,
		IsActive: c.PostForm("is_active") != "false" && c.PostForm("is_active") != "0",
		IsRoot:   false,
	}
	_ = nu.SetPassword(password)
	gormDB().Create(&nu)
	if tmpl.WantsJSON(c) {
		c.JSON(200, gin.H{"code": 0, "msg": "用户创建成功"})
		return
	}
	c.Redirect(302, tmpl.URLFor("users_index"))
}

func usersUpdate(c *gin.Context) {
	id := atoi(c.Param("user_id"), 0)
	var u models.CmsUser
	if err := gormDB().First(&u, id).Error; err != nil {
		c.Status(404)
		return
	}
	if u.IsRoot {
		c.JSON(403, gin.H{"code": 1, "msg": "不能修改根用户"})
		return
	}
	pw := c.PostForm("password")
	if pw != "" {
		if len(pw) < 6 {
			c.JSON(400, gin.H{"code": 1, "msg": "密码至少6位"})
			return
		}
		_ = u.SetPassword(pw)
	}
	nick := c.PostForm("nickname")
	if nick == "" {
		u.Nickname = nil
	} else {
		u.Nickname = &nick
	}
	gid := int64(atoi(c.PostForm("user_group_id"), 0))
	u.UserGroupID = &gid
	u.IsActive = c.PostForm("is_active") != "false" && c.PostForm("is_active") != "0"
	gormDB().Save(&u)
	c.JSON(200, gin.H{"code": 0, "msg": "更新成功"})
}

func usersDestroy(c *gin.Context) {
	id := atoi(c.Param("user_id"), 0)
	var u models.CmsUser
	if err := gormDB().First(&u, id).Error; err != nil {
		c.Status(404)
		return
	}
	if u.IsRoot {
		c.JSON(403, gin.H{"code": 1, "msg": "不能删除根用户"})
		return
	}
	gormDB().Delete(&u)
	c.JSON(200, gin.H{"code": 0, "msg": "删除成功"})
}
