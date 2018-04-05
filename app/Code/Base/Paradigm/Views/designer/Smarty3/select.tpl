<style type="text/css">
    .form-select-row {
        white-space: nowrap; margin-bottom: 2px
    }
    .form-select-name {
        width: 25%; overflow: hidden; padding: 2px; margin-right: .1%; display: inline-block; border-right: 1px solid rgba(50,50,50,.4)
    }
    .form-select-description {
        width: 75.5%; overflow: hidden; padding: 2px; display: inline-block;
    }
</style>
<table style="width: 100%; height: 100%">
    <tr>
        <td colspan="3" height="50" style="background-color: #333; color: ghostwhite; font-size: 1.5em">
            Please choose a form to load from the left pane
        </td>
    </tr>
    <tr>
        <td width="49.5%" id="designer-available-forms">
        </td>
        <td width="1%" style="background-color: #333"></td>
        <td width="49.5%" align="center"><img src="" style="display: none; height: 400px" id="designer-form-preview" onload="this.style.display = 'block'" /></td>
    </tr>
    <tr>
        <td colspan="3" height="30" align="center">
            <form onsubmit="return false">
                <div style="float: left">
                    Row <span id="form-preview-from-row"></span> to <span id="form-preview-to-row"></span> of <span id="form-preview-rows"></span>
                </div>
                <div style="float: right">
                    Page <span id="form-preview-page"></span> of <span id="form-preview-pages"></span>&nbsp;
                </div>
            <input type="button" value=" < " name="form-preview-previous" id="form-preview-previous" />
            <input type="button" value=" << " name="form-preview-first" id="form-preview-first" />
            <input type="button" value=" >> " name="form-preview-last" id="form-preview-last" />
            <input type="button" value=" > " name="form-preview-next" id="form-preview-next" />
            </form>
        </td>
    </tr>
</table>
<script type="text/javascript">
    Pagination.init('form-preview',function (page,rows) {
       (new EasyAjax('/paradigm/designer/forms')).add('page',page).add('rows',rows).thenfunction (response) {
           $('#designer-available-forms').html(response);
           Pagination.set('form-preview',this.getPagination());
       }).get();
    },1,10);
</script>