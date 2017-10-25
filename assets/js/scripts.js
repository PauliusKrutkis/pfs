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
                store.change(options.slug, value);
            } else {
                store.empty(options.slug);
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

    var data = [];

    function add(slug, value) {
        var filter = find(slug);

        if (filter) {
            filter.values.push(value);
        } else {
            filter = {
                slug: slug,
                values: []
            };

            filter.values.push(value);
            data.push(filter);
        }
    }

    function change(slug, value) {
        var filter = find(slug);

        if (filter) {
            filter.values = [];
            filter.values.push(value);
        } else {
            filter = {
                slug: slug,
                values: []
            };

            filter.values.push(value);
            data.push(filter);
        }
    }

    function remove(slug, value) {
        var filter = find(slug);

        if (filter) {
            var index = filter.values.indexOf(value);
            if (index > -1) {
                filter.values.splice(index, 1);
            }
        } else {
            return '';
        }
    }

    function empty(slug) {
        var filter = find(slug);

        if (filter) {
            filter.values = [];
        } else {
            return '';
        }
    }

    function find(slug) {
        return data.find(function (filter) {
            return filter.slug === slug;
        });
    }

    return {
        add: add,
        change: change,
        remove: remove,
        empty: empty,
        data: data
    }

})();

// Bind events

$('[data-pfs-checkbox]').click(function () {
    var data = $(this).data('pfs-checkbox');

    if (this.checked) {
        store.add(data.slug, data.value);
    } else {
        store.remove(data.slug, data.value);
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


