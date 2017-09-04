<?php $view->layout() ?>

<div class="row">
  <div class="col-xs-12">
    <form action="<?= $url('admin/member-settings/update') ?>" class="js-setting-form form-horizontal" method="post"
      role="form">
      <div class="form-group">
        <label class="col-lg-2 control-label" for="init-level">
          <span class="text-warning">*</span>
          新卡的等级
        </label>

        <div class="col-lg-4">
          <select class="js-member-init-level form-control" id="init-level" name="settings[member.init_level]">
            <?php foreach ($levels as $level) : ?>
              <option value="<?= $level['id'] ?>"><?= $level['name'] ?></option>
            <?php endforeach ?>
          </select>
        </div>
      </div>

      <div class="clearfix form-actions form-group">
        <div class="col-lg-offset-2">
          <button class="btn btn-primary" type="submit">
            <i class="fa fa-check bigger-110"></i>
            提交
          </button>
        </div>
      </div>
    </form>
  </div>
  <!-- PAGE CONTENT ENDS -->
</div><!-- /.col -->
<!-- /.row -->

<?= $block('js') ?>
<script>
  require(['form', 'ueditor', 'validator'], function () {
    $('.js-setting-form')
      .loadJSON(<?= $setting->getFormJson([
        'member.init_level_id' => 0
      ]) ?>)
      .ajaxForm({
        dataType: 'json',
        beforeSubmit: function (arr, $form) {
          return $form.valid();
        }
      })
      .validate();
  });
</script>
<?= $block->end() ?>
