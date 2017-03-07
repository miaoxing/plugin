<?= $block('html') ?>
<script id="checkbox-col-tpl" type="text/html">
    <label>
        <input class="js-toggle-status ace toggle-status" name="<%= name %>" data-id="<%= id %>" data-value="<%= value %>" type="checkbox" <% if (value == 1) { %> checked <% } %> >
        <span class="lbl"></span>
    </label>
</script>
<?= $block->end() ?>
