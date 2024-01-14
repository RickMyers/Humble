<!DOCTYPE html>
<html>
    <head>
        <title>Service Authorization</title>
        <link type="text/css" rel="stylesheet" href="/css/common" />
        <style type="text/css">
            .body-background {
                background-image: url(/web/images/light.jpg); background-repeat: no-repeat; background-size: cover
            }
            .services-body-background {
                background-image: url(/images/core/services.png); background-repeat: no-repeat; background-position: -100px -100px; position: absolute; top: 0px; left: 0px; height: 100%; width: 100%
            }
            .services-display-area {
                width: 1600px; margin-left: auto; margin-right: auto; height: 100%; background-color: white; top: 0px; left: 0px
            }
            .services-header {
                color: navy; font-size: 2.2em; height: 50px
            }
        </style>
        <script type="text/javascript" src="/js/jquery"></script>
        <script type="text/javascript" src="/js/common"></script>
        <script type="text/javascript">
            var Services = (function () {
                return {
                    activate: function (whichOne) {
                        $('.form_checkboxes_'+whichOne).prop('disabled',false);
                        $('#edit_'+whichOne).css('display','none');
                        $('#save_'+whichOne).css('display','block');
                    },
                    save:   function (whichOne) {
                        $('.form_checkboxes_'+whichOne).prop('disabled',true);
                        $('#edit_'+whichOne).css('display','block');
                        $('#save_'+whichOne).css('display','none');
                        var elements = $E('roles_form_'+whichOne).elements;
                        var element;
                        var x = {
                            service: whichOne,
                            roles: []
                        }
                        for (var i=0; i<elements.length; i++) {
                            element = elements[i];
                            if (element.type && (element.type=='checkbox') && (element.checked)) {
                                x.roles[x.roles.length] = element.value;
                            }
                        }
                        (new EasyAjax('/humble/services/save')).add('data',JSON.stringify(x)).then((response) => {
                            alert(response);
                        }).post();
                    }
                }
            })();
        </script>
    </head>
    <body class="body-background">
        {assign var=roles value=$roles->fetch()}
        <div class="services-body-background">
            <div class="services-display-area" id='service-display-area'>
                <div style="font-size: 2em; font-weight: bold">Roles to Services Authorizations</div>
                {assign var=ctr value=0}
                <div style='border-bottom: 1px solid black;'>
                    <div style="float: left; width: 190px; height: 80px"></div>
                        {foreach from=$roles item=role}
                            <div id="box_{$ctr}" style="transform: rotate(60deg);width: 80px;  transform-origin: 0px 0px;  text-align: right; float: left; padding-right: 8px; font-size: .8em;  border-top: 1px solid black;">
                                {$role.alias}
                            </div>
                            {assign var=ctr value=$ctr+1}
                        {/foreach}
                    <div class='cf' style='clear: both'></div>
                </div>
                {foreach from=$services->fetch() item=service}
                    <div style="background-color: {cycle values="#e5e5e5,#f2f2ff"}">
                        <form id="roles_form_{$service.id}" name="roles_form_{$service.id}" onsubmit="return false">
                        <div style="float: left; width: 140px; border-right: 1px solid black; font-size: .7em; overflow: hidden">
                        {$service.URL}
                        </div>
                        {assign var=ctr value=0}
                        {foreach from=$roles item=role}
                            <div class="theboxes_{$ctr} row_{$service.id}" style="float: left; width: 80px; border-right: 1px solid black; text-align: right; ">
                                <input name="s_{$service.id}_{$role.id}" id="s_{$service.id}_{$role.id}" disabled class="form_checkboxes_{$service.id}" type="checkbox" value="{$role.id}" style="margin-right: 5px" />
                            </div>
                            {assign var=ctr value=$ctr+1}
                        {/foreach}
                        <div class="theboxes_{$ctr}" style="float: left; ">
                            <img id="edit_{$service.id}" src="/images/humble/edit.png" style="margin-left: 5px; cursor: pointer; height: 15px" onclick="Services.activate('{$service.id}')" />
                            <img id="save_{$service.id}" src="/images/humble/save.png" style="margin-left: 5px; cursor: pointer; height: 15px; display: none" onclick="Services.save('{$service.id}')" />
                        </div>
                        </form>
                        <div style="clear: both"></div>
                    </div>

                {/foreach}


            </div>
        </div>
        <script type="text/javascript">

            $(document).ready(function () {
                {for $n = 0 to $ctr}
                $(".theboxes_{$n}").width($('#box_{$n}').outerWidth());
                {/for}
            });
        </script>

    </body>
</html>