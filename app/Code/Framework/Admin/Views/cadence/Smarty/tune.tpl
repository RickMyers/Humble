{assign var=cadence value=$cadence->coalesce()}
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
<div id="win_{$window_id}_header" style="box-sizing: border-box; background-color: #333; color: ghostwhite; padding: 0px; text-align: right; font-size: 1.5em">
    <br />
    <b><i>Cadence</i></b> - Unified Watch Program
</div>
<div id="win_{$window_id}_body"   style="box-sizing: border-box; overflow: auto; display: flex; flex-wrap: nowrap">
    <table style="width: 100%; height: 100%" cellspacing="0" cellpadding="0">
        <tr>
            <td style="min-width: 30%; background-image: url(/images/paradigm/bg_graph.png)">&nbsp;</td>
            <td style="vertical-align: top"><div style="font-size: 1.25em; font-family: monospace; background-color: rgba(202,202,202,.1); margin-left: auto; margin-right: auto; width: 100%">
                     {foreach from=$cadence item=funcs key=namespace}
                         <div style="padding-left: 40px; background-color: rgba(200,200,200,.3); height: 35px; vertical-align: middle">{$namespace|ucfirst}</div>
                             {foreach from=$funcs.handlers item=callback key=topic}
                                 <div style="padding-left: 80px; background-color: rgba(200,200,200,.5); height: 35px; vertical-align: middle">{$topic|ucfirst}</div>
                                     {foreach from=$callback item=callback key=area}
                                         {assign var=multiple value=$callback.multiple}
                                         <div style="padding-left: 120px; padding-bottom: 4px; background-color: rgba(200,200,200,.7); height: 35px; vertical-align: middle">
                                             <div style="display: inline-block; width: 125px; height: 30px; padding: 4px; vertical-align: middle">
                                                 <b>{$area|ucfirst}</b> 
                                             </div>
                                             <div id='slide_{$namspace}_{$area}' style="display: inline-block"></div>
                                             <div id='period_{$namspace}_{$area}' style="display: inline-block; vertical-align: middle"> {$multiple} </div>
                                             <script type='text/javascript'>
                                                 (()=> {
                                                     console.log('{$multiple}');
                                                     var multiple = {$multiple};
                                                     var slider = new EasySlider("slide_{$namspace}_{$area}",250,20,"s_{$namespace}_{$area}");
                                                     slider.setSlideClass("cadence_period_slider");
                                                     slider.setLabelClass("cadence_toggle_label");
                                                     slider.setStopClass("cadence_toggle_stop");
                                                     slider.setStopText("");
                                                     slider.setSlideRanges("true");
                                                     slider.setRangeDirection("left");
                                                     slider.setRangeClass("cadence_period_range");
                                                     slider.setMaxScale(25).setCanClick(true);;
                                                     slider.setOnSlide((slider, range, fromLeft) => {
                                                         document.getElementById('period_{$namspace}_{$area}').innerHTML = EasySliders[range.id].getValue();
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
                                                     slider.setSliderToValue({$multiple});
                                                 })();
                                             </script>                                
                                         </div>
                                         <div>
                                             {foreach from=$callback.callbacks item=enable key=cb}
                                             <div style="padding-left: 160px; background-color: rgba(200,200,200,.9); height: 30px; vertical-align: middle">
                                                 <div style="display: inline-block; width: 270px">
                                                 {$cb}
                                                 </div>
                                                 <div id='slide_{$namespace}_{$cb}' style='display: inline-block'></div>
                                                 <script type='text/javascript'>
                                                     (()=> {
                                                         var slider = new EasySlider("slide_{$namespace}_{$cb}",40,8,"s_{$namespace}_{$cb}");
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
                                                         slider.setSliderToValue({$enable});
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
    

<div id="win_{$window_id}_footer" style="box-sizing: border-box; background-color: #333; color: ghostwhite; text-align: right; padding: 0px">
    <br />
    &copy; 2007-Present, Humbleprogramming.com
</div>
<script type="text/javascript">
    (($) => {
        let win     = Desktop.window.list['{$window_id}'];
        let header  = $('#win_{$window_id}_header').get();
        let winbody = $('#win_{$window_id}_body').get();
        let footer  = $('#win_{$window_id}_footer').get();
        //alert(win.content.height());

        win.resize((evt) => {
            $(winbody).height(win.content.height() - $(header).height() - $(footer).height() - 2);
        });
    })($);
</script>
