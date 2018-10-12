<?php

$view->layout();
$enableLevel = wei()->member->enableLevel;
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
              <input type="text" class="js-nick-name form-control input-sm" id="nick-name" name="nick_name_user_id"
                placeholder="请输入昵称搜索">
            </div>

            <label class="col-md-1 control-label" for="mobile">手机号：</label>

            <div class="col-md-3">
              <input type="text" class="js-mobile form-control input-sm" id="mobile" name="mobile_user_id"
                placeholder="请输入手机号搜索">
            </div>

            <label class="col-md-1 control-label" for="code">卡号：</label>

            <div class="col-md-3">
              <input type="text" class="form-control" id="code" name="card_code">
            </div>
          </div>

          <div class="form-group form-group-sm">
            <?php if ($enableLevel) { ?>
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
            <?php } ?>

            <label class="col-md-1 control-label" for="consumed-at">首次消费时间：</label>

            <div class="col-md-3">
              <input type="text" class="js-range-date form-control" id="consumed-at">
              <input type="hidden" class="js-start-date" name="start_date">
              <input type="hidden" class="js-end-date" name="end_date">
            </div>

          </div>

          <div class="clearfix form-group form-group-sm">
            <div class="col-md-offset-1 col-md-6">
              <button class="btn btn-primary btn-sm" type="submit">
                查询
              </button>
              &nbsp;
              <button class="js-export-csv btn btn-default btn-sm" type="button">
                导出
              </button>
            </div>
          </div>
        </div>
      </form>

      <table class="js-member-table record-table table table-bordered table-hover">
        <thead>
        <tr>
          <th>用户</th>
          <th>卡号</th>
          <?php if ($enableLevel) { ?>
          <th>等级</th>
          <?php } ?>
          <th>首次消费时间</th>
          <th>上次消费时间</th>
          <th>领取的优惠券数</th>
          <th>使用的优惠券数</th>
          <th>现有积分数</th>
          <th>使用过的积分数</th>
          <th>总的积分数</th>
          <th class="t-5">操作</th>
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

<script id="js-edit-level-modal" type="text/html">
  <div class="modal fade">
    <div class="modal-dialog">
      <div class="modal-content">
        <form class="js-edit-form form-horizontal" action="<%= $.url('admin/members/update-level') %>" method="post">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h4 class="modal-title">编辑会员等级</h4>
          </div>
          <div class="modal-body">
            <div class="form-group">
              <label class="col-lg-3 control-label">当前等级</label>

              <div class="col-lg-8">
                <p class="form-control-static" name="level_name"></p>
              </div>
            </div>

            <div class="form-group">
              <label class="col-lg-3 control-label">积分所属等级</label>

              <div class="col-lg-8">
                <p class="form-control-static" name="score_level_name"></p>
              </div>
            </div>

            <div class="form-group">
              <label for="edit-level-id" class="col-lg-3 control-label">更改为等级</label>

              <div class="col-lg-8">
                <select class="js-level form-control" id="edit-level-id" name="level_id">
                  <?php foreach ($levels as $level) : ?>
                    <option value="<?= $level['id'] ?>"><?= $level['name'] ?></option>
                  <?php endforeach ?>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="edit-description" class="col-lg-3 control-label">
                <span class="text-warning">*</span>
                更改说明
              </label>

              <div class="col-lg-8">
                <textarea class="form-control" id="edit-description" name="description"></textarea>
              </div>
            </div>

            <input type="hidden" name="id" id="id">
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
            <button type="submit" class="btn btn-primary">保存</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</script>

<script id="action-tpl" type="text/html">
  <a class="js-edit-level" href="javascript:;">更改等级</a>
</script>
<?php require $view->getFile('@user/admin/user/richInfo.php') ?>

<?= $block->js() ?>
<script>
  require([
    'dataTable',
    'form',
    'comps/select2/select2.min',
    'css!comps/select2/select2',
    'css!comps/select2-bootstrap-css/select2-bootstrap',
    'daterangepicker',
    'plugins/app/libs/jquery.populate/jquery.populate'
  ], function () {
    var $table = $('.js-member-table').dataTable({
      sorting: [[0, 'desc']],
      ajax: {
        url: $.queryUrl('admin/members', {_format: 'json'})
      },
      columns: [
        {
          data: 'id',
          render: function (data, type, full) {
            return template.render('user-info-tpl', full.user);
          }
        },
        {
          data: 'code'
        },
        <?php if ($enableLevel) { ?>
        {
          data: 'level_name',
          render: function (data) {
            return data || '无';
          }
        },
        <?php } ?>
        {
          data: 'consumed_at',
          render: function (data) {
            return data === '0000-00-00 00:00:00' ? '无' : data;
          }
        },
        {
          data: 'last_consumed_at',
          render: function (data) {
            return data === '0000-00-00 00:00:00' ? '无' : data;
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

    $('.js-mobile').select2({
      allowClear: true,
      ajax: {
        url: $.url('admin/user.json', {filter_empty: 'mobile'}),
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
      $('.js-start-date').val(start.format(this.format) + ' 00:00:00');
      $('.js-end-date').val(end.format(this.format) + ' 23:59:59');
      this.element.trigger('change');
    });

    $table.on('click', '.js-edit-level', function () {
      var data = $table.fnGetData($(this).closest('tr')[0]);
      var $modal = $(template.render('js-edit-level-modal'));

      $.ajax({
        url: $.url('admin/member-levels/get-level.json'),
        data: {score: data.score},
        dataType: 'json',
        success: function () {
          // 屏蔽自动提示
        }
      }).done(function (ret) {
        if (ret.code !== 1) {
          $.msg(ret);
          return;
        }

        if (ret.data) {
          data.score_level_name = ret.data.name;
          data.level_id = ret.data.id;
        } else {
          data.score_level_name = '无';
        }

        $modal.modal('show');
        $modal.find('.js-edit-form')
          .loadJSON(data)
          .ajaxForm({
            dataType: 'json',
            success: function (ret) {
              $.msg(ret, function () {
                if (ret.code === 1) {
                  $modal.modal('hide');
                  $table.reload();
                }
              });
            }
          });
      });
    });

    $('.js-export-csv').click(function () {
      window.location = $.appendUrl($table.fnSettings().ajax.url, {page: 1, rows: 99999, _format: 'csv'});
    });
  });
</script>
<?= $block->end() ?>
