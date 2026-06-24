{assign var=cadence value=$cadence->coalesce()}
<style type="text/css">
    
</style>
<div id="win_{$window_id}_header" style="box-sizing: border-box; background-color: #333; color: ghostwhite; padding: 0px; text-align: right; font-size: 1.5em">
    <br />
    <b><i>Cadence</i></b> - Unified Watch Program
</div>
<div id="win_{$window_id}_body"   style="box-sizing: border-box; overflow: auto; background-color: yellow">

    <div style="padding-left: 10px; font-size: 1.25em; font-family: monospace">
        {foreach from=$cadence item=funcs key=namespace}
            <div style="padding-left: 40px">{$namespace|ucfirst}</div>
                {foreach from=$funcs.handlers item=callback key=topic}
                    <div style="padding-left: 80px">{$topic|ucfirst}</div>
                        {foreach from=$callback item=callback key=area}
                            {assign var=multiple value=$callback.multiple}
                            <div style="padding-left: 120px; margin-bottom: 4px">
                                
                                <div style="display: inline-block; width: 125px; height: 20px; padding: 4px">
                                {$area|ucfirst} 
                                </div>
                                <div style="display: inline-block; width: 225px; margin-right: 10px; height: 20px; border: 1px solid #333; border-radius: 5px; vertical-align: middle"></div>
                                <div style="display: inline-block; vertical-align: middle"> {$multiple} </div>
                            </div>
                            <div style="margin-bottom: 10px">
                                {foreach from=$callback.callbacks item=enable key=cb}
                                <div style="padding-left: 160px; width: 700px">
                                    
                                    <div style="display: inline-block; width: 270px">
                                    {$cb}
                                    </div>
                                    <div style="display: inline-block; width: 40px; height: 20px; border: 1px solid #333; border-radius: 5px; background-color: navy; vertical-align: middle"></div>
                                </div>
                                    
                                {/foreach}
                            </div>
                        {/foreach}
                    {/foreach}
        {/foreach}
    </div>
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
<script type="text/javascript">
   var slider = new Slider("whiteboardReviewSlider",122,23,"reviewSlider");
    slider.setSlideClass("slideClass").setLabelClass("labelClass").setStopClass("stopClass").setStopText("");
    slider.setSlideRanges("true").setRangeDirection("left").setRangeClass("rangeClass").setMaxScale(5);
    slider.setOnSlide(function (slider, range, fromLeft) {   });
    slider.setInclusive(true).setSnap(true).setScale(0,5,10);
    slider.addPointer("wr_pointer","/pages/images/sliders/pointer2.gif");
    slider.render();     
    </script>