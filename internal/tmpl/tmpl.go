package tmpl

import (
	"bytes"
	"crypto/rand"
	"encoding/hex"
	"fmt"
	"html/template"
	"net/http"
	"os"
	"path/filepath"
	"strings"
	"sync"
	"time"

	"laycms/internal/config"
	"laycms/internal/menu"
	"laycms/internal/models"

	"github.com/gin-contrib/sessions"
	"github.com/gin-gonic/gin"
	"gorm.io/gorm"
)

type Page struct {
	Title           string
	CmsUser         *models.CmsUser
	DB              *gorm.DB
	Body            template.HTML
	Scripts         template.HTML
	FlashSuccess    string
	FlashErrors     []string
	MenuFormGroups  []menu.Group
	CurrentTable    string
	CSRFToken       string
	Data            map[string]any
}

func (p *Page) Can(tbl, act string) bool {
	if p.CmsUser == nil || p.DB == nil {
		return false
	}
	return p.CmsUser.HasPermission(p.DB, tbl, act)
}

func (p *Page) CanTable(tbl, act string) bool {
	if p.CmsUser == nil || p.DB == nil {
		return false
	}
	return p.CmsUser.CanAccessTable(p.DB, tbl, act)
}

func (p *Page) Year() int {
	return time.Now().Year()
}

var (
	layoutTmpl *template.Template
	fragMu     sync.Mutex
	frags      = map[string]*template.Template{}
)

func Init() error {
	p := filepath.Join(config.TmplDir, "layout_admin.html")
	b, err := os.ReadFile(p)
	if err != nil {
		return err
	}
	layoutTmpl = template.Must(template.New("layout_admin.html").Funcs(funcMap()).Parse(string(b)))
	return nil
}

func getFrag(name string) (*template.Template, error) {
	fragMu.Lock()
	defer fragMu.Unlock()
	if !config.Debug {
		if t, ok := frags[name]; ok {
			return t, nil
		}
	}
	p := filepath.Join(config.TmplDir, "fragments", name+".html")
	b, err := os.ReadFile(p)
	if err != nil {
		return nil, err
	}
	t, err := template.New(name + ".html").Funcs(funcMap()).Parse(string(b))
	if err != nil {
		return nil, err
	}
	if !config.Debug {
		frags[name] = t
	}
	return t, nil
}

func URLFor(name string, kv ...string) string {
	m := routes()
	path := m[name]
	if path == "" {
		return "/"
	}
	for i := 0; i+1 < len(kv); i += 2 {
		path = strings.ReplaceAll(path, "{"+kv[i]+"}", kv[i+1])
	}
	return path
}

func routes() map[string]string {
	return map[string]string{
		"login":               "/login",
		"logout":              "/logout",
		"dashboard":           "/",
		"form_groups_index":   "/form-groups",
		"form_groups_destroy": "/form-groups/{group_id}",
		"forms_index":         "/forms",
		"forms_create":        "/forms/create",
		"forms_edit":          "/forms/{id}",
		"form_fields_index":   "/form-fields/{form_id}",
		"table_data_index":    "/table-data/{table_name}",
		"table_data_create":   "/table-data/{table_name}/create",
		"table_data_edit":     "/table-data/{table_name}/{id}/edit",
		"users_index":         "/users",
		"user_groups_index":   "/user-groups",
		"permissions_edit":    "/permissions/{group_id}",
		"no_permission":       "/no-permission",
	}
}

func Asset(path string) string {
	if strings.HasPrefix(path, "http://") || strings.HasPrefix(path, "https://") {
		return path
	}
	return "/" + strings.TrimPrefix(path, "/")
}

func CSRF(sess sessions.Session) string {
	const k = "csrf_token"
	v := sess.Get(k)
	if s, ok := v.(string); ok && s != "" {
		return s
	}
	b := make([]byte, 16)
	_, _ = rand.Read(b)
	s := hex.EncodeToString(b)
	sess.Set(k, s)
	_ = sess.Save()
	return s
}

func FlashPop(sess sessions.Session, key string) string {
	v := sess.Get(key)
	sess.Delete(key)
	_ = sess.Save()
	if s, ok := v.(string); ok {
		return s
	}
	return ""
}

func FlashPopErrors(sess sessions.Session) []string {
	v := sess.Get("flash_errors")
	sess.Delete("flash_errors")
	_ = sess.Save()
	if a, ok := v.([]string); ok {
		return a
	}
	return nil
}

func BuildPage(c *gin.Context, db *gorm.DB, u *models.CmsUser, title, currentTable string) *Page {
	sess := sessions.Default(c)
	p := &Page{
		Title:          title,
		CmsUser:        u,
		DB:             db,
		MenuFormGroups: menu.FormGroupsForMenu(db, u),
		CurrentTable:   currentTable,
		CSRFToken:      CSRF(sess),
		Data:           map[string]any{},
	}
	if u != nil {
		p.FlashSuccess = FlashPop(sess, "flash_success")
		p.FlashErrors = FlashPopErrors(sess)
	}
	return p
}

func RenderLayout(c *gin.Context, page *Page) error {
	if layoutTmpl == nil {
		return fmt.Errorf("layout not initialized")
	}
	c.Header("Content-Type", "text/html; charset=utf-8")
	return layoutTmpl.Execute(c.Writer, page)
}

func RenderFragment(c *gin.Context, fragName string, page *Page) error {
	return RenderFragmentWith(c, fragName, page, page)
}

// RenderFragmentWith executes the named fragment with execData (often embeds *Page), then renders layout with layoutPage.
func RenderFragmentWith(c *gin.Context, fragName string, execData any, layoutPage *Page) error {
	t, err := getFrag(fragName)
	if err != nil {
		return err
	}
	var buf bytes.Buffer
	if err := t.Execute(&buf, execData); err != nil {
		return err
	}
	layoutPage.Body = template.HTML(buf.String())
	return RenderLayout(c, layoutPage)
}

func RenderStandalone(c *gin.Context, fragName string, data any) error {
	t, err := getFrag(fragName)
	if err != nil {
		return err
	}
	c.Header("Content-Type", "text/html; charset=utf-8")
	return t.Execute(c.Writer, data)
}

func Redirect(c *gin.Context, url string) {
	c.Redirect(http.StatusFound, url)
}

func WantsJSON(c *gin.Context) bool {
	accept := c.GetHeader("Accept")
	return strings.Contains(accept, "application/json") || c.GetHeader("X-Requested-With") == "XMLHttpRequest"
}
