<?php

$view->layout();
?>

<?= $block('header-actions') ?>
<a class="btn btn-secondary" href="<?= $url('admin/member-levels') ?>">返回列表</a>
<?= $block->end() ?>

<div class="row">
  <div class="col-12">
    <form class="js-member-level-form form-horizontal" role="form" method="post">

      <div class="form-group">
        <label class="col-lg-2 control-label" for="name">
          <span class="text-warning">*</span>
          名称
        </label>

        <div class="col-lg-4">
          <input type="text" name="name" id="name" class="form-control" required>
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="special">
          特殊等级
        </label>

        <div class="col-lg-4">
          <div class="checkbox">
            <label>
              <input name="special" type="hidden" value="0" data-populate-ignore>
              <input class="js-special" id="special" name="special" type="checkbox" value="1"
                data-reverse-target=".js-score-form-group" data-value=":checked">
            </label>
          </div>
        </div>

        <label class="col-lg-4 help-text">
          特殊等级没有积分要求,需手工设定会员为该等级
        </label>
      </div>

      <div class="js-score-form-group form-group">
        <label class="col-lg-2 control-label" for="score">
          要求积分
        </label>

        <div class="col-lg-4">
          <input class="js-score form-control" id="score" name="score" type="text"
            data-rule-required="true" data-rule-number="true">
        </div>
      </div>

      <div class="form-group hide">
        <label class="col-lg-2 control-label" for="discount">
          折扣
        </label>

        <div class="col-lg-4">
          <input type="text" name="discount" id="discount" class="form-control">
        </div>
      </div>

      <div class="form-group">
        <label class="col-lg-2 control-label" for="image">
          背景图
        </label>

        <div class="col-lg-4">
          <input type="text" id="image" name="image" class="js-image">
        </div>

        <label class="col-lg-4" for="image">
          留空展示会员卡背景图
        </label>
      </div>

      <div class="clearfix form-actions form-group">
        <input type="hidden" name="id" id="id">

        <div class="offset-lg-2">
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-check bigger-110"></i>
            提交
          </button>
          &nbsp; &nbsp; &nbsp;
          <a class="btn btn-secondary" href="<?= $url('admin/member-levels') ?>">
            <i class="fa fa-undo bigger-110"></i>
            返回列表
          </a>
        </div>
      </div>
    </form>
  </div>
</div>

<?= $block->js() ?>
<script>
  require([
    'plugins/admin/js/form',
    'plugins/app/js/validation',
    'plugins/app/libs/jquery.populate/jquery.populate',
    'plugins/app/libs/jquery-toggle-display/jquery-toggle-display',
    'plugins/admin/js/image-upload'
  ], function () {
    $('.js-member-level-form')
      .populate(<?= $memberLevel->toJson() ?>)
      .ajaxForm({
        url: $.url('admin/member-levels/update'),
        dataType: 'json',
        beforeSubmit: function (arr, $form) {
          return $form.valid();
        },
        success: function (ret) {
          $.msg(ret, function () {
            if (ret.code === 1) {
              window.location.href = $.url('admin/member-levels');
            }
          });
        }
      })
      .validate();

    $('.js-image').imageUpload();
    $('.js-special').toggleDisplay();
  });
</script>
<?= $block->end() ?>
