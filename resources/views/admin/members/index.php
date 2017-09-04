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
            <label class="col-md-1 control-label" for="nick-name">昵称：</label>

            <div class="col-md-3">
              <input type="text" class="js-nick-name form-control input-sm" id="nick-name" name="nick_name_user_id">
            </div>

            <label class="col-md-1 control-label" for="mobile">手机号：</label>

            <div class="col-md-3">
              <input type="text" class="js-mobile form-control input-sm" id="mobile" name="mobile_user_id">
            </div>

            <label class="col-md-1 control-label" for="level-id">等级：</label>

            <div class="col-md-3">
              <select name="level_id" id="level-id" class="form-control">
                <option value="">全部</option>
                <option value="0">无</option>
                <?php foreach ($levels as $level) : ?>
                  <option value="<?= $level['id'] ?>"><?= $level['name'] ?></option>
                <?php endforeach ?>
              </select>
            </div>

          </div>

          <div class="form-group form-group-sm">

            <label class="col-md-1 control-label" for="consumed-at">首次消费时间：</label>

            <div class="col-md-3">
              <input type="text" class="js-range-date form-control" id="consumed-at">
              <input type="hidden" class="js-start-date" name="start_date">
              <input type="hidden" class="js-end-date" name="end_date">
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
          <th>用户</th>
          <th>等级</th>
          <th>首次消费时间</th>
          <th>领取的优惠券数</th>
          <th>使用的优惠券数</th>
          <th>现有积分数</th>
          <th>使用过的积分数</th>
          <th>总的积分数</th>
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
  <a href="<%= $.url('admin/members/%s/edit', id) %>">编辑</a>
</script>
<?php require $view->getFile('@user/admin/user/richInfo.php') ?>

<?= $block('js') ?>
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
        url: $.queryUrl('admin/members.json')
      },
      columns: [
        {
          data: 'id',
          render: function (data, type, full) {
            return template.render('user-info-tpl', full);
          }
        },
        {
          data: 'level_name',
          render: function (data) {
            return data || '无';
          }
        },
        {
          data: 'consumed_at',
          render: function (data) {
            return data == '0000-00-00 00:00:00' ? '无' : data;
          }
        },
        {
          data: 'total_card_count'
        },
        {
          data: 'used_card_count'
        },
        {
          data: 'score'
        },
        {
          data: 'used_score'
        },
        {
          data: 'total_score',
          sortable: true
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

    $('.js-member-form')
      .loadParams()
      .submit(function (e) {
        $table.reload($(this).serialize(), false);
        e.preventDefault();
      });

    $('.js-nick-name').select2({
      ajax: {
        url: $.url('admin/user.json'),
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

    $('.js-mobile').select2({
      ajax: {
        url: $.url('admin/user.json'),
        dataType: 'json',
        data: function (term) {
          return {
            mobile: term,
            rows: 20
          };
        },
        results: function (ret) {
          var results = [];
          $.each(ret.data, function (i, row) {
            results.push({
              id: row.id,
              text: row.nickName + ' ' + (row.mobile || '无手机')
            });
          });
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
  });
</script>
<?= $block->end() ?>
