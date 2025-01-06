/**
 * Pagination helper.  If you follow the naming convention it can manage the pagination controls for you
 *
 * @type Pagination_L4.PaginationAnonym$0|Function
 */
var Pagination = ( ($) => {
        var defaults = {
            page: 1,
            rows: 25
        };
        return {
            data: {},
            /**
             * Name the buttons as you see below
             *  
             * @param string base_id
             * @param function handler
             * @param int page
             * @param int rows
             * @param string win
             * @param boolean execute
             * @returns void
             */
            init: function (base_id,handler,page,rows,win,execute) {
                page = page ? page : defaults.page;
                rows = rows ? rows : defaults.rows;
                win  = win  ? win  : false;
                execute = (execute===false) ? false : true;
                $('#'+base_id+'-previous').on('click',function () {
                    if (Pagination.data[base_id]) {
                        Pagination.data[base_id].pages.current =  (Pagination.data[base_id].pages.current > 1) ? --Pagination.data[base_id].pages.current : Pagination.data[base_id].pages.total;
                        handler(Pagination.data[base_id].pages.current,rows,win);
                    }
                });
                $('#'+base_id+'-first').on('click',function () {
                    if (Pagination.data[base_id]) {
                        Pagination.data[base_id].pages.current = 1;
                        handler(Pagination.data[base_id].pages.current,rows,win);
                    }
                });
                $('#'+base_id+'-last').on('click',function () {
                    if (Pagination.data[base_id]) {
                        Pagination.data[base_id].pages.current = Pagination.data[base_id].pages.total;
                        handler(Pagination.data[base_id].pages.current,rows,win);
                    }
                });
                $('#'+base_id+'-next').on('click',function () {
                    if (Pagination.data[base_id]) {
                        Pagination.data[base_id].pages.current = (Pagination.data[base_id].pages.current < Pagination.data[base_id].pages.total) ? ++Pagination.data[base_id].pages.current : 1;
                        handler(Pagination.data[base_id].pages.current,rows,win);
                        console.log(Pagination.data);
                    }
                });
                if (execute) {
                    handler(page,rows,win);
                }
            },
            /**
             * Follow the naming convention and the display values will be set for you during pagination
             *
             * @param string base_id
             * @param array data
             * @returns void
             */
            set: function (base_id,data) {
                Pagination.data[base_id] = data;
                var f;
                if (f = $E(base_id+'-page')) {
                    $(f).html(data.pages.current);
                }
                if (f = $E(base_id+'-pages')) {
                    $(f).html(data.pages.total);
                }
                if (f = $E(base_id+'-from-row')) {
                    $(f).html(data.rows.from);
                }
                if (f = $E(base_id+'-to-row')) {
                    $(f).html(data.rows.to);
                }
                if (f = $E(base_id+'-rows')) {
                    $(f).html(data.rows.total);
                }
            },
            get: function(base_id) {
                return Pagination.data[base_id];
            }
        };
    })($);
