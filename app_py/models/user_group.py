from sqlalchemy import Column, BigInteger, String, Boolean, ForeignKey, Text
from sqlalchemy.orm import relationship

from app_py.models.base import Base


class UserGroup(Base):
    __tablename__ = "user_groups"

    id = Column(BigInteger, primary_key=True, autoincrement=True)
    name = Column(String(64), nullable=False)
    description = Column(Text, nullable=True)

    users = relationship("CmsUser", back_populates="user_group")
    permissions = relationship("GroupPermission", back_populates="user_group", cascade="all, delete-orphan")


class GroupPermission(Base):
    __tablename__ = "cms_group_permissions"

    id = Column(BigInteger, primary_key=True, autoincrement=True)
    user_group_id = Column(BigInteger, ForeignKey("user_groups.id"), nullable=False)
    table_name = Column(String(128), nullable=False)
    can_create = Column(Boolean, default=False)
    can_read = Column(Boolean, default=False)
    can_update = Column(Boolean, default=False)
    can_delete = Column(Boolean, default=False)

    user_group = relationship("UserGroup", back_populates="permissions")
