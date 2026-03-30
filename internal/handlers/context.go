package handlers

import (
	"laycms/internal/db"
	"laycms/internal/models"
	"laycms/internal/tmpl"

	"github.com/gin-contrib/sessions"
	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

const sessUserKey = "cms_user_id"

func gormDB() *gorm.DB {
	return db.ORM
}

func CurrentUser(c *gin.Context) *models.CmsUser {
	v, exists := c.Get("cms_user")
	if !exists {
		return nil
	}
	u, _ := v.(*models.CmsUser)
	return u
}

func loadUserIntoContext(c *gin.Context) {
	sess := sessions.Default(c)
	v := sess.Get(sessUserKey)
	if v == nil {
		return
	}
	var uid int64
	switch t := v.(type) {
	case int64:
		uid = t
	case int:
		uid = int64(t)
	case uint:
		uid = int64(t)
	case float64:
		uid = int64(t)
	default:
		return
	}
	var u models.CmsUser
	if err := gormDB().Preload("UserGroup").First(&u, uid).Error; err != nil || !u.IsActive {
		return
	}
	c.Set("cms_user", &u)
}

func EnsureUser() gin.HandlerFunc {
	return func(c *gin.Context) {
		if CurrentUser(c) == nil {
			if tmpl.WantsJSON(c) {
				c.JSON(401, gin.H{"code": 401, "msg": "请先登录"})
				c.Abort()
				return
			}
			c.Redirect(302, tmpl.URLFor("login"))
			c.Abort()
			return
		}
		c.Next()
	}
}

func requireAuth(c *gin.Context) *models.CmsUser {
	u := CurrentUser(c)
	if u == nil {
		if tmpl.WantsJSON(c) {
			c.JSON(401, gin.H{"code": 401, "msg": "请先登录"})
			c.Abort()
			return nil
		}
		c.Redirect(302, tmpl.URLFor("login"))
		c.Abort()
		return nil
	}
	return u
}

func requirePermission(resource, action string) gin.HandlerFunc {
	tableMap := map[string]string{"forms": "_forms", "users": "_users"}
	tbl := tableMap[resource]
	if tbl == "" {
		tbl = resource
	}
	check := action
	if action == "manage" {
		check = "read"
	}
	return func(c *gin.Context) {
		u := requireAuth(c)
		if u == nil {
			return
		}
		if u.IsRoot {
			c.Next()
			return
		}
		if !u.HasPermission(gormDB(), tbl, check) {
			if tmpl.WantsJSON(c) {
				c.JSON(403, gin.H{"code": 403, "msg": "无权限"})
				c.Abort()
				return
			}
			c.Redirect(302, tmpl.URLFor("no_permission"))
			c.Abort()
			return
		}
		c.Next()
	}
}

func requireTablePermission(action string) gin.HandlerFunc {
	return func(c *gin.Context) {
		u := requireAuth(c)
		if u == nil {
			return
		}
		tableName := c.Param("table_name")
		if tableName == "" {
			tableName = c.Param("tableName")
		}
		if u.IsRoot {
			c.Next()
			return
		}
		if !u.CanAccessTable(gormDB(), tableName, action) {
			if tmpl.WantsJSON(c) {
				c.JSON(403, gin.H{"code": 403, "msg": "无权限"})
				c.Abort()
				return
			}
			c.Redirect(302, tmpl.URLFor("no_permission"))
			c.Abort()
			return
		}
		c.Next()
	}
}
