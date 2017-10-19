<div class="form-group">
  <label class="col-sm-2 control-label">抵用<?= $setting('score.title', '积分') ?></label>
  <div class="col-sm-10">
    <p class="form-control-static">
      <% if (!config.member_use_score) { %>
        -
      <% } else { %>
        使用<%= config.member_use_score.use_score %><?= $setting('score.title', '积分') ?>抵用<%=
          config.member_use_score.reduce_money %>元
      <% } %>
      <?php ?>
    </p>
  </div>
</div>
