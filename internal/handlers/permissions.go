package handlers

import (
	"laycms/internal/models"
	"laycms/internal/tmpl"
	"strings"

	"github.com/gin-gonic/gin"
)

type tablePerm struct {
	Name  string
	Label string
}

func permissionsEditPage(c *gin.Context) {
	u := CurrentUser(c)
	id := atoi(c.Param("group_id"), 0)
	var g models.UserGroup
	if err := gormDB().Preload("Permissions").First(&g, id).Error; err != nil {
		c.Status(404)
		return
	}
	var forms []models.Form
	gormDB().Order("sort_order ASC").Find(&forms)
	tables := []tablePerm{
		{"_forms", "表单管理"},
		{"_users", "用户管理"},
	}
	for _, f := range forms {
		tables = append(tables, tablePerm{f.DataTable, f.Name})
	}
	permMap := map[string]*models.GroupPermission{}
	for i := range g.Permissions {
		p := &g.Permissions[i]
		permMap[p.ResourceTable] = p
	}
	page := tmpl.BuildPage(c, gormDB(), u, "权限配置 - "+g.Name, "")
	type frag struct {
		*tmpl.Page
		Group   models.UserGroup
		Tables  []tablePerm
		Perms   map[string]*models.GroupPermission
	}
	_ = tmpl.RenderFragmentWith(c, "permissions_edit", &frag{page, g, tables, permMap}, page)
}

func permissionsUpdate(c *gin.Context) {
	id := atoi(c.Param("group_id"), 0)
	var g models.UserGroup
	if err := gormDB().First(&g, id).Error; err != nil {
		c.Status(404)
		return
	}
	_ = c.Request.ParseForm()
	submitted := parsePermissionsForm(c.Request.PostForm)
	var forms []models.Form
	gormDB().Order("sort_order ASC").Find(&forms)
	allTables := map[string]struct{}{"_forms": {}, "_users": {}}
	for _, f := range forms {
		allTables[f.DataTable] = struct{}{}
	}
	for tn := range allTables {
		actions := submitted[tn]
		var perm models.GroupPermission
		q := gormDB().Where("user_group_id = ? AND table_name = ?", id, tn)
		if err := q.First(&perm).Error; err != nil {
			perm = models.GroupPermission{
				UserGroupID: int64(id), ResourceTable: tn,
				CanCreate: actions["create"],
				CanRead:   actions["read"],
				CanUpdate: actions["update"],
				CanDelete: actions["delete"],
			}
			gormDB().Create(&perm)
			continue
		}
		perm.CanCreate = actions["create"]
		perm.CanRead = actions["read"]
		perm.CanUpdate = actions["update"]
		perm.CanDelete = actions["delete"]
		gormDB().Save(&perm)
	}
	c.JSON(200, gin.H{"code": 0, "msg": "权限保存成功"})
}

func parsePermissionsForm(vs map[string][]string) map[string]map[string]bool {
	out := map[string]map[string]bool{}
	prefix := "permissions["
	for key, vals := range vs {
		if !strings.HasPrefix(key, prefix) || !strings.HasSuffix(key, "]") {
			continue
		}
		inner := key[len(prefix) : len(key)-1]
		parts := strings.Split(inner, "][")
		if len(parts) != 2 {
			continue
		}
		table, action := parts[0], parts[1]
		if out[table] == nil {
			out[table] = map[string]bool{}
		}
		val := ""
		if len(vals) > 0 {
			val = vals[len(vals)-1]
		}
		out[table][action] = truthy(val)
	}
	return out
}

func truthy(v string) bool {
	return v != "" && v != "0" && v != "false"
}
