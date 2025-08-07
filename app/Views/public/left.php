<div class="layui-side layui-bg-black">
  <div class="layui-side-scroll">
    <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
    <ul class="layui-nav layui-nav-tree" lay-filter="test">
      <?php foreach ($menus as $key => $value): ?>
        <li class="layui-nav-item layui-nav-itemed">
          <a class="" href="javascript:;"><?= $value['group'] ?></a>
          <dl class="layui-nav-child">
            <?php foreach ($value['model'] as $key1 => $value1): ?>
              <dd class="<?php if (isset($_GET['model']) && $_GET['model'] == $value1['name_en']): ?>layui-this<?php endif ?>"><a href="/model/data/<?= $value1['name_en'] ?>"><?= $value1['name_ch'] ?></a></dd>
            <?php endforeach ?>
          </dl>
        </li>
      <?php endforeach ?>
    </ul>
  </div>
</div>
