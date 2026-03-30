package tabledata

import (
	"regexp"
	"strconv"
	"strings"
)

var columnRE = regexp.MustCompile(`^[a-z][a-z0-9_]*$`)

var sqlDangerous = []string{
	";", "--", "*", "/", "\\", "union", "select", "drop", "truncate", "insert", "delete", "update",
	"exec", "alter", "create", "grant", "revoke",
}

func ParseSafeWhere(rawWhere string, allowedColumns []string, args []any) (string, []any) {
	allowed := map[string]struct{}{}
	for _, c := range allowedColumns {
		if columnRE.MatchString(c) {
			allowed[c] = struct{}{}
		}
	}
	raw := strings.TrimSpace(rawWhere)
	lower := strings.ToLower(raw)
	for _, d := range sqlDangerous {
		if strings.Contains(lower, d) {
			return "", args
		}
	}
	if raw == "" {
		return "", args
	}

	re := regexp.MustCompile(`(?i)\s+(and|or)\s+`)
	parts := re.Split(raw, -1)
	logic := re.FindAllStringSubmatch(raw, -1)
	var log []string
	for _, m := range logic {
		if len(m) > 1 {
			log = append(log, strings.ToLower(strings.TrimSpace(m[1])))
		}
	}

	var conds []string
	a := append([]any(nil), args...)
	for _, tok := range parts {
		tok = strings.TrimSpace(tok)
		if tok == "" {
			continue
		}
		frag, na := parseSingleCondition(tok, allowed, a)
		if frag == "" {
			return "", args
		}
		conds = append(conds, frag)
		a = na
	}
	if len(conds) == 0 {
		return "", args
	}
	if len(conds) == 1 {
		return conds[0], a
	}
	var b strings.Builder
	b.WriteString(conds[0])
	for i := 1; i < len(conds); i++ {
		op := "and"
		if i-1 < len(log) {
			op = log[i-1]
		}
		b.WriteString(" ")
		b.WriteString(op)
		b.WriteString(" ")
		b.WriteString(conds[i])
	}
	return b.String(), a
}

var condRE = regexp.MustCompile(`(?i)^\s*([a-z][a-z0-9_]*)\s*(<=|>=|!=|=|like|in|<|>)\s*(.+)$`)

func parseSingleCondition(tok string, allowed map[string]struct{}, args []any) (string, []any) {
	m := condRE.FindStringSubmatch(tok)
	if m == nil {
		return "", args
	}
	col, op, valPart := m[1], strings.ToLower(m[2]), strings.TrimSpace(m[3])
	if _, ok := allowed[col]; !ok {
		return "", args
	}

	if op == "in" {
		inn := regexp.MustCompile(`(?s)^\s*\((.+)\)\s*$`).FindStringSubmatch(valPart)
		if inn == nil {
			return "", args
		}
		items := regexp.MustCompile(`,\s*`).Split(inn[1], -1)
		var ph []string
		for _, it := range items {
			it = strings.TrimSpace(it)
			v, ok := parseLiteral(it)
			if !ok {
				return "", args
			}
			ph = append(ph, "?")
			args = append(args, v)
		}
		return "`" + col + "` IN (" + strings.Join(ph, ", ") + ")", args
	}

	v, ok := parseLiteral(valPart)
	if !ok {
		return "", args
	}
	if op == "like" {
		args = append(args, v)
		return "`" + col + "` LIKE ?", args
	}
	if v == nil {
		if op == "=" {
			return "`" + col + "` IS NULL", args
		}
		if op == "!=" {
			return "`" + col + "` IS NOT NULL", args
		}
		return "", args
	}
	args = append(args, v)
	return "`" + col + "` " + strings.ToUpper(op) + " ?", args
}

func parseLiteral(valPart string) (any, bool) {
	if len(valPart) >= 2 && valPart[0] == '\'' && valPart[len(valPart)-1] == '\'' {
		s := strings.ReplaceAll(valPart[1:len(valPart)-1], "''", "'")
		return s, true
	}
	if len(valPart) >= 2 && valPart[0] == '"' && valPart[len(valPart)-1] == '"' {
		s := strings.ReplaceAll(valPart[1:len(valPart)-1], `""`, `"`)
		return s, true
	}
	if strings.EqualFold(valPart, "NULL") {
		return nil, true
	}
	if n, err := strconv.ParseInt(valPart, 10, 64); err == nil {
		return n, true
	}
	if f, err := strconv.ParseFloat(valPart, 64); err == nil {
		return f, true
	}
	return nil, false
}
