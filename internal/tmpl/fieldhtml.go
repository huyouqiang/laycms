package tmpl

import (
	"encoding/json"
	"fmt"
	"html/template"
	"strings"

	"laycms/internal/models"
)

func funcMap() template.FuncMap {
	fm := template.FuncMap{
		"urlFor": URLFor,
		"asset":  Asset,
		"renderField": func(f models.FormField, row map[string]any) template.HTML {
			return template.HTML(FieldControlHTML(f, row))
		},
		"fieldValue": func(row map[string]any, name string) any {
			if row == nil {
				return nil
			}
			return row[name]
		},
		"toJSON": func(v any) template.JS {
			b, _ := json.Marshal(v)
			return template.JS(b)
		},
		"optsJSON": func(f models.FormField) template.JS {
			m := f.OptionsMap()
			b, _ := json.Marshal(m)
			return template.JS(b)
		},
	}
	return fm
}

func FieldControlHTML(f models.FormField, row map[string]any) string {
	var val any
	if row != nil {
		val = row[f.FieldName]
	}
	switch f.FormControl {
	case "textarea":
		s := strVal(val)
		req := reqAttr(f.IsRequired)
		return fmt.Sprintf(`<textarea name="%s" class="layui-textarea" placeholder="%s" %s>%s</textarea>`,
			esc(f.FieldName), esc(f.Label), req, template.HTMLEscapeString(s))
	case "number":
		s := strVal(val)
		req := reqAttr(f.IsRequired)
		return fmt.Sprintf(`<input type="number" name="%s" value="%s" class="layui-input" placeholder="%s" %s>`,
			esc(f.FieldName), esc(s), esc(f.Label), req)
	case "date":
		s := strVal(val)
		req := reqAttr(f.IsRequired)
		return fmt.Sprintf(`<input type="date" name="%s" value="%s" class="layui-input" %s>`,
			esc(f.FieldName), esc(s), req)
	case "datetime":
		s := strVal(val)
		if len(s) > 10 {
			s = strings.ReplaceAll(s[:19], " ", "T")
		}
		req := reqAttr(f.IsRequired)
		return fmt.Sprintf(`<input type="datetime-local" name="%s" value="%s" class="layui-input" step="1" %s>`,
			esc(f.FieldName), esc(s), req)
	case "select":
		var b strings.Builder
		b.WriteString(fmt.Sprintf(`<select name="%s" class="layui-input" %s><option value="">请选择</option>`, esc(f.FieldName), reqAttr(f.IsRequired)))
		for k, v := range f.OptionsMap() {
			sel := ""
			if strVal(val) == k {
				sel = ` selected`
			}
			b.WriteString(fmt.Sprintf(`<option value="%s"%s>%s</option>`, esc(k), sel, esc(v)))
		}
		b.WriteString(`</select>`)
		return b.String()
	case "radio":
		var b strings.Builder
		sv := strVal(val)
		for k, v := range f.OptionsMap() {
			chk := ""
			if sv == k {
				chk = ` checked`
			}
			id := fmt.Sprintf("r_%s_%s", f.FieldName, k)
			b.WriteString(fmt.Sprintf(`<input type="radio" name="%s" value="%s" title="%s" id="%s"%s>`, esc(f.FieldName), esc(k), esc(v), esc(id), chk))
		}
		return `<div class="layui-form-item">` + b.String() + `</div>`
	case "checkbox":
		arr := fromJSONArr(val)
		set := map[string]struct{}{}
		for _, x := range arr {
			set[strVal(x)] = struct{}{}
		}
		var b strings.Builder
		for k, v := range f.OptionsMap() {
			chk := ""
			if _, ok := set[k]; ok {
				chk = ` checked`
			}
			b.WriteString(fmt.Sprintf(`<input type="checkbox" name="%s[]" value="%s" title="%s" lay-skin="primary"%s> `,
				esc(f.FieldName), esc(k), esc(v), chk))
		}
		return b.String()
	case "editor":
		s := strVal(val)
		req := reqAttr(f.IsRequired)
		return fmt.Sprintf(`<textarea name="%s" id="ckeditor-%s" class="layui-textarea ckeditor-field" style="min-height:200px" %s>%s</textarea>`,
			esc(f.FieldName), esc(f.FieldName), req, template.HTMLEscapeString(s))
	case "file":
		s := strVal(val)
		disp := "未上传"
		if s != "" {
			disp = fmt.Sprintf(`<a href="%s" target="_blank">%s</a>`, esc(Asset(s)), esc(s))
		}
		req := reqAttr(f.IsRequired)
		return fmt.Sprintf(`<div class="file-upload-wrap layui-form-item" data-name="%s"><input type="hidden" name="%s" value="%s" class="file-path-input" %s>`+
			`<button type="button" class="layui-btn layui-btn-primary layui-btn-sm file-btn">选择文件</button>`+
			`<input type="file" class="file-upload-input" style="display:none" accept="*/*">`+
			`<span class="file-path-display layui-word-aux" style="margin-left:8px">%s</span> `+
			`<a href="javascript:;" class="file-clear-link">清除</a></div>`,
			esc(f.FieldName), esc(f.FieldName), esc(s), req, disp)
	case "relation":
		s := strVal(val)
		var relTable, refCol string
		if f.Relation != nil && f.Relation.RelatedForm != nil {
			relTable = f.Relation.RelatedForm.DataTable
			refCol = f.Relation.RelatedFieldName
		}
		if refCol == "" {
			refCol = "id"
		}
		req := reqAttr(f.IsRequired)
		return fmt.Sprintf(
			`<div class="relation-autocomplete" data-table="%s" data-ref="%s" data-display="name" data-name="%s">`+
				`<input type="hidden" name="%s" value="%s" class="relation-value" %s>`+
				`<input type="text" class="layui-input relation-input" placeholder="输入搜索" autocomplete="off">`+
				`<div class="relation-dropdown layui-menu" style="display:none;max-height:200px;overflow:auto"></div></div>`,
			esc(relTable), esc(refCol), esc(f.FieldName), esc(f.FieldName), esc(s), req)
	default:
		s := strVal(val)
		req := reqAttr(f.IsRequired)
		return fmt.Sprintf(`<input type="text" name="%s" value="%s" class="layui-input" placeholder="%s" %s>`,
			esc(f.FieldName), esc(s), esc(f.Label), req)
	}
}

func reqAttr(req bool) string {
	if req {
		return `lay-verify="required"`
	}
	return ""
}

func esc(s string) string {
	return template.HTMLEscapeString(s)
}

func strVal(v any) string {
	if v == nil {
		return ""
	}
	switch t := v.(type) {
	case []byte:
		return string(t)
	case string:
		return t
	default:
		return fmt.Sprint(t)
	}
}

func fromJSONArr(v any) []any {
	if v == nil {
		return nil
	}
	if s, ok := v.(string); ok {
		var a []any
		_ = json.Unmarshal([]byte(s), &a)
		return a
	}
	if b, ok := v.([]byte); ok {
		var a []any
		_ = json.Unmarshal(b, &a)
		return a
	}
	return nil
}
