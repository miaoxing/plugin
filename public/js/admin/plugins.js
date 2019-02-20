define(['template', 'plugins/admin/js/data-table', 'form', 'jquery-deparam'], function (template) {
  var Plugins = function () {
    // do nothing.
  };

  Plugins.prototype.indexAction = function () {
    var recordTable = $('#record-table').dataTable({
      dom: 't<\'row\'<\'col-sm-12\'ir>>',
      displayLength: 99999,
      ajax: {
        url: $.queryUrl('admin/plugin.json')
      },
      columns: [
        {
          data: 'name',
          render: function (data, type, full) {
            return data || full.id;
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
      data[$this.attr('name')] = Number(!$this.data('value'));
      $.post($.url('admin/plugin/update'), data, function (ret) {
        $.msg(ret);
        recordTable.reload();
      }, 'json');
    });
  };

  return new Plugins();
});
