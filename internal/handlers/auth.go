package handlers

import (
	"laycms/internal/models"
	"laycms/internal/tmpl"

	"github.com/gin-contrib/sessions"
	"github.com/gin-gonic/gin"
)

func showLogin(c *gin.Context) {
	if CurrentUser(c) != nil {
		c.Redirect(302, tmpl.URLFor("dashboard"))
		return
	}
	sess := sessions.Default(c)
	_ = tmpl.CSRF(sess)
	_ = sess.Save()
	data := gin.H{
		"CSRFToken":    tmpl.CSRF(sess),
		"Errors":       []string{},
		"OldUsername":  "",
		"Title":        "登录",
		"FooterNote":   "LayCMS · 基于 Gin 构建",
	}
	if e, ok := c.Get("login_errors"); ok {
		if arr, ok := e.([]string); ok {
			data["Errors"] = arr
		}
	}
	if u, ok := c.Get("old_username"); ok {
		if s, ok := u.(string); ok {
			data["OldUsername"] = s
		}
	}
	_ = tmpl.RenderStandalone(c, "login", data)
}

func postLogin(c *gin.Context) {
	username := c.PostForm("username")
	password := c.PostForm("password")
	var u models.CmsUser
	if err := gormDB().Where("username = ?", username).First(&u).Error; err != nil || !u.CheckPassword(password) {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 400, "msg": "用户名或密码错误"})
			return
		}
		c.Set("login_errors", []string{"用户名或密码错误"})
		c.Set("old_username", username)
		showLogin(c)
		return
	}
	if !u.IsActive {
		if tmpl.WantsJSON(c) {
			c.JSON(400, gin.H{"code": 400, "msg": "账号已被禁用"})
			return
		}
		c.Set("login_errors", []string{"账号已被禁用"})
		c.Set("old_username", username)
		showLogin(c)
		return
	}
	sess := sessions.Default(c)
	sess.Set(sessUserKey, u.ID)
	_ = sess.Save()
	if tmpl.WantsJSON(c) {
		c.JSON(200, gin.H{"code": 0, "msg": "登录成功", "redirect": tmpl.URLFor("dashboard")})
		return
	}
	c.Redirect(302, tmpl.URLFor("dashboard"))
}

func logout(c *gin.Context) {
	sess := sessions.Default(c)
	sess.Delete(sessUserKey)
	_ = sess.Save()
	c.Redirect(302, tmpl.URLFor("login"))
}
