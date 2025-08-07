<div class="layui-side layui-bg-cyan">
  <div class="layui-side-scroll layui-bg-cyan">
    <!-- 左侧导航区域（可配合layui已有的垂直导航） -->
    <ul class="layui-nav layui-nav-tree layui-bg-cyan" lay-filter="test">
      <?php foreach ($menus as $key => $value): ?>
        <li class="layui-nav-item layui-nav-itemed">
          <a class="" href="javascript:;"><i class="layui-icon layui-icon-folder"></i> &nbsp;<?= $value['group'] ?></a>
          <dl class="layui-nav-child">
            <?php foreach ($value['model'] as $key1 => $value1): ?>
              <dd class="<?php if (strpos($_SERVER['REQUEST_URI'], $value1['name_en']) !== false): ?>layui-this<?php endif ?>"><a href="/model/data/<?= $value1['name_en'] ?>"><i class="layui-icon layui-icon-table"></i> &nbsp;<?= $value1['name_ch'] ?></a></dd>
            <?php endforeach ?>
          </dl>
        </li>
      <?php endforeach ?>
    </ul>
  </div>
</div>
