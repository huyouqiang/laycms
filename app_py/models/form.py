from sqlalchemy import Column, BigInteger, String, Boolean, ForeignKey, SmallInteger, Text
from sqlalchemy.orm import relationship

from app_py.models.base import Base


class FormGroup(Base):
    __tablename__ = "cms_form_groups"

    id = Column(BigInteger, primary_key=True, autoincrement=True)
    name = Column(String(50), nullable=False)
    sort_order = Column(SmallInteger, default=0)

    forms = relationship("Form", back_populates="form_group", order_by="Form.sort_order")


class Form(Base):
    __tablename__ = "cms_forms"

    id = Column(BigInteger, primary_key=True, autoincrement=True)
    name = Column(String(100), nullable=False)
    table_name = Column(String(100), nullable=False, unique=True)
    description = Column(String(255), nullable=True)
    sort_order = Column(SmallInteger, default=0)
    form_group_id = Column(BigInteger, ForeignKey("cms_form_groups.id"), nullable=False)

    form_group = relationship("FormGroup", back_populates="forms")
    fields = relationship("FormField", back_populates="form", order_by="FormField.sort_order", cascade="all, delete-orphan")
    relations = relationship(
        "FormRelation",
        back_populates="form",
        foreign_keys="FormRelation.form_id",
        cascade="all, delete-orphan",
    )


class FormField(Base):
    __tablename__ = "cms_form_fields"

    id = Column(BigInteger, primary_key=True, autoincrement=True)
    form_id = Column(BigInteger, ForeignKey("cms_forms.id"), nullable=False)
    field_name = Column(String(64), nullable=False)
    label = Column(String(100), nullable=False)
    form_control = Column(String(50), default="input")
    options = Column(Text, nullable=True)
    attributes = Column(Text, nullable=True)
    validation_rules = Column(String(255), nullable=True)
    sort_order = Column(SmallInteger, default=0)
    is_required = Column(Boolean, default=False)
    is_list_visible = Column(Boolean, default=True)

    form = relationship("Form", back_populates="fields")
    relation = relationship("FormRelation", back_populates="form_field", uselist=False, cascade="all, delete-orphan")

    def get_options_array(self):
        import json
        if not self.options:
            return {}
        try:
            d = json.loads(self.options)
            return d if isinstance(d, dict) else {}
        except Exception:
            return {}


class FormRelation(Base):
    __tablename__ = "cms_form_relations"

    id = Column(BigInteger, primary_key=True, autoincrement=True)
    form_id = Column(BigInteger, ForeignKey("cms_forms.id"), nullable=False)
    form_field_id = Column(BigInteger, ForeignKey("cms_form_fields.id"), nullable=False)
    related_form_id = Column(BigInteger, ForeignKey("cms_forms.id"), nullable=False)
    related_field_name = Column(String(64), default="id")

    form = relationship("Form", back_populates="relations", foreign_keys=[form_id])
    form_field = relationship("FormField", back_populates="relation")
    related_form = relationship("Form", foreign_keys=[related_form_id])
