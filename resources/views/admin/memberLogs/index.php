<?php

$view->layout();
?>

<div class="row">
  <div class="col-xs-12">
    <!-- PAGE CONTENT BEGINS -->
    <div class="table-responsive">
      <form class="js-member-form form-horizontal filter-form" role="form">
        <div class="well form-well m-b">
          <div class="form-group form-group-sm">
            <label class="col-md-1 control-label" for="created-by">操作人：</label>

            <div class="col-md-3">
              <input type="text" class="js-nick-name form-control input-sm" id="created-by" name="created_by" placeholder="请输入昵称搜索">
            </div>

            <label class="col-md-1 control-label" for="created-at">操作时间：</label>

            <div class="col-md-3">
              <input type="text" class="js-range-date form-control" id="created-at">
              <input type="hidden" class="js-start-date" name="start_date">
              <input type="hidden" class="js-end-date" name="end_date">
            </div>

            <label class="col-md-1 control-label" for="code">会员卡号：</label>

            <div class="col-md-3">
              <input class="form-control" id="code" name="card_code" type="text">
            </div>

          </div>

          <div class="clearfix form-group form-group-sm">
            <div class="col-md-offset-1 col-md-6">
              <button class="js-user-filter btn btn-primary btn-sm" type="submit">
                查询
              </button>
            </div>
          </div>
        </div>
      </form>

      <table class="js-member-table record-table table table-bordered table-hover">
        <thead>
        <tr>
          <th>操作人</th>
          <th>时间</th>
          <th>会员</th>
          <th>会员卡号</th>
          <th>操作</th>
          <th>操作说明</th>
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

<?php require $view->getFile('@user/admin/user/richInfo.php') ?>

<?= $block->js() ?>
<script>
  require([
    'dataTable',
    'form',
    'comps/select2/select2.min',
    'css!comps/select2/select2',
    'css!comps/select2-bootstrap-css/select2-bootstrap',
    'daterangepicker'
  ], function () {
    var $table = $('.js-member-table').dataTable({
      ajax: {
        url: $.queryUrl('admin/member-logs.json')
      },
      columns: [
        {
          data: 'creator',
          render: function (data, type, full) {
            return template.render('user-info-tpl', data);
          }
        },
        {
          data: 'created_at'
        },
        {
          data: 'user',
          render: function (data, type, full) {
            return template.render('user-info-tpl', data);
          }
        },
        {
          data: 'code'
        },
        {
          data: 'action',
          sClass: 'js-tooltip'
        },
        {
          data: 'description',
          sClass: 'js-tooltip'
        }
      ]
    });

    $('.js-member-form')
      .loadParams()
      .submit(function (e) {
        $table.reload($(this).serialize(), false);
        e.preventDefault();
      });

    $('.js-nick-name').select2({
      allowClear: true,
      ajax: {
        url: $.url('admin/user.json', {filter_empty: 'nickName'}),
        dataType: 'json',
        data: function (term) {
          return {
            nickName: term,
            rows: 20
          };
        },
        results: function (ret) {
          var results = [];
          for (var i in ret.data) {
            if (Object.prototype.hasOwnProperty.call(ret.data, i)) {
              results.push({
                id: ret.data[i]['id'],
                text: ret.data[i]['nickName']
              });
            }
          }
          return {
            results: results
          };
        }
      }
    });

    // 日期范围选择
    $('.js-range-date').daterangepicker({
      format: 'YYYY-MM-DD',
      separator: ' ~ '
    }, function (start, end) {
      $('.js-start-date').val(start.format(this.format));
      $('.js-end-date').val(end.format(this.format));
      this.element.trigger('change');
    });

    $table.tooltip({
      selector: 'td.js-tooltip',
      container: 'body',
      title: function () {
        return $(this).html();
      }
    });
  });
</script>
<?= $block->end() ?>
