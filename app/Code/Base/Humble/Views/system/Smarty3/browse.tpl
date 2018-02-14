{assign var=application value=Humble::status()}
<style type='text/css'>
    #system-status-table tr td {
        background-color: white; border: 1px solid #c3c3c3; padding: 5px
    }
    #system-status-table tr td div input {
        background-color: lightcyan;
    }
    .system-stats-desc {
        font-size: .8em; letter-spacing: 2px; font-family: monospace, serif;
    }
    .system-stats-field {
        font-size: 1em; text-align: right; padding-right: 2px; font-family: sans-serif
    }
</style>
<br />
<h2>System Status</h2>
<hr />
<br />
<form onsubmit='return false'>
    <table id="system-status-table" width='500' border='0' cellspacing='1'>
        <tr>
            <td colspan="2" style="background-color: #0e90d2; color: white; padding: 15px 5px; text-align: justify; font-size: .9em; font-weight: bolder">
                Here you can set or update the system name, version, and whether you can run the installer or not.
                You can also take the system offline by unchecking the status checkbox below.  When you do that, only a
                user with the role of Super-User can bring the system back online.
            </td>
        </tr>
        <tr>
            <td width='50%'>
                <div class='system-stats-desc'>Name</div>
                <div class='system-stats-field'><input disabled type="text" name="system-version" id="system-version" style="border: 1px solid #aaf; padding: 2px; width: 100px" value="{$application->name}" /></div>
            </td>
            <td>
                <div class='system-stats-desc'>Version</div>
                <div class='system-stats-field'><input disabled type="text" name="system-version" id="system-version" style="border: 1px solid #aaf; padding: 2px; width: 100px" value="{$application->version}" /></div>
            </td>
        </tr>
        <tr>
            <td width='50%'>
                <div class='system-stats-desc'>Status</div>
                <div class='system-stats-field'><input disabled type="checkbox" name="system-enabled" id="system-enabled" value="1" {if ($application->status->enabled == 1)}checked{/if} /></div>
            </td>
            <td>
                <div class='system-stats-desc'>Installer</div>
                <div class='system-stats-field'><input disabled type="checkbox" name="system-installer" id="system-installer" value="1" {if ($application->status->installer == 1)}checked{/if} /></div>
            </td>
        </tr>
        <tr>
            <td>
                <div class='system-stats-desc'>Periodic Polling</div>
                <div class='system-stats-field'><input disabled type="checkbox" name="poll-enabled" id="poll-enabled" value="1" {if ($application->status->polling == 1)}checked{/if} /></div>
            </td>
            <td>
                <div class='system-stats-desc'>Interval</div>
                <div class='system-stats-field'>{$application->status->interval} Seconds</div>
            </td>
        </tr>
        <tr>
            <td width='50%'>
                <div class='system-stats-desc'>SSO Enabled</div>
                <div class='system-stats-field'><input disabled type="checkbox" name="sso-enabled" id="sso-enabled" value="1" {if ($application->status->SSO->enabled == 1)}checked{/if} /></div>
            </td>
            <td width='50%'>
                <div class='system-stats-desc'>Authorization Engine</div>
                <div class='system-stats-field'><input disabled type="checkbox" name="authorization-enabled" id="authorization-enabled" value="1" {if ($application->status->authorization->enabled == 1)}checked{/if} /></div>
            </td>
        </tr>
        <tr>
            <td colspan='2'width='50%'>
                <div class='system-stats-desc'>Landing URI</div>
                <div class='system-stats-field'><input disabled type="text" name="system-landing" id="system-landing" value='{$application->landing}' /></div>
            </td>
        </tr>
        <tr>
            <td colspan='2'width='50%'>
                <div class='system-stats-desc'>Login URI</div>
                <div class='system-stats-field'><input disabled type="text" name="system-landing" id="system-landing" value='{$application->login}' /></div>
            </td>
        </tr>
        <tr>
            <td colspan='2'width='50%'>
                <div class='system-stats-desc'>Logout URI</div>
                <div class='system-stats-field'><input disabled type="text" name="system-landing" id="system-landing" value='{$application->logout}' /></div>
            </td>
        </tr>
    </table>
</form>
<br />