window.prog = window.prog || {};
prog.profiler = prog.profiler || {};

prog.profiler = (function () {
    return {
        heredoc: function (fn) {
            return fn.toString().split('\n').slice(1,-1).join('\n') + '\n';
        },
        css: function () {
            return this.heredoc(function () {/*
#prog-profiler { position: fixed; bottom: 0; left: 0; background: #000; color: #efefef; font-family: Verdana; font-size: 11px; width: 100%; z-index: 50000; }
#prog-profiler header { padding: 10px; }
#prog-profiler nav ul { list-style: none; padding: 0; margin: 0 }
#prog-profiler nav li { display: inline-block; margin: 0 5px; }
#prog-profiler nav li.logo { margin-left: 20px; margin-right: 20px; }
#prog-profiler nav li.nav { padding: 7px 14px; background: #222; border-radius: 8px; text-shadow: 1px solid #000; }
#prog-profiler nav li.nav:hover { box-shadow: inset 0 0 3px #111; background: #333; }
#prog-profiler nav a, #prog-profiler nav a:active, #prog-profiler nav a:visited, #prog-profiler nav a:hover { text-decoration: none; color: #efefef; }
.prog-table-data { max-height: 500px; overflow: auto; background: #222; }
.prog-table-data table { color: #222; font-family: Monaco, monospace; font-size: 11px; width: 100%; }
.prog-table-data th { text-align: left; font-weight: normal; background: #B81F2F; padding: 10px; border-left: 1px solid #bb4b56; border-right: 1px solid #720914; border-bottom: 1px solid #720914; border-top: 1px solid #bb4b56; color: #fff; text-shadow: 1px 1px 0 #720914; }
.prog-table-data td { background: #e8e8e8; padding: 10px; border-left: 1px solid #fff; border-right: 1px solid #ccc; border-bottom: 1px solid #ccc; border-top: 1px solid #fff; }
.prog-table-data .query { width: 60%; }
.prog-table-data .query-time { width: 10%; }
.prog-table-data .query-memory { width: 10%; }
.prog-table-data .query-params { width: 20%; }
            */});
        },
        injectCss: function () {
            $('head').append(document.createElement('style'))
                     .find('style')
                     .append(this.css());

            return this;
        },
        attachEvents: function () {
            $('#prog-profiler nav a').on('click', this.open);

            return this;
        },
        open: function (evt) {
            var target = evt.target;
            var table = 'prog-' + target.data('table') + '-table';

            console.log(target);

            target.closest('#prog-profiler').find('table').hide();
            target.closest('#prog-profiler').find(table).show();

            evt.preventDefault();
            evt.stopPropagation();
        }
    };
})();

$(function () {
    prog.profiler.injectCss()
                 .attachEvents();
});
