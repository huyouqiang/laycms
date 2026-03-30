package models

import (
	"encoding/json"
	"fmt"
)

func parseOptionsJSON(s string) map[string]string {
	out := make(map[string]string)
	var raw map[string]interface{}
	if err := json.Unmarshal([]byte(s), &raw); err != nil {
		return out
	}
	for k, v := range raw {
		out[k] = fmt.Sprint(v)
	}
	return out
}
