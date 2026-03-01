<?php

namespace Database\Seeders;

use App\Models\CmsUser;
use App\Models\Form;
use App\Models\FormField;
use App\Models\GroupPermission;
use App\Models\UserGroup;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $adminGroup = UserGroup::create([
            'name' => '超级管理员组',
            'description' => '拥有所有权限',
        ]);

        CmsUser::create([
            'username' => 'admin',
            'password' => 'admin123',
            'nickname' => '系统管理员',
            'user_group_id' => $adminGroup->id,
            'is_root' => true,
            'is_active' => true,
        ]);

        $form = Form::create([
            'name' => '示例文章',
            'table_name' => 'sample_articles',
            'description' => '文章管理示例表单',
            'sort_order' => 1,
        ]);

        $fields = [
            ['form_id' => $form->id, 'field_name' => 'title', 'label' => '标题', 'form_control' => 'input', 'sort_order' => 1, 'is_required' => true],
            ['form_id' => $form->id, 'field_name' => 'content', 'label' => '内容', 'form_control' => 'textarea', 'sort_order' => 2],
            ['form_id' => $form->id, 'field_name' => 'author', 'label' => '作者', 'form_control' => 'input', 'sort_order' => 3],
            ['form_id' => $form->id, 'field_name' => 'publish_date', 'label' => '发布日期', 'form_control' => 'date', 'sort_order' => 4],
            ['form_id' => $form->id, 'field_name' => 'status', 'label' => '状态', 'form_control' => 'radio', 'options' => json_encode(['1' => '启用', '0' => '禁用']), 'sort_order' => 5],
        ];
        foreach ($fields as $field) {
            FormField::create($field);
        }

        GroupPermission::create([
            'user_group_id' => $adminGroup->id,
            'table_name' => '_forms',
            'can_create' => true, 'can_read' => true, 'can_update' => true, 'can_delete' => true,
        ]);
        GroupPermission::create([
            'user_group_id' => $adminGroup->id,
            'table_name' => '_users',
            'can_create' => true, 'can_read' => true, 'can_update' => true, 'can_delete' => true,
        ]);
        GroupPermission::create([
            'user_group_id' => $adminGroup->id,
            'table_name' => 'sample_articles',
            'can_create' => true, 'can_read' => true, 'can_update' => true, 'can_delete' => true,
        ]);
    }
}
