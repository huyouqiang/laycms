package config

import (
	"os"
	"path/filepath"
	"strconv"

	"github.com/joho/godotenv"
)

var (
	AppName   string
	Debug     bool
	SecretKey string
	DSN       string
	BaseDir   string
	UploadDir string
	StaticDir string
	TmplDir   string
)

func Load() {
	_ = godotenv.Load()
	wd, _ := os.Getwd()
	BaseDir = wd

	AppName = getenv("APP_NAME", "LayCMS")
	Debug = getenv("APP_DEBUG", "true") == "true"
	SecretKey = getenv("SECRET_KEY", "laycms-secret-key-change-in-production")

	host := getenv("DB_HOST", "127.0.0.1")
	port := getenv("DB_PORT", "3306")
	dbname := getenv("DB_DATABASE", "laycms")
	user := getenv("DB_USERNAME", "root")
	pass := getenv("DB_PASSWORD", "")
	DSN = user + ":" + pass + "@tcp(" + host + ":" + port + ")/" + dbname + "?charset=utf8mb4&parseTime=True&loc=Local"

	UploadDir = filepath.Join(BaseDir, "public", "upload")
	StaticDir = filepath.Join(BaseDir, "public")
	TmplDir = filepath.Join(BaseDir, "templates")
}

func getenv(k, def string) string {
	if v := os.Getenv(k); v != "" {
		return v
	}
	return def
}

func GetInt(k string, def int) int {
	v := os.Getenv(k)
	if v == "" {
		return def
	}
	n, err := strconv.Atoi(v)
	if err != nil {
		return def
	}
	return n
}
