    <style type="text/css">
        .body-background {
            background-image: url(/images/paradigm/bg_graph.png); position: relative
        }
        .services-header {
            color: #333; font-size: 2.2em; height: 50px
        }
        .service-display {
            overflow: auto; background-color: ghostwhite
        }
        .services-field {
            padding-right: 5px; font-size: .9em; font-family: sans-serif; text-align: right; overflow: hidden; white-space: nowrap; padding-left: 10px
        }
        .services-field2 {
            padding: 2px 25px 0px 15px; font-size: .8em; font-family: monospace; text-align: left; white-space: nowrap;
        }
        .services-field-desc {
            font-family: sans-serif; font-size: .9em; letter-spacing: 2px; text-align: center; font-weight: bolder
        }
        .services-field-desc2 {
            font-family: sans-serif; font-size: .7em; letter-spacing: 1px; text-align: left; font-weight: bolder; padding-top: 10px
        }
    </style>
    <div id="services-directory-layer" class="body-background">
        <div class="services-body-background">
            <div class="services-display-area" id='service-display-area'>
                <div id="service-header" class="services-header">
                    <div style='float: right; white-space: nowrap; font-size: .5em; margin-right: 5px;'>
                        <form onsubmit="return false">
                            <div style="display: inline-block; width: 240px">
                                Search: <input type="text" name="search_services_text" id="search_services_text" style="width: 190px; background-color: lightcyan; color: #333; border: 1px solid #333; border-radius: 2px" />
                            </div>                            
                            <div style="display: inline-block; width: 180px">
                                Module: <select name="module_namespace" id="module_namespace" style="color: #333; width: 110px; background-color: lightcyan; padding: 2px; border: 1px solid #333; border-radius: 2px">
                                    <option value=""> </option>
                                    {foreach from=$modules->fetch() item=module}
                                        <option value="{$module.namespace}" title="{$module.description}"> {$module.module|ucfirst} </option>
                                    {/foreach}
                                </select>
                            </div>
                            <div style="display: inline-block; margin-right: 20px">
                                <input type="checkbox" name="hide_framework_services" id="hide_framework_services" value="Y" /> Hide Framework Services
                            </div>
                            <div style="display: inline-block">
                            Rows: <select id='service-rows' name='service-rows' style="background-color: lightcyan; color: #333; padding: 2px; border: 1px solid #333; border-radius: 2px">
                                <option value='10000'> All </option>
                                <option value='10'> 10 </option>
                                <option value='25'> 25 </option>
                                <option value='40' selected> 40 </option>
                                <option value='50'> 50 </option>
                                <option value='100'> 100 </option>
                            </select>
                            </div>
                        </form>
                    </div>
                    Service Directory
                </div>
                <div id="service-directory" class="service-display">
                    <!-- stuff goes here -->
                </div>
                <div id='service-controls' style='height: 30px'>
                    <table style="width: 100%; height: 100%; table-layout: fixed">
                        <tr height="20">
                            <td style="padding-left: 5px">Rows <span id='from-row'></span> thru <span id='to-row'></span> of <span id='total-rows'></span></td>
                            <td style="text-align: center">
                                <input type="button" id="services-first" value=" << " />
                                <input type="button" id="services-prev" value=" < " />
                                <input type="button" id="services-next" value=" > " />
                                <input type="button" id="services-last" value=" >> " />
                            </td>
                            <td style="text-align: right; padding-right: 5px">
                                Page <span id='current-page'></span> of <span id='total-pages'></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <script type="text/javascript">
    var Services = {
        rows:       0,
        fromRow:    0,
        toRow:      0,
        pages:      0,
        currentPage: 0,
        loaded:     {},
        page: {
            goto: function (page) {
                var hide = $('#hide_framework_services').prop('checked');
                (new EasyAjax('/humble/directory/services')).add('namespace',$('#module_namespace').val()).add('hide_framework_services',hide).add('page',page).add('rows',$('#service-rows').val()).then((response) => {
                    $('#service-directory').html(response);
                }).post();
            }
        },
        parms: function (service_id) {
            if (!Services.loaded[service_id]) {
                (new EasyAjax('/humble/directory/serviceparms')).add('service_id',service_id).then((response) => {
                    $('#service-'+service_id+'-parameters').html(response);
                    $('#service-'+service_id+'-parameters').slideToggle();
                    Services.loaded[service_id] = true;
                }).post();
            } else {
                $('#service-'+service_id+'-parameters').slideToggle();
            }
        }
    }
    $("#services-prev").on("click",function () {
        Services.currentPage = Services.currentPage -1;

        if (Services.currentPage<1) {
            Services.currentPage = Services.pages;
        }
       Services.page.goto(Services.currentPage);
    });
    $("#services-first").on("click",function () {
        Services.page.goto(1);
    });
    $("#services-last").on("click",function () {
        Services.page.goto(Services.pages);
    });
    $("#services-next").on("click",function () {
        Services.currentPage = Services.currentPage +1;
        if (Services.currentPage > Services.pages) {
            Services.currentPage=1;
        }
        Services.page.goto(Services.currentPage);
    });
    Services.page.goto(1);
</script>

