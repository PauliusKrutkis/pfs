'use strict';

(function ($) {
    var url = getBaseUrl();
    var data = {};
    var scrolled = false;
    var trigger = '[data-filter-param]';
    var filterGroup = '[data-filter-group]';
    var filterContainer = '[data-filter-groups]';
    var pagination = '[data-pagination] a';

    $(pagination).click(goToPage);
    $(filterGroup).each(getData);
    $(trigger).click(toggle);
    noUiSliderInit('[data-no-ui-slider]');

    function goToPage(e) {
        e.preventDefault();
        var url = this.href;
        var pageNr = url.match(/page\/(\d+)/)[1];

        add('page', pageNr);
    }

    function getBaseUrl() {
        if (true) {
            return location.protocol + '//' + location.host + '/' + pfs.pageUrl;
        } else {
            return location.protocol + '//' + location.host + location.pathname + '?';
        }
    }

    function noUiSliderInit(selector) {
        $(selector).each(function () {
            var $slider = $(this);
            var options = $slider.data('no-ui-slider');

            noUiSlider.create($slider[0], {
                start: [options.activeFrom, options.activeTo],
                connect: [false, true, false],
                step: 1,
                range: {
                    'min': [options.min],
                    'max': [options.max]
                }
            });

            bindNoUiMinMax($slider);
            bindNoUiChange($slider);
        });
    }

    function bindNoUiChange($slider) {
        var options = $slider.data('no-ui-slider');

        $slider[0].noUiSlider.on('change', function (values) {
            var filter = values.map(function (number) {
                return Math.round(number)
            }).join('-');

            emptyGroup(options.meta);

            if ((Math.round(values[0]) !== options.min) || (Math.round(values[1]) !== options.max)) {
                add(options.meta, filter);
            } else {
                emptyGroupAndPush(options.meta);
            }
        });
    }

    function bindNoUiMinMax($slider) {
        var options = $slider.data('no-ui-slider');
        var $from = $(options.from);
        var $to = $(options.to);

        $slider[0].noUiSlider.on('update', function (values, handle) {
            if (handle) {
                $to.text(Math.round(values[handle]));
            } else {
                $from.text(Math.round(values[handle]));
            }
        });
    }

    function scrollTofilters() {
        if (!scrolled)
            $(window).scrollTop($(filterContainer).offset().top);
    }

    function getData() {
        var group = $(this).data('filter-group');
        var params = getUrlParameter(group);

        if (typeof params !== "undefined") {
            scrollTofilters();

            switch (typeof params) {
                case 'string':
                    params = params.split(',');
                    break;
                case 'number':
                    params = [params];
                    break;
            }

            data[group] = params;
        } else {
            data[group] = [];
        }
    }

    function push() {
        window.location.href = generateUrl();
    }

    function generateUrl() {
        var count = 0;
        for (var key in data) {
            if (data[key].length > 0) {
                var group;
                if (true) {
                    group = '/' + key + '/';
                } else {
                    group = '&' + key + '=';
                }
                for (var subkey in data[key]) {
                    if (subkey != 0) {
                        group += ',';
                    }
                    group += data[key][subkey];
                }
                url += group;
                count++;
            }
        }

        if (count === 0) {
            url = url.replace('?', '');
        }

        return url;
    }

    function getUrlParameter(sParam) {
        var filterValue = pfs.activeFilters[sParam];

        if (filterValue !== undefined) {
            var values = pfs.activeFilters[sParam];

            if (Array.isArray(values)) {
                return values.join(',');
            } else {
                return values;
            }
        }
    }

    function toggle() {
        var element = $(this).data('slug').toString();
        var group = $(this).closest(filterGroup).data('filter-group');

        if (filterExist(group, element)) {
            $(this).removeClass('active');
            remove(group, element);
        } else {
            $(this).addClass('active');
            add(group, element);
        }
    }

    function filterExist(group, filter) {
        return $.inArray(filter, data[group]) >= 0;
    }

    function add(group, filter) {
        emptyGroup('page');
        data[group].push(filter);
        push();
    }

    function emptyGroup(group) {
        data[group] = [];
    }

    function emptyGroupAndPush(group) {
        data[group] = [];
        push();
    }

    function remove(group, filter) {
        for (var key in data[group]) {
            if (data[group][key] === filter) {
                data[group].splice(key, 1);
            }
        }

        push();
    }

})(jQuery);
