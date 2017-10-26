'use strict';

var $ = jQuery;

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

    var filters = [];

    function add(data) {
        var filter = find(data.slug);

        if (filter) {
            filter.values.push(data.value);
        } else {
            filter = new Filter(data);
            filter.values.push(data.value);
            filters.push(filter);
        }

        url.change(filters);
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

        url.change(filters);
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

        url.change(filters);
    }

    function empty(data) {
        var filter = find(data.slug);

        if (filter) {
            filter.values = [];
        }

        url.change(filters);
    }

    function find(slug) {
        return filters.find(function (filter) {
            return filter.slug === slug;
        });
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
            url += filter.slug;
            url += '/';
            url += filter.values.join(',');
            url += '/';
        });

        // remove last slash
        url = url.slice(0, -1);

        return url;
    }

    function change(data) {
        var page = $('[data-pfs]').data('pfs');
        var url = generate(data);

        window.location.href = page + url;
    }

    function order(data) {
        data.sort(function (a, b) {
            return a.order > b.order;
        });

        return data;
    }

    return {
        change: change
    }

})();

// Filter model

var Filter = function (data) {
    this.slug = data.slug;
    this.order = data.order;
    this.values = [];
};

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


