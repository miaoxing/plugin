<?php $view->layout() ?>

<div class="page-header">
  <h1>
    插件管理
  </h1>
</div>
<!-- /.page-header -->

<div class="row">
  <div class="col-xs-12">
    <!-- PAGE CONTENT BEGINS -->
    <div class="table-responsive">
      <table id="record-table" class="record-table table table-bordered table-hover">
        <thead>
        <tr>
          <th>名称</th>
          <th>标识</th>
          <th>版本</th>
          <th>描述</th>
          <th>安装</th>
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

<script id="built-in-tips-tpl" type="text/html">
  <span title="内置插件,无需安装">-</span>
</script>

<script id="table-actions" type="text/html">
  <div class="action-buttons">
    <a href="<%= $.url('admin/plugin/show', {id: id}) %>"
      target="_blank" title="查看">
      <i class="fa fa-search-plus bigger-130"></i>
    </a>
  </div>
</script>

<?php require $view->getFile('admin:admin/checkboxCol.php') ?>

<?= $block('js') ?>
<script>
  require(['dataTable', 'form', 'jquery-deparam'], function () {
    var recordTable = $('#record-table').dataTable({
      dom: "t<'row'<'col-sm-12'ir>>",
      displayLength: 99999,
      ajax: {
        url: $.queryUrl('admin/plugin.json')
      },
      columns: [
        {
          data: 'name',
          render: function (data, type, full) {
            return data || full.id
          }
        },
        {
          data: 'id'
        },
        {
          data: 'version'
        },
        {
          data: 'description'
        },
        {
          data: 'installed',
          render: function (data, type, full) {
            return full.builtIn ? $('#built-in-tips-tpl').html() : template.render('checkbox-col-tpl', {
              id: full.id,
              name: 'installed',
              value: data
            });
          }
        }
      ]
    });

    // 切换状态
    recordTable.on('click', '.toggle-status', function () {
      var $this = $(this);
      var data = {};
      data['id'] = $this.data('id');
      data[$this.attr('name')] = +!$this.data('value');
      $.post($.url('admin/plugin/update'), data, function (ret) {
        $.msg(ret);
        recordTable.reload();
      }, 'json');
    });
  });
</script>
<?= $block->end() ?>
