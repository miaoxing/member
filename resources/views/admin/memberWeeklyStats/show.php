<?php

$view->layout();
?>

<?= $block('css') ?>
<link rel="stylesheet" href="<?= $asset('assets/admin/stat.css') ?>"/>
<?= $block->end() ?>

<div class="row">
  <div class="col-xs-12">

    <div class="well well-sm bigger-110">
      名称：<?= $e($card['title']) ?>
    </div>

    <div class="well well-sm">
      <form class="js-chart-form form-inline">
        <div class="form-group">
          <label class="control-label" for="range-date">周数的日期范围</label>
          <input type="text" class="js-range-date form-control text-center input-large" id="range-date"
            value="<?= $e($startDate . ' ~ ' . $endDate) ?>">
          <input type="hidden" class="js-start-date" name="start_date">
          <input type="hidden" class="js-end-date" name="end_date">
        </div>
      </form>
    </div>

    <h5 class="stat-title">趋势图</h5>

    <ul class="js-chart-tabs nav tab-underline">
      <li role="presentation" class="active">
        <a href="#receive" aria-controls="receive" role="tab" data-toggle="tab">领取数</a>
      </li>
      <li role="presentation">
        <a href="#first-consume" aria-controls="first-consume" role="tab" data-toggle="tab">首次消费数</a>
      </li>
    </ul>
    <div class="tab-content m-t no-border">
      <div role="tabpanel" class="js-chart-pane tab-pane text-center active" id="receive">
        加载中...
      </div>
      <div role="tabpanel" class="js-chart-pane tab-pane" id="first-consume"></div>
    </div>

    <hr>

    <h5 class="stat-title">详细数据</h5>

    <table class="js-stat-table table table-center table-head-bordered">
      <thead>
      <tr>
        <th>周（日期）</th>
        <th>领取人数</th>
        <th>首次消费人数</th>
      </tr>
      </thead>
      <tbody>
      </tbody>
    </table>

  </div>
  <!-- /col -->
</div>
<!-- /row -->

<?= $block('js') ?>
<script>
  require([
    'plugins/stat/js/stat',
    'template',
    'highcharts',
    'form',
    'jquery-deparam',
    'dataTable',
    'daterangepicker'
  ], function (stat, template) {
    // 渲染底部表格
    var $statTable = $('.js-stat-table').dataTable({
      dom: 't',
      ajax: null,
      processing: false,
      serverSide: false,
      displayLength: 99999,
      columnDefs: [{
        targets: ['_all'],
        sortable: true
      }],
      columns: [
        {
          data: 'stat_week'
        },
        {
          data: 'receive_user'
        },
        {
          data: 'first_consume_user'
        }
      ]
    });

    // 所有图表的配置
    var charts = [
      {
        id: 'receive',
        series: [
          {
            name: '领取人数',
            dataSource: 'receive_user'
          }
        ],
        xAxis: {
          categoriesSource: 'stat_week'
        }
      },
      {
        id: 'first-consume',
        series: [
          {
            name: '首次消费人数',
            dataSource: 'first_consume_user'
          }
        ],
        xAxis: {
          categoriesSource: 'stat_week'
        }
      }
    ];

    var $form = $('.js-chart-form');

    function render() {
      $.ajax({
        url: $.queryUrl('admin/member-weekly-stats/show.json'),
        dataType: 'json',
        data: $form.serializeArray(),
        success: function (ret) {
          if (ret.code !== 1) {
            $.msg(ret);
            return;
          }

          stat.renderChart({
            charts: charts,
            data: ret.data
          });
          $statTable.fnClearTable();
          $statTable.fnAddData(ret.data);
        }
      });
    }

    render();

    // 更新表单时,重新渲染
    $form.update(function () {
      render();
    });

    // 日期范围选择
    $('.js-range-date').daterangepicker({
      format: 'YYYY-MM-DD',
      separator: ' ~ ',
      showWeekNumbers: true
    }, function (start, end) {
      $('.js-start-date').val(start.format(this.format));
      $('.js-end-date').val(end.format(this.format));
      this.element.trigger('change');
    });
  });
</script>
<?= $block->end() ?>
