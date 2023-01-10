<style type="text/css">
    #unit-test-harness-header-{$window_id} {
        background-color: #333; color: ghostwhite; font-size: 1em; border-bottom: 1px solid #000; padding: 10px 5px
    }
    #unit-test-harness-content-{$window_id} {
        background-color: ghostwhite; color: #333; font-size: .9em; overflow: auto
    }
    #unit-test-harness-footer-{$window_id} {
        background-color: #333; color: ghostwhite; font-size: .8em; border-top: 1px solid #000;
    }
</style>
<form name="unit-test-harness-form" id="unit-test-harness-form-{$window_id}">
    <div id="unit-test-harness-header-{$window_id}">
        <input onclick='Landing.tests.run("{$window_id}")' id='unit-test-harness-run-{$window_id}' type='button' style='color: #333; padding: 2px 5px; float: right; margin-right: 2px' disabled='true' value='  Run  ' />
        Please Specify Harness Source: <input type="text" id='unit-test-harness-source-{$window_id}' style="width: 200px; background-color: lightcyan; color: #333; padding: 2px; border: 1px solid #aaf" value="tests/connect.xml" />
        <input type="button" value=" Load Tests" style="color: #333; padding: 2px 5px" onclick='Landing.tests.load("{$window_id}")'/>
    </div>
    <div id="unit-test-harness-content-{$window_id}">
    </div>
    <div id="unit-test-harness-footer-{$window_id}">
        &copy; 2007-present, Humble Project, all rights reserved
    </div>
</form>
<script type="text/javascript">
    let Landing = {  };
    Landing.tests = (function (win) {
        win.resize = function () {
            $E('unit-test-harness-content-{$window_id}').style.height = (this.content.offsetHeight - $E('unit-test-harness-header-{$window_id}').offsetHeight - $E('unit-test-harness-footer-{$window_id}').offsetHeight - 6)+'px';
        }
        win.resize();
        return {
            open: function () {
                var win = Desktop.semaphore.checkout(true);
                win._open();
                win._title('Test Harness');
                (new EasyAjax('/humble/unittests/open')).add('window_id',win.id).then(function (response) {
                    win.set(response);
                }).get();
            },
            load: function (window_id) {
                (new EasyAjax('/humble/unittests/load')).add('window_id',window_id).add('source',$('#unit-test-harness-source-'+window_id).val()).then(function (response) {
                    $('#unit-test-harness-content-{$window_id}').html(response);
                }).post();
            },
            run: function (window_id) {
                (new EasyAjax('/humble/unittests/run')).add('window_id',window_id).add('source',$('#unit-test-harness-source-'+window_id).val()).then(function (response) {
                    $('#unit-test-harness-content-{$window_id}').html(response);
                }).post();
            }
        }
    })(Desktop.window.list['{$window_id}']);
</script>