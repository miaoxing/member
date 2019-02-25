<?php $view->layout() ?>

<div class="row">
  <div class="col-12">
    <form action="<?= $url('admin/member-settings/update') ?>" class="js-setting-form form-horizontal" method="post"
      role="form">
      <div class="form-group">
        <label class="col-lg-2 control-label" for="default-card-id">
          <span class="text-warning">*</span>
          默认会员卡
        </label>

        <div class="col-lg-4">
          <?php if ($cards->length()) : ?>
            <select class="js-member-default-card-id form-control" id="default-card-id" name="settings[member.default_card_id]">
              <?php foreach ($cards as $card) : ?>
                <option value="<?= $card['id'] ?>"><?= $card['title'] ?></option>
              <?php endforeach ?>
            </select>
          <?php else: ?>
            <p class="form-control-plaintext">暂无会员卡</p>
          <?php endif ?>
        </div>
      </div>

      <?php if (wei()->member->enableLevel) { ?>
      <div class="form-group">
        <label class="col-lg-2 control-label" for="init-level-id">
          <span class="text-warning">*</span>
          新卡的等级
        </label>

        <div class="col-lg-4">
          <?php if ($levels->length()) : ?>
            <select class="js-member-init-level-id form-control" id="init-level-id" name="settings[member.init_level_id]">
              <?php foreach ($levels as $level) : ?>
                <option value="<?= $level['id'] ?>"><?= $level['name'] ?></option>
              <?php endforeach ?>
            </select>
          <?php else : ?>
            <p class="form-control-plaintext">暂无等级</p>
          <?php endif ?>
        </div>
      </div>
      <?php } ?>

      <div class="clearfix form-actions form-group">
        <div class="offset-lg-2">
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

<?= $block->js() ?>
<script>
  require(['form', 'ueditor', 'plugins/app/js/validation'], function () {
    $('.js-setting-form')
      .loadJSON(<?= $setting->getFormJson([
        'member.default_card_id' => 0,
        'member.init_level_id' => 0,
      ]) ?>)
      .ajaxForm({
        dataType: 'json',
        beforeSubmit: function (arr, $form) {
          return $form.valid();
        },
        success: function (ret) {
          $.msg(ret);
        }
      })
      .validate();
  });
</script>
<?= $block->end() ?>
