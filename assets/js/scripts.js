'use strict';

var $ = jQuery;

// Filter model

var Filter = function (data) {
    this.slug = data.slug;
    this.order = data.order;
    this.values = [];
};

// Store

var store = (function () {

    var $navigation = $('[data-pfs-navigation]');
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
            if (index > -2) {
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

        if (!data.noUpdate) {
            url.update(filters);
        }
    }

    function find(slug) {
        return filters.find(function (filter) {
            return filter.slug === slug;
        });
    }

    function getFilters() {
        var filters = [];

        if (!$navigation.length) {
            return;
        }

        var filtersJson = $navigation.data('pfs-navigation').filters;

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
        empty: empty,
        navigation: $navigation
    }

})();

// Events

var events = (function () {

    function dispatch(element, name) {
        var event;

        if (document.createEvent) {
            event = document.createEvent("HTMLEvents");
            event.initEvent(name, true, true);
        } else {
            event = document.createEventObject();
            event.eventType = name;
        }

        event.eventName = name;

        if (document.createEvent) {
            element.dispatchEvent(event);
        } else {
            element.fireEvent("on" + event.eventType, event);
        }
    }

    return {
        dispatch: dispatch
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

        return url;
    }

    function update(data) {
        var settings = store.navigation.data('pfs-navigation');
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

        events.dispatch(store.navigation[0], 'update_start');

        $.get(pfs.ajaxUrl, query)
            .done(function (response) {
                for (var fragment in response) {
                    if (response.hasOwnProperty(fragment)) {
                        $(fragment).html(response[fragment]);
                    }
                }

                window.history.pushState(null, "", url);
                events.dispatch(store.navigation[0], 'update_done');
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

    store.empty({
        'slug': 'page',
        'noUpdate': true
    });

    if (this.checked) {
        store.add(data);
    } else {
        store.remove(data);
    }

});

$('[data-pfs-range]').slider({
    range: true,
    create: function () {
        var $this = $(this);
        var options = $this.data('pfs-range');

        $this.slider('option', 'min', options.min);
        $this.slider('option', 'max', options.max);
        $this.slider('option', 'values', [options.activeMin, options.activeMax]);

        $this.find('[data-pfs-range-min]').text($(this).slider("values")[0]);
        $this.find('[data-pfs-range-max]').text($(this).slider("values")[1]);

        $this.attr('data-init', 1);
    },
    slide: function (event, ui) {
        var $this = $(this);

        $this.find('[data-pfs-range-min]').text(ui.values[0]);
        $this.find('[data-pfs-range-max]').text(ui.values[1]);
    },
    change: function (event, ui) {
        var $this = $(this);
        var options = $this.data('pfs-range');

        if (!$this.attr('data-init')) {
            return;
        }

        store.empty({
            'slug': 'page',
            'noUpdate': true
        });

        if ((ui.values[0] !== options.min) || (ui.values[1] !== options.max)) {
            options.value = ui.values.join('-');
            store.change(options);
        } else {
            store.empty(options);
        }
    }
});

$('[data-pfs-pagination]').delegate('[data-page]', 'click', function (e) {
    e.preventDefault();
    var page = $(this).data('page');

    if (page !== 1) {
        store.change({
            'slug': 'page',
            'order': 999,
            'value': page
        });
    } else {
        store.empty({
            'slug': 'page'
        });
    }
});