{assign var=application value=Environment::status()}
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
    .polling-interval-slide {
        height: 6px; border: 1px solid #333; border-radius: 2px; padding: 1px; background-color: rgba(202,202,202,.8);
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
                user with the role of Super-User can bring the system back online.<br /><br />
                If all else fails you can log in to the server and run the command line utility with the option of '--o' to toggle offline/online the system
            </td>
        </tr>
        <tr>
            <td width='50%'>
                <div class='system-stats-desc'>Name</div>
                <div class='system-stats-field'><input type="text" name="system-name" id="system-name" style="border: 1px solid #aaf; padding: 2px; width: 100px" value="{$application->name}" /></div>
            </td>
            <td>
                <div class='system-stats-desc'>Version</div>
                <div class='system-stats-field'><input type="text" name="system-version" id="system-version" style="border: 1px solid #aaf; padding: 2px; width: 100px" value="{$application->version}" /></div>
            </td>
        </tr>
        <tr>
            <td width='50%'>
                <div class='system-stats-desc'>Status</div>
                <div class='system-stats-field'><input type="checkbox" name="system-enabled" id="system-enabled" value="1" {if ($application->status->enabled == 1)}checked{/if} /></div>
            </td>
            <td>
                <div class='system-stats-desc'>Installer</div>
                <div class='system-stats-field'><input type="checkbox" name="system-installer" id="system-installer" value="1" {if ($application->status->installer == 1)}checked{/if} /></div>
            </td>
        </tr>
        <tr>
            <td width='50%'>
                <div class='system-stats-desc'>SSO Enabled</div>
                <div class='system-stats-field'><input type="checkbox" name="sso-enabled" id="sso-enabled" value="1" {if ($application->status->SSO->enabled == 1)}checked{/if} /></div>
            </td>
            <td width='50%'>
                <div class='system-stats-desc'>Authorization Engine</div>
                <div class='system-stats-field'><input type="checkbox" name="authorization-enabled" id="authorization-enabled" value="1" {if ($application->status->authorization->enabled == 1)}checked{/if} /></div>
            </td>

        </tr>
        <tr>
            <td>
                <div class='system-stats-desc'>Periodic Polling</div>
                <div class='system-stats-field'><input type="checkbox" name="poll-enabled" id="poll-enabled" value="1" {if ($application->status->polling == 1)}checked{/if} /></div>
            </td>
            <td>
                <div class='system-stats-desc'>Interval</div>
                <div class='system-stats-field'>
                    <div style="height: 20px; padding: 2px 4px 0px 4px">
                        <div id="polling-interval-slider"></div>
                    </div>
                    <script type="text/javascript">
                        var s=new Slider("polling-interval-slider",200,5,"interval-slider");
                        var f = function (slide,pointer,distance) {
                            var v = Sliders[pointer.id].getValue();
                        }
                        s.setOnSlideStop(f).setMaxScale(60);
                        s.setInclusive(true);
                        s.setSlideClass("polling-interval-slide");
                        s.setLabelClass("polling-interval-label");
                        s.addPointer("system_arrow","/images/humble/pointer.png");
                        s.render();
                        s.setSliderToValue('{$application->status->interval}',false);
                    </script>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan='2'width='50%'>
                <div class='system-stats-desc'>Landing URI</div>
                <div class='system-stats-field'><input disabled type="text" name="system-landing" id="system-landing" value='{$application->landing}' style="border: 1px solid #aaf; padding: 2px; width: 140px" /></div>
            </td>
        </tr>
        <tr>
            <td colspan='2'width='50%'>
                <div class='system-stats-desc'>Login URI</div>
                <div class='system-stats-field'><input disabled type="text" name="system-login" id="system-login" value='{$application->login}' style="border: 1px solid #aaf; padding: 2px; width: 140px" /></div>
            </td>
        </tr>
        <tr>
            <td colspan='2'width='50%'>
                <div class='system-stats-desc'>Logout URI</div>
                <div class='system-stats-field'><input disabled type="text" name="system-logout" id="system-logout" value='{$application->logout}'  style="border: 1px solid #aaf; padding: 2px; width: 140px" /></div>
            </td>
        </tr>
        <tr>
            <td colspan='2'>
                <input type="button" onclick="configuration.status.quiesce.start()" value="QUIESCE" style="padding: 3px 10px; float: right; margin-right: 5px" />
                <input type="button" onclick="configuration.status.save()" class="settingsButton" value=" Save " style="font-size: 1em; padding: 3px 10px" />
            </td>
        </tr>
    </table>
</form>
<br />