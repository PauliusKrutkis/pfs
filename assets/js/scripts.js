'use strict';

var $ = jQuery;

// Filter model

var Filter = function (data) {
    this.slug = data.slug;
    this.order = data.order;
    this.values = [];
};

// Range

var range = (function () {
    function update(input, options) {
        var $from = $(options.from);
        var $to = $(options.to);

        input.noUiSlider.on('update', function (values, handle) {
            if (handle) {
                $to.text(Math.round(values[handle]));
            } else {
                $from.text(Math.round(values[handle]));
            }
        });
    }

    function change(input, options) {
        input.noUiSlider.on('change', function (values) {
            var value = values.map(function (number) {
                return Math.round(number)
            }).join('-');

            if ((Math.round(values[0]) !== options.min) || (Math.round(values[1]) !== options.max)) {
                options.value = value;
                store.change(options);
            } else {
                store.empty(options);
            }
        });
    }

    return {
        update: update,
        change: change
    }
})();

// Store

var store = (function () {

    var filters = getFilters();

    function add(data) {
        var filter = find(data.slug);

        if (filter) {
            filter.values.push(data.value);
        } else {
            filter = new Filter(data);
            filter.values.push(data.value);
            filters.push(filter);
        }

        url.update(filters);
    }

    function change(data) {
        var filter = find(data.slug);

        if (filter) {
            filter.values = [];
            filter.values.push(data.value);
        } else {
            filter = new Filter(data);
            filter.values.push(data.value);
            filters.push(filter);
        }

        url.update(filters);
    }

    function remove(data) {
        var filter = find(data.slug);

        if (filter) {
            var index = filter.values.indexOf(data.value);
            if (index > -1) {
                filter.values.splice(index, 1);
            }
        } else {
            return '';
        }

        url.update(filters);
    }

    function empty(data) {
        var filter = find(data.slug);

        if (filter) {
            filter.values = [];
        }

        url.update(filters);
    }

    function find(slug) {
        return filters.find(function (filter) {
            return filter.slug === slug;
        });
    }

    function getFilters() {
        var $pfs = $('[data-pfs]');
        var filters = [];

        if (!$pfs.length) {
            return;
        }

        var filtersJson = $pfs.data('pfs').filters;

        filtersJson.map(function (data) {
            var values = data.values;
            var filter = new Filter(data);

            filter.values = values;
            filters.push(filter);
        });

        return filters;
    }

    return {
        add: add,
        change: change,
        remove: remove,
        empty: empty
    }

})();

// Url

var url = (function () {

    function generate(data) {
        var url = '';

        data = order(data);

        data.map(function (filter) {
            if (!filter.values.length) {
                return;
            }

            url += filter.slug;
            url += '/';
            url += filter.values.join(',');
            url += '/';
        });

        // remove last slash
        url = url.slice(0, -1);

        return url;
    }

    function update(data) {
        var settings = $('[data-pfs]').data('pfs');
        var page = settings.permalink;
        var ajax = settings.ajax;
        var url = page + generate(data);

        if (ajax) {
            updateContent(url, settings, data);
        } else {
            window.location.href = url;
        }
    }

    function updateContent(url, settings, data) {

        var query = {
            action: 'getNavigation',
            page: settings.page,
            data: data
        };

        $.get(pfs.ajaxUrl, query)
            .done(function (response) {
                window.history.pushState(null, "", url);
            });

    }

    function order(data) {
        data.sort(function (a, b) {
            return a.order > b.order;
        });

        return data;
    }

    return {
        update: update
    }

})();

// Bind events

$('[data-pfs-checkbox]').click(function () {
    var data = $(this).data('pfs-checkbox');

    if (this.checked) {
        store.add(data);
    } else {
        store.remove(data);
    }
});

$('[data-pfs-range]').each(function () {
    var options = $(this).data('pfs-range');

    noUiSlider.create(this, {
        start: [options.activeFrom, options.activeTo],
        connect: [false, true, false],
        step: 1,
        range: {
            'min': [options.min],
            'max': [options.max]
        }
    });

    range.update(this, options);
    range.change(this, options);
});

$('[data-pfs-pagination]').find('a').click(function (e) {
    e.preventDefault();

    var url = this.href;
    var page = url.match(/page\/(\d+)/)[1];

    store.change({
        'slug': 'page',
        'order': 999,
        'value': page
    });
});