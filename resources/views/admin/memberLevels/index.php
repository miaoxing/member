<?php

$view->layout();
?>

<?= $block('header-actions') ?>
<a class="btn btn-success" href="<?= $url('admin/member-levels/new') ?>">添加会员等级</a>
<?= $block->end() ?>

<div class="row">
  <div class="col-xs-12">
    <!-- PAGE CONTENT BEGINS -->
    <div class="table-responsive">
      <table class="js-member-level-table record-table table table-bordered table-hover">
        <thead>
        <tr>
          <th>名称</th>
          <th>积分范围</th>
          <th>折扣</th>
          <th>特殊</th>
          <th class="t-8">操作</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
    </div>
    <!-- /.table-responsive -->
    <!-- PAGE CONTENT ENDS -->
  </div>
  <!-- /col -->
</div>
<!-- /row -->

<script id="action-tpl" type="text/html">
  <a href="<%= $.url('admin/member-levels/%s/edit', id) %>">编辑</a>
</script>

<?= $block('js') ?>
<script>
  require(['dataTable', 'form'], function () {
    $('.js-member-level-table').dataTable({
      ajax: {
        url: $.queryUrl('admin/member-levels.json')
      },
      columns: [
        {
          data: 'name'
        },
        {
          data: 'start_score',
          render: function (data, type, full) {
            if (full.special == '1') {
              return '无'
            } else if (full.end_score == '0') {
              return '> ' + full.start_score;
            } else {
              return full.start_score + ' ~ ' + full.end_score;
            }
          }
        },
        {
          data: 'discount',
          render: function (data, type, full) {
            return data === '0.00' ? '无' : data;
          }
        },
        {
          data: 'special',
          render: function (data, type, full) {
            return full.special == '1' ? '是' : '否';
          }
        },
        {
          data: 'id',
          sClass: 'text-center',
          render: function (data, type, full) {
            return template.render('action-tpl', full);
          }
        }
      ]
    });
  });
</script>
<?= $block->end() ?>
