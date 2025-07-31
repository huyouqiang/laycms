<?php

namespace App\Controllers;
use CodeIgniter\Files\File;

class Model extends BaseController
{
  public function index()
  {
    return view('index', ['menus' => json_decode($this->cache->get('models'),true)]);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function data($modelName)
  {
    $get = $this->get;
    if ( isset($get['page']) ) {
      $page = isset($get['page']) ? (($get['page']-1)*$get['limit']):0;
      $rowNum = $this->db->query("select count(id) as rowNum from ".$modelName)->getRowArray();
      $data = $this->db->query("select * from ".$modelName." order by id desc limit {$page},{$get['limit']}")->getResultArray();
      $res = ['code' => '0', 'msg' => '模型数据', 'count' => $rowNum['rowNum'], 'data' => $data];
      return $this->response->setJSON($res);
    }

    $fieldArr = $this->modelFields($modelName);
    $fieldArr[] = ['fixed' => 'right', 'title' => '操作', 'width' => '120', 'templet' => '#toolDemo'];
    $fieldJson = json_encode($fieldArr);
    return view('model_data', ['menus' => json_decode($this->cache->get('models'),true), 'modelName' => $modelName, 'fieldJson' => $fieldJson]);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function rowForm($modelName, $rowId)
  {
    $rowData = $this->rowDetail($modelName, $rowId);
    $fieldArr = $this->modelFields($modelName);
    $fieldForm = $this->fieldForm($fieldArr, $rowData);
    $res = ['code' => '1', 'msg' => $fieldForm];
    return $this->response->setJSON($res);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function rowDel($modelName, $rowId)
  {
    $this->db->table($modelName)->delete(['id' => $rowId]);
    $res = ['code' => '1', 'msg' => '删除记录成功'];
    return $this->response->setJSON($res);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function rowUpdate($modelName, $rowId)
  {
    $post = $this->post;
    unset($post['file']);
    if ($rowId == '0') {
      $this->db->table(''.$modelName)->insert($post);
      $res = ['code' => '1', 'msg' => '添加成功'];
    }
    else {
      $this->db->table(''.$modelName)->where(['id' => $rowId])->update($post);
      $res = ['code' => '1', 'msg' => '更新成功'];
    }
    return $this->response->setJSON($res);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function settings()
  {
    return view('model_list', ['menus' => json_decode($this->cache->get('models'), true)]);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function modelJson()
  {
    $post = $this->post;
    $this->cache->save('models', $post['modelJson'], 60*60*24*365*10);
    $res = ['code' => '1', 'msg' => '保存成功'];
    return $this->response->setJSON($res);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function uploadFile()
  {

    $path = getcwd().'/uploads/'.date('Ymd',time()).'/';
    $fileName = substr(md5(date('Ymd')),0,16);
    $fileType = substr($_FILES['file']['name'], strrpos($_FILES['file']['name'],'.'));
    if(!file_exists($path)){
      mkdir($path, 0777, true);
    }
    $res = move_uploaded_file($_FILES['file']['tmp_name'], $path.$fileName.$fileType);
    if($res) {
      $res = ['code' => '1', 'msg' => '/uploads/'.date('Ymd',time()).'/'.$fileName.$fileType];
    }
    else {
      $res = ['code' => '1', 'msg' => '上传失败'];
    }

    return $this->response->setJSON($res);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  private function fieldForm($fieldArr, $rowData = []):string
  {
    $form = '';
    foreach ($fieldArr as $key => $value) {

      $fieldType = substr($value['COLUMN_COMMENT'], strpos($value['COLUMN_COMMENT'], '[')+1, strpos($value['COLUMN_COMMENT'], ']')-(strpos($value['COLUMN_COMMENT'], '[')+1));
      $fieldTypeArr = explode('|', $fieldType);
      $inputValue = empty($rowData) ? '':' value="'.$rowData[$value['field']].'"';
//      print_r($inputValue);
//      die();

      switch ($fieldTypeArr['0']) {
        case 'input':
          $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block"><input type="text" name="'.$value['field'].'" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" '.$inputValue.'></div></div>';
          break;
        case 'date':
          $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block"><input type="text" name="'.$value['field'].'" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" '.$inputValue.'></div></div>';
          break;
        case 'float':
          $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block"><input type="text" name="'.$value['field'].'" lay-verify="required" placeholder="请输入" autocomplete="off" class="layui-input" '.$inputValue.'></div></div>';
          break;
        case 'radio':
          $radioArr = explode('&', $fieldTypeArr['1']);
          $optionForm = '';
          foreach ($radioArr as $k => $v) {
            $optionArr = explode('=', $v);
            $isChecked = ($optionArr['0'] == $rowData[$value['field']]) ? ' checked ':'';
            $optionForm .= '<input type="radio" name="'.$value['field'].'" value="'.$optionArr['0'].'" title="'.$optionArr['1'].'" '.$isChecked.'>';
          }
          $form .= '<div class="layui-form-item" pane><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block">'.$optionForm.'</div></div>';
          break;
        case 'file':
          $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block"><div class="layui-btn-group"><button type="button" class="layui-btn layui-btn-sm uploadFile" uploadFieldName="'.$value['field'].'"><i class="layui-icon layui-icon-uploads"></i>文件上传</button><a type="button" class="layui-btn layui-btn-sm" href="'.($rowData[$value['field']] =='' ? 'javascript:;':$rowData[$value['field']]).'" target="_blank" id="'.$value['field'].'_btn">'.($rowData[$value['field']] =='' ? '文件地址':$rowData[$value['field']]).'</a></div><input type="text" name="'.$value['field'].'" placeholder="请输入" autocomplete="off" id="'.$value['field'].'" class="layui-input" style="display:none;" '.$inputValue.'></div></div>';
          break;
        case 'json':
          $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block"><textarea placeholder="请输入" class="layui-textarea" name="'.$value['field'].'" name="'.$value['field'].'" lay-verify="required" placeholder="请输入" autocomplete="off">'.$rowData[$value['field']].'</textarea></div></div>';
          break;
        case 'editor':
          $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block"><textarea placeholder="请输入" class="layui-textarea" name="'.$value['field'].'" name="'.$value['field'].'" lay-verify="required" placeholder="请输入" autocomplete="off" >'.$rowData[$value['field']].'</textarea></div></div>';
          break;
        case 'checkbox':
          $checkboxArr = explode('&', $fieldTypeArr['1']);
          $optionForm = '';
          foreach ($checkboxArr as $k => $v) {
            $optionArr = explode('=', $v);
            $checkboxValueArr = explode(',', $rowData[$value['field']]);
//                        print_r($optionArr);
//            print_r($rowData[$value['field']]);
//            die();
            $isChecked = (in_array($optionArr['0'], $checkboxValueArr)) ? ' checked ':'';
            $optionForm .= '<input type="checkbox" name="'.$value['field'].'[]" value="'.$optionArr['0'].'" title="'.$optionArr['1'].'" checkboxName="'.$value['field'].'"'.$isChecked.'>';
          }
          $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block">'.$optionForm.'</div></div>';
          break;
        case 'select':
          $selectArr = explode('&', $fieldTypeArr['1']);
          $optionForm = '';
          foreach ($selectArr as $k => $v) {
            $optionArr = explode('=', $v);
            $isChecked = ($optionArr['0'] == $rowData[$value['field']]) ? '':' checked ';
            $optionForm .= '<option name="'.$value['field'].'[]" value="'.$optionArr['0'].'" title="'.$optionArr['1'].'" '.$isChecked.'>'.$optionArr['1'].'</option>';
          }
          $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block"><select name="'.$value['field'].'" value="'.$optionArr['0'].'" title="'.$optionArr['1'].'">'.$optionForm.'</select></div></div>';
          break;
      }
    }

//    print_r($form);
//    die();
    return $form;
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  private function modelFields($modelName)
  {
    $fieldArr = $this->db->query("SELECT  COLUMN_NAME as field,COLUMN_COMMENT,left(COLUMN_COMMENT,locate('[',COLUMN_COMMENT)-1) as title,'100' as width FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'laycms' AND  TABLE_NAME = '{$modelName}' order by ordinal_position asc")->getResultArray();

    return $fieldArr;
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  private function rowDetail($modelName, $rowId)
  {
    $rowDetail = $this->db->query("select * from {$modelName} where id='{$rowId}'")->getRowArray();

    return $rowDetail;
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

}
