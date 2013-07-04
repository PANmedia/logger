window.prog = window.prog || {};
prog.profiler = prog.profiler || {};

prog.profiler = (function () {
    return {
        /**
         * Converts a formatted callback to enable multiline strings
         * 
         * @param  {closure} fn 
         * @return {string}
         */
        heredoc: function (fn) {
            return fn.toString().split('\n').slice(1,-1).join('\n') + '\n';
        },
        /**
         * CSS string for profiler
         * 
         * @return {void}
         */
        css: function () {
            return this.heredoc(function () {/*
#prog-profiler { position: fixed; bottom: 0; left: 0; color: #efefef; font-family: Verdana; font-size: 11px; width: 100%; z-index: 50000; }
#prog-profiler header { text-align: left; font-weight: normal; background: #720913; padding: 10px; border-right: 1px solid #3c0505; border-bottom: 1px solid #3c0505; border-top: 1px solid #bb4b56; color: #fff; text-shadow: 1px 1px 0 #720914; }
#prog-profiler nav ul { list-style: none; padding: 0; margin: 0 }
#prog-profiler nav li { display: inline-block; margin: 0 5px; }
#prog-profiler nav li.logo { margin-left: 20px; margin-right: 20px; margin-top: 7px; }
#prog-profiler nav li.nav { background: #3c0505; border-radius: 7px; display: inline-block; padding: 7px 10px; text-decoration: none; text-shadow: 0px 1px 0px #ab303c; }
#prog-profiler nav li.nav:active { position: relative; top: 1px; }
#prog-profiler nav a, #prog-profiler nav a:active, #prog-profiler nav a:visited, #prog-profiler nav a:hover { text-decoration: none; color: #efefef; }
.prog-table-data { height: 500px; display: none; overflow: auto; background: #efefef; }
.prog-table-data table { display: none; color: #222; font-family: Monaco, monospace; font-size: 11px; width: 100%; }
.prog-table-data th { text-align: left; font-weight: normal; background: #B81F2F; padding: 10px; border-left: 1px solid #bb4b56; border-right: 1px solid #720914; border-bottom: 1px solid #720914; border-top: 1px solid #bb4b56; color: #fff; text-shadow: 1px 1px 0 #720914; }
.prog-table-data td { background: #e8e8e8; padding: 10px; border-left: 1px solid #fff; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc; border-top: 1px solid #fff; }
.prog-table-data .query { width: 60%; }
.prog-table-data .query-time { width: 10%; }
.prog-table-data .query-memory { width: 10%; }
.prog-table-data .query-params { width: 20%; }
.prog-close-profiler li { display: none; float: right; }
            */});
        },
        /**
         * Inject CSS <style> tag in to the head of the page
         * 
         * @return {prog.profiler}
         */
        injectCss: function () {
            $('head').append(document.createElement('style'))
                     .find('style')
                     .append(this.css());

            return this;
        },
        /**
         * Attach all events
         * 
         * @return {prog.profiler}
         */
        attachEvents: function () {
            $('#prog-profiler nav').find('a').not('.prog-close-profiler').on('click', this.open);
            $('#prog-profiler nav').find('.prog-close-profiler').on('click', this.close);
            return this;
        },
        /**
         * Open the profiler bar
         * 
         * @param  {object} evt 
         * @return {void}
         */
        open: function (evt) {
            var target = $(evt.currentTarget);
            var table = '#prog-' + target.data('table') + '-table';

            target.closest('#prog-profiler').find('.prog-table-data').slideDown(350).end()
                                            .find('table').hide().end()
                                            .find(table).show().end()
                                            .find('.prog-close-profiler li').fadeIn(350);

            evt.preventDefault();
            evt.stopPropagation();
        },
        /**
         * Close the profiler
         * 
         * @param  {object} evt
         * @return {void}
         */
        close: function (evt) {
            var target = $(evt.currentTarget);
            target.closest('#prog-profiler').find('.prog-table-data').slideUp(350, function () {
                target.closest('#prog-profiler').find('table').hide();
            }).end().find('.prog-close-profiler li').fadeOut(350);

            evt.preventDefault();
            evt.stopPropagation();
        }
    };
})();

$(function () {
    prog.profiler.injectCss()
                 .attachEvents();
});
