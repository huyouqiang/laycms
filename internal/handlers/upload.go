package handlers

import (
	"crypto/rand"
	"encoding/hex"
	"os"
	"path/filepath"
	"time"

	"laycms/internal/config"

	"github.com/gin-gonic/gin"
)

const maxUpload = 10 * 1024 * 1024

func apiUpload(c *gin.Context) {
	fh, err := c.FormFile("file")
	if err != nil {
		c.JSON(400, gin.H{"code": 1, "msg": "请选择文件"})
		return
	}
	if fh.Size > maxUpload {
		c.JSON(400, gin.H{"code": 1, "msg": "文件大小不能超过 10MB"})
		return
	}
	_ = os.MkdirAll(config.UploadDir, 0755)
	ext := filepath.Ext(fh.Filename)
	b := make([]byte, 3)
	_, _ = rand.Read(b)
	name := time.Now().Format("20060102150405") + "_" + hex.EncodeToString(b) + ext
	dst := filepath.Join(config.UploadDir, name)
	if err := c.SaveUploadedFile(fh, dst); err != nil {
		c.JSON(500, gin.H{"code": 1, "msg": err.Error()})
		return
	}
	c.JSON(200, gin.H{"code": 0, "msg": "上传成功", "path": "upload/" + name})
}
