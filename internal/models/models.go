package models

import (
	"strings"

	"golang.org/x/crypto/bcrypt"
	"gorm.io/gorm"
)

type CmsUser struct {
	ID           int64      `gorm:"column:id;primaryKey;autoIncrement"`
	Username     string     `gorm:"column:username;size:64;uniqueIndex;not null"`
	Password     string     `gorm:"column:password;size:255;not null"`
	Nickname     *string    `gorm:"column:nickname;size:64"`
	UserGroupID  *int64     `gorm:"column:user_group_id"`
	IsRoot       bool       `gorm:"column:is_root"`
	IsActive     bool       `gorm:"column:is_active"`
	UserGroup    *UserGroup `gorm:"foreignKey:UserGroupID"`
}

func (CmsUser) TableName() string { return "cms_users" }

func (u *CmsUser) TruncatePassword(raw string) []byte {
	b := []byte(raw)
	if len(b) > 72 {
		return b[:72]
	}
	return b
}

func (u *CmsUser) SetPassword(raw string) error {
	h, err := bcrypt.GenerateFromPassword(u.TruncatePassword(raw), bcrypt.DefaultCost)
	if err != nil {
		return err
	}
	u.Password = string(h)
	return nil
}

func (u *CmsUser) CheckPassword(raw string) bool {
	return bcrypt.CompareHashAndPassword([]byte(u.Password), u.TruncatePassword(raw)) == nil
}

func (u *CmsUser) HasPermission(db *gorm.DB, tableName, action string) bool {
	if u.IsRoot {
		return true
	}
	if u.UserGroupID == nil {
		return false
	}
	var perm GroupPermission
	err := db.Where("user_group_id = ? AND table_name = ?", *u.UserGroupID, tableName).First(&perm).Error
	if err != nil {
		return false
	}
	switch action {
	case "create":
		return perm.CanCreate
	case "read":
		return perm.CanRead
	case "update":
		return perm.CanUpdate
	case "delete":
		return perm.CanDelete
	case "manage":
		return perm.CanRead
	default:
		return false
	}
}

func (u *CmsUser) CanAccessTable(db *gorm.DB, tableName, action string) bool {
	return u.HasPermission(db, tableName, action)
}

type UserGroup struct {
	ID          int64              `gorm:"column:id;primaryKey;autoIncrement"`
	Name        string             `gorm:"column:name;size:64;not null"`
	Description *string            `gorm:"column:description;type:text"`
	Users       []CmsUser          `gorm:"foreignKey:UserGroupID"`
	Permissions []GroupPermission  `gorm:"foreignKey:UserGroupID"`
}

func (UserGroup) TableName() string { return "user_groups" }

type GroupPermission struct {
	ID           int64  `gorm:"column:id;primaryKey;autoIncrement"`
	UserGroupID  int64  `gorm:"column:user_group_id;not null;index"`
	ResourceTable string `gorm:"column:table_name;size:128;not null"`
	CanCreate    bool   `gorm:"column:can_create"`
	CanRead      bool   `gorm:"column:can_read"`
	CanUpdate    bool   `gorm:"column:can_update"`
	CanDelete    bool   `gorm:"column:can_delete"`
}

func (GroupPermission) TableName() string { return "cms_group_permissions" }

type FormGroup struct {
	ID        int64  `gorm:"column:id;primaryKey;autoIncrement"`
	Name      string `gorm:"column:name;size:50;not null"`
	SortOrder int16  `gorm:"column:sort_order"`
	Forms     []Form `gorm:"foreignKey:FormGroupID"`
}

func (FormGroup) TableName() string { return "cms_form_groups" }

type Form struct {
	ID           int64          `gorm:"column:id;primaryKey;autoIncrement"`
	Name         string         `gorm:"column:name;size:100;not null"`
	DataTable    string         `gorm:"column:table_name;size:100;uniqueIndex;not null"`
	Description  *string        `gorm:"column:description;size:255"`
	SortOrder    int16          `gorm:"column:sort_order"`
	FormGroupID  int64          `gorm:"column:form_group_id;not null"`
	FormGroup    *FormGroup     `gorm:"foreignKey:FormGroupID"`
	Fields       []FormField    `gorm:"foreignKey:FormID"`
	Relations    []FormRelation `gorm:"foreignKey:FormID"`
}

func (Form) TableName() string { return "cms_forms" }

type FormField struct {
	ID            int64         `gorm:"column:id;primaryKey;autoIncrement"`
	FormID        int64         `gorm:"column:form_id;not null;index"`
	FieldName     string        `gorm:"column:field_name;size:64;not null"`
	Label         string        `gorm:"column:label;size:100;not null"`
	FormControl   string        `gorm:"column:form_control;size:50"`
	Options       *string       `gorm:"column:options;type:text"`
	Attributes    *string       `gorm:"column:attributes;type:text"`
	ValidationRules *string     `gorm:"column:validation_rules;size:255"`
	SortOrder     int16         `gorm:"column:sort_order"`
	IsRequired    bool          `gorm:"column:is_required"`
	IsListVisible bool          `gorm:"column:is_list_visible"`
	Form          *Form         `gorm:"foreignKey:FormID"`
	Relation      *FormRelation `gorm:"foreignKey:FormFieldID"`
}

func (FormField) TableName() string { return "cms_form_fields" }

func (f *FormField) OptionsMap() map[string]string {
	if f.Options == nil || strings.TrimSpace(*f.Options) == "" {
		return map[string]string{}
	}
	// minimal JSON object parse - use encoding/json
	return parseOptionsJSON(*f.Options)
}

type FormRelation struct {
	ID                 int64      `gorm:"column:id;primaryKey;autoIncrement"`
	FormID             int64      `gorm:"column:form_id;not null"`
	FormFieldID        int64      `gorm:"column:form_field_id;not null"`
	RelatedFormID      int64      `gorm:"column:related_form_id;not null"`
	RelatedFieldName   string     `gorm:"column:related_field_name;size:64"`
	Form               *Form      `gorm:"foreignKey:FormID"`
	FormField          *FormField `gorm:"foreignKey:FormFieldID"`
	RelatedForm        *Form      `gorm:"foreignKey:RelatedFormID"`
}

func (FormRelation) TableName() string { return "cms_form_relations" }
