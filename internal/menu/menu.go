package menu

import (
	"laycms/internal/models"

	"gorm.io/gorm"
)

type Group struct {
	ID    int64
	Name  string
	Forms []models.Form
}

func FormGroupsForMenu(db *gorm.DB, u *models.CmsUser) []Group {
	if u == nil {
		return nil
	}
	var groups []models.FormGroup
	if err := db.Preload("Forms", func(db *gorm.DB) *gorm.DB {
		return db.Order("sort_order ASC")
	}).Order("sort_order ASC, id ASC").Find(&groups).Error; err != nil {
		return nil
	}
	var out []Group
	for _, g := range groups {
		var visible []models.Form
		for _, f := range g.Forms {
			if u.IsRoot || u.CanAccessTable(db, f.DataTable, "read") {
				visible = append(visible, f)
			}
		}
		if len(visible) > 0 {
			out = append(out, Group{ID: g.ID, Name: g.Name, Forms: visible})
		}
	}
	return out
}
