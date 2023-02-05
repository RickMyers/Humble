<style type="text/css">
</style>
<div style="">
    
    <form nohref onsubmit="return false">
        <fieldset><legend>Instructions</legend>
            <img src="/images/paradigm/import_export.png" style="float: left; margin-right: 10px; height: 100px" />
            <div style="font-size: 1.2em; font-weight: bolder; padding-bottom: 20px ">
                Exporting/Importing Workflows and Token Management
            </div>
            From this area you can manage your export tokens and set your import tokens.  Environments are mapped, export -&gt; import, so the import field should have the same token as the corresponding export field.
            These tokens are used in authentication when publishing (a.k.a. exporting) a workflow to another environment.  The Environment URL is mapped along with the token to allow the export from one environment and 
            the import to another environment to take place.
        </fieldset>
    </form>
</div>
<div id="paradigm_manage_sources_nav">
</div>
<div id="paradigm_manage_targets">
</div>
<div id="paradigm_manage_sources">
</div>
<script type="text/javascript">
    (function () {
        let tabs = new EasyTab('paradigm_manage_sources_nav',160);
        tabs.add('Export Targets',function () {
            (new EasyAjax('/paradigm/workflow/targets')).add('window_id','{$window_id}').then(function (response) {
                $('#paradigm_manage_targets').html(response);
            }).post();
        },'paradigm_manage_targets');
        tabs.add('Import Sources',function () {
            (new EasyAjax('/paradigm/workflow/sources')).add('window_id','{$window_id}').then(function (response) {
                $('#paradigm_manage_sources').html(response);
            }).post();
        },'paradigm_manage_sources');
        tabs.tabClick(0);
    })();
</script>

