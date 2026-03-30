package handlers

import (
	"os"
	"laycms/internal/config"

	"github.com/gin-contrib/sessions"
	"github.com/gin-contrib/sessions/cookie"
	"github.com/gin-gonic/gin"
)

func Register(r *gin.Engine) {
	store := cookie.NewStore([]byte(config.SecretKey))
	r.Use(sessions.Sessions("laycms_session", store))
	r.Use(func(c *gin.Context) {
		loadUserIntoContext(c)
		c.Next()
	})

	r.Static("/upload", config.UploadDir)
	r.Static("/css", config.StaticDir+"/css")
	if exists(config.StaticDir + "/js") {
		r.Static("/js", config.StaticDir+"/js")
	}

	r.GET("/login", showLogin)
	r.POST("/login", postLogin)
	r.GET("/logout", logout)
	r.GET("/no-permission", noPermissionPage)

	r.GET("/", EnsureUser(), dashboardPage)

	fg := r.Group("/form-groups", requirePermission("forms", "read"))
	fg.GET("/", formGroupsIndex)
	fg.POST("/", requirePermission("forms", "create"), formGroupsStore)
	fg.PUT("/:group_id", requirePermission("forms", "update"), formGroupsUpdate)
	fg.POST("/:group_id", requirePermission("forms", "update"), formGroupsUpdate)
	fg.DELETE("/", requirePermission("forms", "delete"), formGroupsDeleteNoID)
	fg.DELETE("/:group_id", requirePermission("forms", "delete"), formGroupsDestroy)

	fo := r.Group("/forms", requirePermission("forms", "read"))
	fo.GET("/", formsIndex)
	fo.GET("/create", requirePermission("forms", "create"), formsCreatePage)
	fo.POST("/", requirePermission("forms", "create"), formsStore)
	fo.GET("/:id/related-columns", formsRelatedColumns)
	fo.GET("/:id", formsEditPage)
	fo.PUT("/:id", requirePermission("forms", "update"), formsUpdate)
	fo.POST("/:id/add-index", requirePermission("forms", "update"), formsAddIndex)
	fo.DELETE("/:id/drop-index", requirePermission("forms", "update"), formsDropIndex)
	fo.DELETE("/:id", requirePermission("forms", "delete"), formsDestroy)

	ff := r.Group("/form-fields", requirePermission("forms", "read"))
	ff.GET("/:form_id", formFieldsIndex)
	ff.POST("/", requirePermission("forms", "update"), formFieldsStore)
	ff.PUT("/field/:field_id", requirePermission("forms", "update"), formFieldsUpdate)
	ff.DELETE("/field/:field_id", requirePermission("forms", "delete"), formFieldsDestroy)

	fr := r.Group("/form-relations")
	fr.POST("/", requirePermission("forms", "update"), formRelationsStore)
	fr.DELETE("/:relation_id", requirePermission("forms", "update"), formRelationsDestroy)
	fr.GET("/:form_id/related-columns", requirePermission("forms", "read"), formRelatedColumnsByForm)

	td := r.Group("/table-data")
	td.GET("/relation-options", EnsureUser(), tableRelationOptions)
	td.GET("/:table_name/create", requireTablePermission("create"), tableDataCreatePage)
	td.GET("/:table_name/:id/edit", requireTablePermission("update"), tableDataEditPage)
	td.GET("/:table_name", requireTablePermission("read"), tableDataIndex)
	td.POST("/:table_name", requireTablePermission("create"), tableDataStore)
	td.PUT("/:table_name", requireTablePermission("create"), tableDataStore)
	td.POST("/:table_name/:id", requireTablePermission("update"), tableDataUpdate)
	td.PUT("/:table_name/:id", requireTablePermission("update"), tableDataUpdate)
	td.DELETE("/:table_name/:id", requireTablePermission("delete"), tableDataDestroy)
	td.DELETE("/:table_name", requireTablePermission("delete"), tableDataDestroyNoID)

	ug := r.Group("/user-groups", requirePermission("users", "read"))
	ug.GET("/", userGroupsIndex)
	ug.POST("/", requirePermission("users", "create"), userGroupsStore)
	ug.PUT("/:group_id", requirePermission("users", "update"), userGroupsUpdate)
	ug.DELETE("/:group_id", requirePermission("users", "delete"), userGroupsDestroy)

	us := r.Group("/users", requirePermission("users", "read"))
	us.GET("/", usersIndex)
	us.POST("/", requirePermission("users", "create"), usersStore)
	us.PUT("/:user_id", requirePermission("users", "update"), usersUpdate)
	us.DELETE("/:user_id", requirePermission("users", "delete"), usersDestroy)

	pm := r.Group("/permissions", requirePermission("users", "read"))
	pm.GET("/:group_id", permissionsEditPage)
	pm.PUT("/:group_id", requirePermission("users", "update"), permissionsUpdate)

	api := r.Group("/api", EnsureUser())
	api.POST("/upload", apiUpload)
}

func exists(p string) bool {
	_, err := os.Stat(p)
	return err == nil
}
