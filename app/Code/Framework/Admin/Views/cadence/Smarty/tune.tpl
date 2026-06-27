{assign var=cadence_data value=$cadence->coalesce()}
<style type="text/css">
    .cadence_toggle_slider {
        border: 1px solid #555; 
        border-radius: 12px;
        display: inline-block;
        background-color: rgba(202,202,202,.2);
        width: 40px;
        height: 6px;
        vertical-align: middle
    }
    .cadence_toggle_label {
        font-family: sans-serif; 
        font-size: 10pt
    }
    .cadence_toggle_stop {
        font-family: sans-serif; 
        padding-top: 16px; 
        font-size: 22pt; 
        font-weight: bold
    }
    .cadence_toggle_pointer {  
        height: 16px;
        margin-top: -2px 
    }   
    .cadence_period_pointer {  
        height: 28px;
        margin-top: 0px 
    }  
    .master_period_pointer {  
        height: 28px;
        margin-top: 0px 
    }     
    .cadence_range_slider {
        display: inherit; 
        border: 1px solid #777; 
        border-radius: 6px;
        background-color: black
            
    }   
    .cadence_period_range {
        display: inherit; 
        border: 1px solid #777; 
        border-radius: 6px;
        background-color: black
            
    }       
    .cadence_period_slider {
        display: inline-block; 
        width: 250px; 
        margin-right: 10px; 
        height: 20px; 
        border: 1px solid #333; 
        border-radius: 5px; 
        vertical-align: middle        
    }
</style>
<form name="cadence_tailoring_form" onsubmit="return false" nohref style='vertical-align: top'>
    <div id="win_{$window_id}_header" style="box-sizing: border-box; background-color: #333; color: ghostwhite; padding: 0px 5px; font-size: 1.5em">
        <br />
        <div style='float: right'><b><i>Cadence</i></b> - Unified Watch Program</div>
        Control Panel
    </div>
    <div id="win_{$window_id}_body"   style="box-sizing: border-box; overflow: auto; display: flex; flex-wrap: nowrap">
        <table style="width: 100%; height: 100%" cellspacing="0" cellpadding="0">
            <tr>
                <td style="min-width: 30%; background-image: url(/images/paradigm/bg_graph.png)">&nbsp;</td>
                <td style="vertical-align: top; min-width: 800px"><div style="padding: 10px 0px 0px 0px;background-color: rgba(200,200,200,.3); vertical-align: top">
                        <fieldset style='padding: 10px 0px; width: 49%; display: inline-block'><legend>Log Information</legend>
                            <input type='text' name='log_location' value='{$cadence->getLogLocation()}' style='width: 75%; padding: 2px; border: 1px solid #333'/><br />
                            <div style='font-family: monospace; font-size: .85em; letter-spacing: 2px; padding-bottom: 15px'>Log Location</div>

                            <select name='cadence_log_size' style='padding: 2px;  border: 1px solid #333; font-family: monospace; width: 75%'> 
                                <option value='50K'> 50K </option>
                                <option value='100K'> 100K </option>
                                <option value='500K'> 500K </option>
                                <option value='1M'> 1M </option>
                                <option value='5M'> 5M </option>
                                <option value='10M'> 10M </option>
                            </select>
                            <div style='font-family: monospace; font-size: .85em; letter-spacing: 2px; padding-bottom: 15px'>Maximum Log Size</div>
                        </fieldset>
                        <fieldset style='padding: 10px 0px; width: 49%; display: inline-block'><legend>Cadence Master</legend>
                            <select name="cadence_profile" style="width: 100%; padding: 2px 4px; border: 1px solid #333" placeholder="Cadence Profile"> 
                                <option value=""> </option>
                                <option value="Development"> Development/Testing </option>
                                <option value="Production"> Production </option>
                            </select>
                            <div style='font-family: monospace; font-size: .85em; letter-spacing: 2px'>Default Profile</div>
                            <div style='height: 15px'></div>                            
                            <div id='cadence_master_period'         style='display: inline-block'></div>
                            <div id='cadence_master_period_display' style='display: inline-block'>{$cadence->getPeriod()}</div>
                            <div style='font-family: monospace; font-size: .85em; letter-spacing: 2px'>Periodic Pulse (in seconds)</div>
                        </fieldset>                    
                        <script type='text/javascript'>
                            var Cadence = {};
                            EasySlider.Control.init();
                            (()=> {
                                var period = +'{$cadence->getPeriod()}';
                                var slider = new EasySlider("cadence_master_period",300,20,"s_master_period");
                                slider.setSlideClass("cadence_period_slider");
                                slider.setLabelClass("cadence_toggle_label");
                                slider.setStopClass("cadence_toggle_stop");
                                slider.setStopText("");
                                slider.setSlideRanges("true");
                                slider.setRangeDirection("left");
                                slider.setRangeClass("cadence_period_range");
                                slider.setMaxScale(20).setCanClick(true);;
                                slider.setOnSlide((slider, range, fromLeft) => {
                                    document.getElementById('cadence_master_period_display').innerHTML = EasySliders[range.id].getValue();
                                });
                                slider.setInclusive(true); 
                                slider.setSnap(true)
                                slider.setCanClick(true);
                                slider.setRounding(true);
                                slider.setScale(1,20,20);
                                slider.addPointer("sl_master_period","/images/admin/slider_3.png",null,'master_period_pointer');
                                slider.render();
                                slider.setSliderToValue(period);
                            })();
                        </script>                                

                        <div style='height: 15px'></div>
                        {assign var=cadence_data value=[]}
                    </div><div style="font-size: 1.25em; font-family: monospace; background-color: rgba(202,202,202,.1); margin-left: auto; margin-right: auto; width: 100%">
                         {foreach from=$cadence_data item=funcs key=namespace}
                            <script type='text/javascript'>Cadence['{$namespace}'] = {  };</script>
                            <div style="padding-left: 40px; background-color: rgba(200,200,200,.3); height: 35px; vertical-align: middle">{$namespace|ucfirst}</div>
                                {foreach from=$funcs.handlers item=callback key=topic}
                                    <div style="padding-left: 80px; background-color: rgba(200,200,200,.5); height: 35px; vertical-align: middle">{$topic|ucfirst}</div>
                                        {foreach from=$callback item=callback key=area}
                                            {assign var=multiple value=$callback.multiple}
                                            <script type='text/javascript'>Cadence['{$namespace}']['{$area}'] = {
                                                "handlers": {},
                                                "multiple": {$multiple}
                                            };</script>
                                            <div style="padding-left: 120px; padding-bottom: 4px; background-color: rgba(200,200,200,.7); height: 35px; vertical-align: middle">
                                                <div style="display: inline-block; width: 175px; height: 30px; padding: 4px; vertical-align: middle"><b>{$area|ucfirst}</b></div>
                                                <div id='slide_{$namespace}_{$area}'    style="display: inline-block"></div>
                                                <div id='period_{$namespace}_{$area}'   style="display: inline-block; vertical-align: middle"> {$multiple} </div>
                                                <script type='text/javascript'>
                                                    (()=> {
                                                        var slider = new EasySlider("slide_{$namespace}_{$area}",250,22,"s_{$namespace}_{$area}");
                                                        var multiple = +'{$multiple}';
                                                        slider.setSlideClass("cadence_period_slider");
                                                        slider.setLabelClass("cadence_toggle_label");
                                                        slider.setStopClass("cadence_toggle_stop");
                                                        slider.setStopText("");
                                                        slider.setSlideRanges("true");
                                                        slider.setRangeDirection("left");
                                                        slider.setRangeClass("cadence_period_range");
                                                        slider.setMaxScale(25);
                                                        slider.setCanClick(true);
                                                        slider.setOnSlide((slider, range, fromLeft) => {
                                                            document.getElementById('period_{$namespace}_{$area}').innerHTML = EasySliders[range.id].getValue();
                                                            //let color = (fromLeft > 20) ? '#0FFF50' : 'darkgray';
                                                            //$(range).css('background-color',color);
                                                        });
                                                        slider.setInclusive(true); 
                                                        slider.setSnap(true)
                                                        slider.setCanClick(true);
                                                        slider.setRounding(true);
                                                        slider.setScale(1,25,25);
                                                        slider.addPointer("sl_{$area}_pointer","/images/admin/slider_3.png",null,'cadence_period_pointer');
                                                        slider.render();
                                                        console.log('----------------------------------------------------');                                
                                                        console.log('Setting: cadence_period_slider');         
                                                        console.log('multiple: '+multiple);
                                                        //slider.setSliderToValue(multiple);
                                                    })();
                                                </script>                                
                                            </div>
                                            <div>
                                                {foreach from=$callback.callbacks item=enable key=cb}
                                                <div style="padding-left: 160px; background-color: rgba(200,200,200,.9); height: 30px; vertical-align: middle">
                                                    <div style="display: inline-block; width: 345px">
                                                        <a href="javascript:Administration.cadence.explain('{$namespace}','{$cb}'); return false" style='color: blue'>{$cb}</a>
                                                    </div>
                                                    <div id='slide_{$area}_{$cb}' style='display: inline-block'></div>
                                                    <script type='text/javascript'>
                                                        Cadence['{$namespace}']['{$area}']['handlers']['{$cb}'] = '{$enable}';
                                                        (()=> {
                                                            var enable = ('{$enable}' === '1') ? 1 : 0;
                                                            var slider = new EasySlider("slide_{$area}_{$cb}",40,8,"s_{$area}_{$cb}");
                                                            slider.setSlideClass("cadence_toggle_slider");
                                                            slider.setLabelClass("cadence_toggle_label");
                                                            slider.setStopClass("cadence_toggle_stop");
                                                            slider.setStopText("");
                                                            //slider.setSlideRanges("true");
                                                            //slider.setRangeDirection("left");
                                                            //slider.setRangeClass("cadence_range_slider");
                                                            slider.setMaxScale(1).setCanClick(true);;
                                                            slider.setOnSlide((slider, range, fromLeft) => {
                                                                let color = (fromLeft > 20) ? '#0FFF50' : 'darkgray';
                                                                $(range).css('background-color',color);
                                                            });
                                                            slider.setInclusive(true); 
                                                            slider.setSnap(true)
                                                            slider.setRounding(true);
                                                            slider.setScale(0,1,2);
                                                            slider.addPointer("sl_{$cb}_pointer","/images/admin/circle_pointer.png",null,'cadence_toggle_pointer');
                                                            slider.render();
                                                            console.log('Setting slider on/off toggle');                                                            
                                                            slider.setSliderToValue(enable);
                                                        })();
                                                    </script>
                                                </div>
                                                {/foreach}
                                            </div>
                                        {/foreach}
                                {/foreach}
                        {/foreach}
                    </div>                    
                </td>
                <td style="min-width: 30%; background-image: url(/images/paradigm/bg_graph.png)">&nbsp;</td>
            </tr>
        </table>
    </div>
    <div id="win_{$window_id}_footer" style="box-sizing: border-box; background-color: #333; color: ghostwhite; padding: 0px 10px; text-align: center">
        <div style='position: absolute; bottom: 2px; right: 5px'>&copy; 2007-Present, Humbleprogramming.com
        </div><input type='button' style='position: relative; background-color: #d7d7d7; font-size: 1.25em; padding: 3px; color: #333; width: 90px; border-radius: 3px; border: 1px solid black' name='cadence_apply_button' value='  Apply  '/>
    </div>
</form>    
<script type="text/javascript">
    (($) => {
        let win     = Desktop.window.list['{$window_id}'];
        let header  = $('#win_{$window_id}_header').get();
        let winbody = $('#win_{$window_id}_body').get();
        let footer  = $('#win_{$window_id}_footer').get();
        //alert(win.content.height());

        win.resize((evt) => {
            $(winbody).height(win.content.height() - $(header).height() - $(footer).height());
        });
        /*
           $('#form_id [name=cadence_apply_button]').on('click',() => {
            let win = Desktop.window.list['{$window_id}'];
            //confirm then submit to back end
             
           });
         
         */
        console.log(Cadence);
    })($);
</script>
