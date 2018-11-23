<html>
    <head>
        <link type="text/css" rel="stylesheet" href="/css/common" />
        <style type="text/css">
            .body-background {
                background-image: url(/web/images/light.jpg); background-repeat: no-repeat; background-size: cover
            }
            .services-body-background {
                background-image: url(/images/core/services.png); background-repeat: no-repeat; background-position: -100px -100px; position: absolute; top: 0px; left: 0px; height: 100%; width: 100%
            }
            .services-display-area {
                width: 80%; height: 80%; border: 1px solid #cecece; background-color: rgba(232,232,232,.8); margin-left: auto; margin-right: auto; margin-top: 5%
            }
            .services-header {
                color: navy; font-size: 2.2em; height: 50px
            }
            .service-display {
                overflow: auto
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
        <script type="text/javascript" src="/js/jquery"></script>
        <script type="text/javascript" src="/js/common"></script>
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
                        (new EasyAjax('/focos/directory/services')).add('page',page).add('rows',$('#service-rows').val()).callback(function (response) {
                            $('#service-directory').html(response);
                        }).post();
                    }
                },
                parms: function (service_id) {
                    if (!Services.loaded[service_id]) {
                        (new EasyAjax('/focos/directory/serviceparms')).add('service_id',service_id).callback(function (response) {
                            $('#service-'+service_id+'-parameters').html(response);
                            $('#service-'+service_id+'-parameters').slideToggle();
                            Services.loaded[service_id] = true;
                        }).post();
                    } else {
                        $('#service-'+service_id+'-parameters').slideToggle();
                    }
                }
            }
            $(document).ready(function () {
                Services.page.goto(1);
            });
        </script>
    </head>
    <body class="body-background">
        <div class="services-body-background">
            <div class="services-display-area" id='service-display-area'>
                <div id="service-header" class="services-header">
                    <div style='float: right; white-space: nowrap; font-size: .5em'>
                        <form>
                            Rows: <select id='service-rows' name='service-rows'>
                                <option value='10000'> All </option>
                                <option value='10'> 10 </option>
                                <option value='25'> 25 </option>
                                <option value='40' selected> 40 </option>
                                <option value='50'> 50 </option>
                                <option value='100'> 100 </option>
                            </select>
                        </form>
                    </div>
                    FOCoS Service Directory
                </div>
                <div id="service-directory" class="service-display">
                    <!-- stuff goes here -->
                </div>
                <div id='service-controls' style='height: 30px'>
                    <table style="width: 100%; height: 100%; table-layout: fixed">
                        <tr height="20">
                            <td>Rows <span id='from-row'></span> thru <span id='to-row'></span> of <span id='total-rows'></span></td>
                            <td align="center">
                                <input type="button" id="services-first" value=" << " />
                                <input type="button" id="services-prev" value=" < " />
                                <input type="button" id="services-next" value=" > " />
                                <input type="button" id="services-last" value=" >> " />
                            </td>
                            <td align="right">
                                Page <span id='current-page'></span> of <span id='total-pages'></span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </body>
    <script type="text/javascript">
    $(window).resize(function () {
        $('#service-directory').height($('#service-display-area').height() - $('#service-header').height() - $('#service-controls').height());
    });
    $(window).resize();
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
    </script>

</html>