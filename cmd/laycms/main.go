package main

import (
	"log"
	"os"

	"laycms/internal/config"
	"laycms/internal/db"
	"laycms/internal/handlers"
	"laycms/internal/tmpl"

	"github.com/gin-gonic/gin"
)

func main() {
	config.Load()
	if err := os.MkdirAll(config.UploadDir, 0755); err != nil {
		log.Fatal(err)
	}
	if err := db.Connect(); err != nil {
		log.Fatal("database:", err)
	}
	if err := tmpl.Init(); err != nil {
		log.Fatal("templates:", err)
	}
	if !config.Debug {
		gin.SetMode(gin.ReleaseMode)
	}
	r := gin.Default()
	handlers.Register(r)
	addr := ":8000"
	if p := os.Getenv("PORT"); p != "" {
		addr = ":" + p
	}
	log.Println("LayCMS (Gin) listening on http://0.0.0.0" + addr)
	if err := r.Run("0.0.0.0" + addr); err != nil {
		log.Fatal(err)
	}
}
