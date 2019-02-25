<div class="form-group">
  <label class="col-sm-2 control-label">抵用<?= $setting('score.title', '积分') ?></label>
  <div class="col-sm-10">
    <p class="form-control-plaintext">
      <% if (!config.memberUseScore) { %>
        -
      <% } else { %>
        使用<%= config.memberUseScore.useScore %><?= $setting('score.title', '积分') ?>抵用<%=
          config.memberUseScore.reduceMoney %>元
      <% } %>
      <?php ?>
    </p>
  </div>
</div>
