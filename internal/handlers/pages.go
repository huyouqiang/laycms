package handlers

import (
	"laycms/internal/models"
	"laycms/internal/tmpl"

	"github.com/gin-gonic/gin"
)

func dashboardPage(c *gin.Context) {
	u := requireAuth(c)
	if u == nil {
		return
	}
	var forms []models.Form
	gormDB().Order("sort_order ASC").Find(&forms)
	page := tmpl.BuildPage(c, gormDB(), u, "首页", "")
	type frag struct {
		*tmpl.Page
		Forms []models.Form
	}
	_ = tmpl.RenderFragmentWith(c, "dashboard", &frag{page, forms}, page)
}

func noPermissionPage(c *gin.Context) {
	page := tmpl.BuildPage(c, gormDB(), nil, "无权限", "")
	page.CmsUser = nil
	_ = tmpl.RenderFragment(c, "no_permission", page)
}
