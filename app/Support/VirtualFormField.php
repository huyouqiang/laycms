<?php

namespace App\Support;

/**
 * 用于 table-data 虚拟表（如 users）的字段描述，兼容 FormField 的列表与选项接口.
 */
class VirtualFormField
{
    public string $field_name;

    public string $label;

    public bool $is_list_visible;

    public string $form_control;

    /** @var array<string, string> */
    public array $options;

    public function __construct(string $field_name, string $label, bool $is_list_visible, string $form_control = 'input', array $options = [])
    {
        $this->field_name = $field_name;
        $this->label = $label;
        $this->is_list_visible = $is_list_visible;
        $this->form_control = $form_control;
        $this->options = $options;
    }

    /** @return array<string, string> */
    public function getOptionsArray(): array
    {
        return $this->options;
    }
}
