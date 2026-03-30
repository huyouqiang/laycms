package db

import (
	"laycms/internal/config"

	"gorm.io/driver/mysql"
	"gorm.io/gorm"
	"gorm.io/gorm/logger"
)

var ORM *gorm.DB

func Connect() error {
	var err error
	logLevel := logger.Error
	if config.Debug {
		logLevel = logger.Info
	}
	ORM, err = gorm.Open(mysql.Open(config.DSN), &gorm.Config{
		Logger: logger.Default.LogMode(logLevel),
	})
	return err
}
