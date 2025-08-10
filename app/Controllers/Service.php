<?php

namespace App\Controllers;
use CodeIgniter\Files\File;

class Service extends BaseController
{
  public function index()
  {
    $this->checkLogin();

    $res = $this->staticData();

    $this->accessLog($res);
    return view('index', $res);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function data($modelName)
  {
    $this->checkLogin();
    $get = $this->get;
    $res = $this->staticData();

    if ( isset($get['page']) ) {
      $page = isset($get['page']) ? (($get['page']-1)*$get['limit']):0;
      $fieldArr = $this->_modelFields($modelName);
      $where = " ";
      if ( isset($get['pid']) ) {
        foreach ($fieldArr as $k => $v) {
          if ($v['priTab'] != '') {
            $where = ' where ' . $v['field'] . ' = ' . $get['pid'] . ' ';
          }
        }
      }

      if (sizeof($get) > 2 && !isset($get['pid'])) {
        $search = $get;
        unset($search['page'], $search['limit'], $search['t']);
        foreach ($search as $k => $v) {
          if (empty($v)) {
            unset($search[$k]);
          }
        }
        $keys = array_keys($search);
        $values = array_values($search);
        $where .= ' where ';
        foreach ($keys as $k => $v) {


          if (is_array($values[$k])) {
            $where .= "$keys[$k] like '%".implode(',', $values[$k])."%'";
          }
          else {
            $where .= "$keys[$k] like '%".$values[$k]."%'";
          }

          if (sizeof($values) != ($k + 1)) {
            $where .= " and ";
          }

        }
//        print_r($where);
//        die();
      }

      if (sizeof($get) > 4 && isset($get['pid'])) {
        $search = $get;
        unset($search['page'], $search['limit'], $search['t'], $search['pid']);
        foreach ($search as $k => $v) {
          if (empty($v)) {
            unset($search[$k]);
          }
        }
        $keys = array_keys($search);
        $values = array_values($search);
        $where .= ' and ';
        foreach ($keys as $k => $v) {


          if (is_array($values[$k])) {
            $where .= "$keys[$k] like '%".implode(',', $values[$k])."%'";
          }
          else {
            $where .= "$keys[$k] like '%".$values[$k]."%'";
          }

          if (sizeof($values) != ($k + 1)) {
            $where .= " and ";
          }

        }
//        print_r($where);
//        die();
      }

      $rowNum = $this->db->query("select count(id) as rowNum from ".$modelName.$where)->getRowArray();
      $data = $this->db->query("select * from ".$modelName.$where." order by id desc limit {$page},{$get['limit']}")->getResultArray();

      $data = $this->_tabList($modelName, $data);

//      print_r($data);
//      die();
      $res = ['code' => '0', 'msg' => '模型数据', 'count' => $rowNum['rowNum'], 'data' => $data];
      return $this->response->setJSON($res);
    }


    $fieldArr = $this->_modelFields($modelName);
    array_unshift($fieldArr, ['fixed' => 'left', 'type' => 'checkbox']);

//    print_r($fieldArr);
//    die();
    if (isset($get['t']) && $get['t'] == 'child') {
      $fieldArr[] = ['fixed' => 'right', 'title' => '操作', 'width' => '120', 'templet' => '#toolDemo'];
    }
    else {
      $fieldArr[] = ['fixed' => 'right', 'title' => '操作', 'width' => '120', 'templet' => '#toolDemo'];
    }
    $fieldJson = json_encode($fieldArr);


    if ( isset($get['t']) && $get['t'] == 'child' ) {
      $res = ['code' => '1', 'msg' => '字段json', 'fieldJson' => $fieldJson];
      return $this->response->setJSON($res);
    }

    $res['modelName'] = $modelName;
    $res['fieldJson'] = $fieldJson;
    return view('model_data', $res);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function rowForm($modelName, $rowId)
  {
    $this->checkLogin();

    $get = $this->get;
    $rowData = $this->rowDetail($modelName, $rowId);
    $fieldArr = $this->_modelFields($modelName);
    if (empty($rowData)) {
      foreach ($fieldArr as $k => $v) {
        $rowData[$v['field']] = '';
      }
    }
//    print_r($rowData);
//    die();
    $search = (isset($get['t']) && $get['t'] == 'search') ? '' : '1';
    $child = (isset($get['t']) && $get['t'] == 'child') ? '' : '';

    $fieldForm = $this->fieldForm($fieldArr, $rowData, $search, $child);

    if (isset($get['t']) && $get['t'] == 'child' && $rowId != 0) {
      $rowHtml = '<div style="padding: 16px;"><form class="layui-form" lay-filter="demo-val-filter" id="'.$modelName.'">' . $fieldForm . '<div class="layui-form-item"><label class="layui-form-label"></label><div class="layui-input-block"><button  type="button" class="layui-btn layui-btn-primary" lay-submit lay-filter="child-submit">编辑</button></div></div></form></div>';
    }
    else if( isset($get['t']) && $get['t'] == 'child' && $rowId == 0 ) {

      $rowHtml = '<div style="padding: 16px;"><form class="layui-form" lay-filter="demo-val-filter" id="'.$modelName.'">' . $fieldForm . '<div class="layui-form-item"><label class="layui-form-label"></label><div class="layui-input-block"><button  type="button" class="layui-btn layui-btn-primary" lay-submit lay-filter="child-submit">增加</button></div></div></form></div>';
    }
    else if( isset($get['t']) && $get['t'] == 'search' && $rowId == 0 ) {

      $rowHtml = '<div style="padding: 16px;"><form class="layui-form" lay-filter="demo-val-filter" id="'.$modelName.'">' . $fieldForm . '<div class="layui-form-item"><label class="layui-form-label"></label><div class="layui-input-block"><button  type="button" class="layui-btn layui-btn-primary" lay-submit lay-filter="'.$modelName.'">搜索</button></div></div></form></div>';
    }
    else if( !isset($get['t']) && $rowId != 0 ) {

      $rowHtml = '<div style="padding: 16px;"><form class="layui-form" lay-filter="demo-val-filter" id="'.$modelName.'">' . $fieldForm . '<div class="layui-form-item"><label class="layui-form-label"></label><div class="layui-input-block"><button  type="button" class="layui-btn layui-btn-primary" lay-submit lay-filter="demo-submit">编辑</button></div></div></form></div>';
    }
    else if( isset($get['t']) && $get['t'] == 'searchChild' && $rowId == 0 ) {

      $rowHtml = '<div style="padding: 16px;"><form class="layui-form" lay-filter="demo-val-filter" id="'.$modelName.'">' . $fieldForm . '<div class="layui-form-item"><label class="layui-form-label"></label><div class="layui-input-block"><button  type="button" class="layui-btn layui-btn-primary" lay-submit lay-filter="'.$modelName.'">搜索</button></div></div></form></div>';
    }
    else {
      $rowHtml = '<div style="padding: 16px;"><form class="layui-form" lay-filter="demo-val-filter" id="'.$modelName.'">' . $fieldForm . '<div class="layui-form-item"><label class="layui-form-label"></label><div class="layui-input-block"><button  type="button" class="layui-btn layui-btn-primary" lay-submit lay-filter="priAdd">增加</button></div></div></form></div>';
    }

    $body = [];
    $body[] = ['content' => $rowHtml];
    $header = [];
    if (isset($get['t']) && $get['t'] == 'child') {
      $header[] = ['title' => $modelName . '&nbsp;<span class="layui-badge-rim layui-bg-gray">子表</span>'];
    }
    else if ( isset($get['t']) && $get['t'] == 'searchChild' ) {
      $header[] = ['title' => $modelName . '&nbsp;<span class="layui-badge-rim layui-bg-gray">子表</span>'];
    }
    else {
      $header[] = ['title' => $modelName . '&nbsp;<span class="layui-badge-rim layui-bg-cyan">主表</span>'];
    }

    if ($rowId != 0) {
      $child = $this->_childTab($modelName);
      foreach ($child as $k => $v) {
        $header[] = ['title' => $v['tabName'].'&nbsp;<span class="layui-badge-rim layui-bg-gray">子表</span>'];
        $body[] = ['content' => '<table class="layui-hide" id="'.$v['tabName'].'"></table><script type="text/html" id="toolbarDemo"><div class="layui-btn-group"><button type="button" class="layui-btn layui-btn-primary layui-btn-sm" lay-event="add"><i class="layui-icon layui-icon-add-1"></i> </button> <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" lay-event="batchDelete"> <i class="layui-icon layui-icon-delete"></i></button> <button type="button" class="layui-btn layui-btn-primary layui-btn-sm" lay-event="search"><i class="layui-icon layui-icon-search"></i></button><button type="button" class="layui-btn layui-btn-primary layui-btn-sm" lay-event="reload"><i class="layui-icon layui-icon-refresh"></i></button></div></script>'];
      }
    }


//    print_r($header);
//    print_r($body);
//    die();

    $res = ['code' => '1', 'msg' => '记录详情表单', 'header' => $header, 'body' => $body];

//    $this->accessLog($res);
    return $this->response->setJSON($res);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function rowDel($modelName, $rowId)
  {
    $this->checkLogin();
    try {
      $this->db->table($modelName)->delete(['id' => $rowId]);
      $res = ['code' => '1', 'msg' => '删除记录成功'];
    }
    catch (\Exception $e) {
      $res = ['code' => '0', 'msg' => $e->getMessage()];
    }

    return $this->response->setJSON($res);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function rowUpdate($modelName, $rowId)
  {
    $this->checkLogin();

    $post = $this->post;
    unset($post['file'], $post['undefined']);
    if ($rowId == '0') {
      unset($post['id']);
      try {
        $this->db->table(''.$modelName)->insert($post);
        $res = ['code' => '1', 'msg' => '添加成功'];
      }
      catch (\Exception $e) {
        $res = ['code' => '0', 'msg' => $e->getMessage()];
      }
    }
    else {
      try {
        $this->db->table(''.$modelName)->where(['id' => $rowId])->update($post);
        $res = ['code' => '1', 'msg' => '更新成功'];
      }
      catch (\Exception $e) {
        $res = ['code' => '0', 'msg' => $e->getMessage()];
      }
    }

    $this->accessLog($res);
    return $this->response->setJSON($res);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function settings()
  {
    $this->checkLogin();
    $res = $this->staticData();
    return view('model_list', $res);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function users()
  {
    $get = $this->get;
    $res = $this->staticData();

    if (isset($get['usersJson'])) {
      $this->cache->save('users', $get['usersJson'], 60*60*24*365*10);
      $res = ['code' => '1', 'msg' => '保存成功'];
      return $this->response->setJSON($res);
    }
    else {
      return view('users', $res);
    }


  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function backup()
  {
    $this->checkLogin();
    $res = $this->staticData();
    $get = $this->get;
    return view('backup', $res);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function systemConfig()
  {
    $this->checkLogin();
    $get = $this->get;
    $res = $this->staticData();


    if (isset($get['systemConfigJson'])) {
      $this->cache->save('systemConfig', $get['systemConfigJson'], 60*60*24*365*10);
      $res = ['code' => '1', 'msg' => '保存成功'];
      return $this->response->setJSON($res);
    }
    else {
      return view('systemConfig', $res);
    }


  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function modelJson()
  {
    $this->checkLogin();
    $post = $this->post;
    $this->cache->save('models', $post['modelJson'], 60*60*24*365*10);
    $res = ['code' => '1', 'msg' => '保存成功'];
    return $this->response->setJSON($res);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function uploadFile()
  {
    $this->checkLogin();
    $path = getcwd().'/uploads/'.date('Ymd',time()).'/';
    $fileName = substr(md5(date('YmdHis').$this->randNum(6)),0,16);
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

  private function fieldForm($fieldArr, $rowData = [], $search = '', $child = ''):string
  {
    $form = '';
    foreach ($fieldArr as $key => $value) {

      $fieldType = substr($value['COLUMN_COMMENT'], strpos($value['COLUMN_COMMENT'], '[')+1, strpos($value['COLUMN_COMMENT'], ']')-(strpos($value['COLUMN_COMMENT'], '[')+1));
      $fieldTypeArr = explode('|', $fieldType);
      $inputValue = empty($rowData) ? '':' value="'.$rowData[$value['field']].'"';
      $foreignKey = '';
      $layVerify = empty($search) ? '' : '';
      $readOnly = !empty($child) && !empty($value['priTabKey']) ? 'readonly' : '';

      switch ($fieldTypeArr['0']) {
        case 'input':
          if ($value['field'] == 'id' && $rowData[$value['field']] == '') {
            $form .= '';
          }
          else {
            $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block"><input type="text" name="'.$value['field'].'" lay-verify="'.$layVerify.'" placeholder="请输入" autocomplete="off" class="layui-input" '.$inputValue.' '.$readOnly.'></div>'.$foreignKey.'</div>';
          }
          break;
        case 'date':
          $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block"><input type="text" name="'.$value['field'].'" placeholder="yyyy-MM-dd HH:mm:ss" autocomplete="off" class="layui-input lay-date" '.$inputValue.'  lay-verify="datetime" ></div></div>';
          break;
        case 'float':
          $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block"><input type="text" name="'.$value['field'].'" lay-verify="'.$layVerify.'" placeholder="请输入" autocomplete="off" class="layui-input" '.$inputValue.'></div></div>';
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
          if (empty($search)) {
            $form .= '';
          }
          else {
            $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block"><div class="layui-btn-group"><button type="button" class="layui-btn layui-btn-primary layui-btn-sm uploadFile" uploadFieldName="'.$value['field'].'"><i class="layui-icon layui-icon-uploads"></i>文件上传</button><a type="button" class="layui-btn layui-btn-primary layui-btn-sm" href="'.($rowData[$value['field']] =='' ? 'javascript:;':$rowData[$value['field']]).'" target="_blank" id="'.$value['field'].'_btn">'.($rowData[$value['field']] =='' ? '文件地址':$rowData[$value['field']]).'</a></div><input type="text" name="'.$value['field'].'" placeholder="请输入" autocomplete="off" id="'.$value['field'].'" class="layui-input" style="display:none;" '.$inputValue.'></div></div>';
          }
          break;
        case 'json':
          $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block"><textarea placeholder="请输入" class="layui-textarea" name="'.$value['field'].'" name="'.$value['field'].'" lay-verify="'.$layVerify.'" placeholder="请输入" autocomplete="off">'.$rowData[$value['field']].'</textarea></div></div>';
          break;
        case 'editor':
          $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block"><textarea placeholder="请输入" class="layui-textarea" name="'.$value['field'].'" name="'.$value['field'].'" lay-verify="'.$layVerify.'" placeholder="请输入" autocomplete="off" >'.$rowData[$value['field']].'</textarea></div></div>';
          break;
        case 'checkbox':
          $checkboxArr = explode('&', $fieldTypeArr['1']);
          $optionForm = '';
          foreach ($checkboxArr as $k => $v) {
            $optionArr = explode('=', $v);
            $checkboxValueArr = explode(',', $rowData[$value['field']]);
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
            $isChecked = ($optionArr['0'] == $rowData[$value['field']]) ? 'selected':'';
            $optionForm .= '<option name="'.$value['field'].'[]" value="'.$optionArr['0'].'" title="'.$optionArr['1'].'" '.$isChecked.'>'.$optionArr['1'].'</option>';
          }
          $form .= '<div class="layui-form-item"><label class="layui-form-label">'.$value['title'].'</label><div class="layui-input-block"><select name="'.$value['field'].'" value="'.$optionArr['0'].'" title="'.$optionArr['1'].'">'.$optionForm.'</select></div></div>';
//          print_r($form);
//          print_r($rowData[$value['field']]);
//          die();
          break;
      }
    }

//    print_r($form);
//    die();
    return $form;
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  private function _modelFields($modelName)
  {
    $fieldArr = $this->db->query("SELECT
  C.COLUMN_NAME as field,
  C.COLUMN_COMMENT,
  C.COLUMN_KEY,
  left(C.COLUMN_COMMENT, locate('[', C.COLUMN_COMMENT) -1) as title,
  '' as width,
  if(C.COLUMN_NAME='id','left','') as fixed,
  if(K.REFERENCED_TABLE_NAME<>C.TABLE_NAME,K.REFERENCED_TABLE_NAME,'') as priTab,
  if(K.REFERENCED_TABLE_NAME<>C.TABLE_NAME,K.REFERENCED_COLUMN_NAME,'') as priTabKey
FROM
  INFORMATION_SCHEMA.COLUMNS C
left JOIN information_schema.KEY_COLUMN_USAGE K
ON C.COLUMN_NAME=K.CONSTRAINT_NAME
WHERE
  C.TABLE_SCHEMA = 'laycms'
  AND C.TABLE_NAME = '{$modelName}'
order by
  C.ordinal_position asc")->getResultArray();

    return $fieldArr;
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  private function rowDetail($modelName, $rowId)
  {
    $this->checkLogin();
    $rowDetail = $this->db->query("select * from {$modelName} where id='{$rowId}'")->getRowArray();
    return $rowDetail;
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function login()
  {
    $get = $this->get;
    $res = $this->staticData();

    if (isset($get['userName']) && !empty($get['userName'])) {

//      print_r($get);123

      $users = json_decode($this->cache->get('users'), true);
//
      foreach ($users as $k => $v) {
        if ($get['userName'] == $v['userName'] && $get['passWord'] == $v['passWord']) {
          $this->session->set('login', $v);
          $res = ['code' => '1', 'msg' => '登录成功'];
          return $this->response->setJSON($res);
        }
      }
      $res = ['code' => '0', 'msg' => '登录失败'];
      return $this->response->setJSON($res);

    }
    else {
      $this->session->remove('login');
      $this->accessLog($res);
      return view('login', $res);
    }

  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  public function noPermission()
  {
    $get = $this->get;
    $res = $this->staticData();
    $res['msg'] = '您没有操作这里的权限';
    return view('noPermission', $res);
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  private function _childTab($modelName)
  {
    $tabArr = $this->db->query("SELECT
  TABLE_NAME as tabName
FROM
  information_schema.KEY_COLUMN_USAGE
where
  CONSTRAINT_SCHEMA = 'laycms'
  and REFERENCED_TABLE_NAME = '{$modelName}'
group by
  TABLE_NAME")->getResultArray();

    return $tabArr;
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

  private function _tabList($modelName, $dataList)
  {

    $modelFields = $this->_modelFields($modelName);

    foreach ($dataList as $k1 => $v1) {
      foreach ($modelFields as $k2 => $v2) {
//        print_r($v2);
        $fieldType = substr($v2['COLUMN_COMMENT'], strpos($v2['COLUMN_COMMENT'], '[')+1, strpos($v2['COLUMN_COMMENT'], ']')-(strpos($v2['COLUMN_COMMENT'], '[')+1));
        $fieldTypeArr = explode('|', $fieldType);
//        print_r($fieldTypeArr);
        switch ($fieldTypeArr['0']) {
          case 'date':
//            $v1[$v2['field']] = date($fieldTypeArr['1'], $v1[$v2['field']]);
            break;
          case 'radio':
            $radioArr = explode('&', $fieldTypeArr['1']);
            foreach ($radioArr as $k => $v) {
              $optionArr = explode('=', $v);
//              print_r($optionArr);
//              print_r($v1[$v2['field']].'ddd');
//              print_r('---');
              if ($optionArr['0'] == $v1[$v2['field']]) {
                $v1[$v2['field']] = $optionArr['1'];
              }
            }
            break;
          case 'select':
            $selectArr = explode('&', $fieldTypeArr['1']);
            foreach ($selectArr as $k => $v) {
              $optionArr = explode('=', $v);
//              print_r($optionArr);
//              print_r($v1[$v2['field']].'ddd');
//              print_r('---');
              if ($optionArr['0'] == $v1[$v2['field']]) {
                $v1[$v2['field']] = $optionArr['1'];
              }
            }
            break;
          case 'checkbox':
            $checkboxArr = explode('&', $fieldTypeArr['1']);
            $checkboxStr = '';
            foreach ($checkboxArr as $k => $v) {
              $optionArr = explode('=', $v);
              $checkboxValueArr = explode(',', $v1[$v2['field']]);

              if (in_array($optionArr['0'], $checkboxValueArr)) {
                $checkboxStr .= $optionArr['1'].'/';
              }
            }
            $v1[$v2['field']] = $checkboxStr;
            break;
        }

      }
      $dataList[$k1] = $v1;
//      print_r($v1);
    }

//    print_r($dataList);

    return $dataList;
  }

  //- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -

}
