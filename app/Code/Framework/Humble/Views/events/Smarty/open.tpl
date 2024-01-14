<style type="text/css">
    #events-viewer-pagination {
        text-align: center; position: relative; box-sizing: border-box; padding: 5px 2px
    }
    #events-viewer-header {
        background-color: #333; color: ghostwhite; position: relative; box-sizing: border-box; font-size: 1.5em; padding: 5px 2px;
    }
    #events-viewer-footer {
        background-color: #333; color: ghostwhite; position: relative; box-sizing: border-box; font-size: .8em; padding: 5px 2px;
    }
    #events-viewer-body {
        box-sizing: border-box;
    }
</style>
<div id="events-viewer-header">
    The Humble Project Events Viewer
</div>
<div id="events-viewer-body">
    <div style="width: 49%; margin-right: 1%; height: 100%; overflow: auto; display: inline-block" id="humble-event-list"></div>
    <div style="width: 49%; height: 100%; overflow: auto; display: inline-block" id="humble-event-detail"></div>
</div>
<div id="events-viewer-pagination">
    <div style="float: right; margin-right: 5px">
        <span id="event-viewer-from-row"></span> to <span id="event-viewer-to-row"></span> of <span id="event-viewer-rows"></span>
    </div>
    <input type="button" id="event-viewer-previous" style="color: #333; width: 50px;" value="<" />
    <input type="button" id="event-viewer-first" style="color: #333; width: 55px" value="<<" />
    <div style="display: inline-block; width: 120px; text-align: center">
        <span id="event-viewer-page"></span> of <span id="event-viewer-pages"></span>
    </div>
    <input type="button" id="event-viewer-last" style="color: #333; width: 55px" value=">>" />
    <input type="button" id="event-viewer-next" style="color: #333; width: 50px" value=">" />
</div>
<div id="events-viewer-footer">
    &copy; 2012-present, The Humble Project, all rights reserved
</div>
<script type="text/javascript">
    var win         = Desktop.window.list['{$window_id}'];
    win.content.style.overflow = 'auto';
    win.content.style.boxStyle = 'border-box';
    win.header      = $E('events-viewer-header');
    win.body        = $E('events-viewer-body');
    win.pagination  = $E('events-viewer-pagination');
    win.footer      = $E('events-viewer-footer');
    win.eventList   = $E('event-list');
    win.eventViewer = $E('event-viewer');
    win.paginationId = 'event-viewer';
    win.timer       = null;
    win.refresh     = (function (win) {
        return function () {
            if ($E('event-list')) {
                Humble.admin.events.fetch(false,30,win);
                win.timer = window.setTimeout(win.refresh,10000);
            } else {
                window.clearTimeout(win.timer);
            }
        }
    })(win);
    win.resize      = (function (win) {
        return function () {
            win.body.style.height = (win.content.offsetHeight - win.header.offsetHeight - win.footer.offsetHeight - win.pagination.offsetHeight - 5) + "px";
        };
    })(win);
    win.resize();
    Pagination.init('event-viewer',Humble.admin.events.fetch,1,30,win);
    win.timer = window.setTimeout(win.refresh,10000);
</script>