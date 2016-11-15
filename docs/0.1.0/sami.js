
(function(root) {

    var bhIndex = null;
    var rootPath = '';
    var treeHtml = '        <ul>                <li data-name="namespace:PHPProm" class="opened">                    <div style="padding-left:0px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="PHPProm.html">PHPProm</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="namespace:PHPProm_Integration" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="PHPProm/Integration.html">Integration</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:PHPProm_Integration_SilexSetup" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="PHPProm/Integration/SilexSetup.html">SilexSetup</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="namespace:PHPProm_Storage" class="opened">                    <div style="padding-left:18px" class="hd">                        <span class="glyphicon glyphicon-play"></span><a href="PHPProm/Storage.html">Storage</a>                    </div>                    <div class="bd">                                <ul>                <li data-name="class:PHPProm_Storage_AbstractStorage" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="PHPProm/Storage/AbstractStorage.html">AbstractStorage</a>                    </div>                </li>                            <li data-name="class:PHPProm_Storage_DBAL" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="PHPProm/Storage/DBAL.html">DBAL</a>                    </div>                </li>                            <li data-name="class:PHPProm_Storage_Memcached" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="PHPProm/Storage/Memcached.html">Memcached</a>                    </div>                </li>                            <li data-name="class:PHPProm_Storage_MongoDB" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="PHPProm/Storage/MongoDB.html">MongoDB</a>                    </div>                </li>                            <li data-name="class:PHPProm_Storage_Redis" >                    <div style="padding-left:44px" class="hd leaf">                        <a href="PHPProm/Storage/Redis.html">Redis</a>                    </div>                </li>                </ul></div>                </li>                            <li data-name="class:PHPProm_PrometheusExport" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="PHPProm/PrometheusExport.html">PrometheusExport</a>                    </div>                </li>                            <li data-name="class:PHPProm_StopWatch" class="opened">                    <div style="padding-left:26px" class="hd leaf">                        <a href="PHPProm/StopWatch.html">StopWatch</a>                    </div>                </li>                </ul></div>                </li>                </ul>';

    var searchTypeClasses = {
        'Namespace': 'label-default',
        'Class': 'label-info',
        'Interface': 'label-primary',
        'Trait': 'label-success',
        'Method': 'label-danger',
        '_': 'label-warning'
    };

    var searchIndex = [
                    
            {"type": "Namespace", "link": "PHPProm.html", "name": "PHPProm", "doc": "Namespace PHPProm"},{"type": "Namespace", "link": "PHPProm/Integration.html", "name": "PHPProm\\Integration", "doc": "Namespace PHPProm\\Integration"},{"type": "Namespace", "link": "PHPProm/Storage.html", "name": "PHPProm\\Storage", "doc": "Namespace PHPProm\\Storage"},
            
            {"type": "Class", "fromName": "PHPProm\\Integration", "fromLink": "PHPProm/Integration.html", "link": "PHPProm/Integration/SilexSetup.html", "name": "PHPProm\\Integration\\SilexSetup", "doc": "&quot;Class SilexSetup\nSetups Silex applications to measure:\n- the time of each route\n- the used memory of each route\n- the amount of requests of each route\nIt also offers an function to be used for a Prometheus scrapable endpoint.&quot;"},
                                                        {"type": "Method", "fromName": "PHPProm\\Integration\\SilexSetup", "fromLink": "PHPProm/Integration/SilexSetup.html", "link": "PHPProm/Integration/SilexSetup.html#method_setupAndGetMetricsRoute", "name": "PHPProm\\Integration\\SilexSetup::setupAndGetMetricsRoute", "doc": "&quot;Sets up the Silex middlewares where the actual measurements happen\nand returns a function to be used for a Prometheus scrapable endpoint.&quot;"},
            
            {"type": "Class", "fromName": "PHPProm", "fromLink": "PHPProm.html", "link": "PHPProm/PrometheusExport.html", "name": "PHPProm\\PrometheusExport", "doc": "&quot;Class PrometheusExport\nTo export the measurements into the Prometheus format.&quot;"},
                                                        {"type": "Method", "fromName": "PHPProm\\PrometheusExport", "fromLink": "PHPProm/PrometheusExport.html", "link": "PHPProm/PrometheusExport.html#method_getExport", "name": "PHPProm\\PrometheusExport::getExport", "doc": "&quot;Gets a Prometheus export of the given storage.&quot;"},
            
            {"type": "Class", "fromName": "PHPProm", "fromLink": "PHPProm.html", "link": "PHPProm/StopWatch.html", "name": "PHPProm\\StopWatch", "doc": "&quot;Class StopWatch\nSmall utility class to measure the time of something.&quot;"},
                                                        {"type": "Method", "fromName": "PHPProm\\StopWatch", "fromLink": "PHPProm/StopWatch.html", "link": "PHPProm/StopWatch.html#method___construct", "name": "PHPProm\\StopWatch::__construct", "doc": "&quot;StopWatch constructor.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\StopWatch", "fromLink": "PHPProm/StopWatch.html", "link": "PHPProm/StopWatch.html#method_start", "name": "PHPProm\\StopWatch::start", "doc": "&quot;To start the measurement.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\StopWatch", "fromLink": "PHPProm/StopWatch.html", "link": "PHPProm/StopWatch.html#method_stop", "name": "PHPProm\\StopWatch::stop", "doc": "&quot;&quot;"},
            
            {"type": "Class", "fromName": "PHPProm\\Storage", "fromLink": "PHPProm/Storage.html", "link": "PHPProm/Storage/AbstractStorage.html", "name": "PHPProm\\Storage\\AbstractStorage", "doc": "&quot;Class AbstractStorage\nThe parent class of all storage implementations.&quot;"},
                                                        {"type": "Method", "fromName": "PHPProm\\Storage\\AbstractStorage", "fromLink": "PHPProm/Storage/AbstractStorage.html", "link": "PHPProm/Storage/AbstractStorage.html#method___construct", "name": "PHPProm\\Storage\\AbstractStorage::__construct", "doc": "&quot;AbstractStorage constructor.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\AbstractStorage", "fromLink": "PHPProm/Storage/AbstractStorage.html", "link": "PHPProm/Storage/AbstractStorage.html#method_addAvailableMetric", "name": "PHPProm\\Storage\\AbstractStorage::addAvailableMetric", "doc": "&quot;Adds a metric to the available ones.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\AbstractStorage", "fromLink": "PHPProm/Storage/AbstractStorage.html", "link": "PHPProm/Storage/AbstractStorage.html#method_getAvailableMetrics", "name": "PHPProm\\Storage\\AbstractStorage::getAvailableMetrics", "doc": "&quot;Gets all available metrics in an array.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\AbstractStorage", "fromLink": "PHPProm/Storage/AbstractStorage.html", "link": "PHPProm/Storage/AbstractStorage.html#method_storeMeasurement", "name": "PHPProm\\Storage\\AbstractStorage::storeMeasurement", "doc": "&quot;Stores a measurement.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\AbstractStorage", "fromLink": "PHPProm/Storage/AbstractStorage.html", "link": "PHPProm/Storage/AbstractStorage.html#method_incrementMeasurement", "name": "PHPProm\\Storage\\AbstractStorage::incrementMeasurement", "doc": "&quot;Increments a measurement, starting with 1 if it doesn&#039;t exist yet.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\AbstractStorage", "fromLink": "PHPProm/Storage/AbstractStorage.html", "link": "PHPProm/Storage/AbstractStorage.html#method_getMeasurements", "name": "PHPProm\\Storage\\AbstractStorage::getMeasurements", "doc": "&quot;Gets all measurements.&quot;"},
            
            {"type": "Class", "fromName": "PHPProm\\Storage", "fromLink": "PHPProm/Storage.html", "link": "PHPProm/Storage/DBAL.html", "name": "PHPProm\\Storage\\DBAL", "doc": "&quot;Class DBAL\nStorage implementation using Doctrine DBAL.&quot;"},
                                                        {"type": "Method", "fromName": "PHPProm\\Storage\\DBAL", "fromLink": "PHPProm/Storage/DBAL.html", "link": "PHPProm/Storage/DBAL.html#method___construct", "name": "PHPProm\\Storage\\DBAL::__construct", "doc": "&quot;DBAL constructor.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\DBAL", "fromLink": "PHPProm/Storage/DBAL.html", "link": "PHPProm/Storage/DBAL.html#method_storeMeasurement", "name": "PHPProm\\Storage\\DBAL::storeMeasurement", "doc": "&quot;Stores a measurement.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\DBAL", "fromLink": "PHPProm/Storage/DBAL.html", "link": "PHPProm/Storage/DBAL.html#method_incrementMeasurement", "name": "PHPProm\\Storage\\DBAL::incrementMeasurement", "doc": "&quot;Increments a measurement, starting with 1 if it doesn&#039;t exist yet.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\DBAL", "fromLink": "PHPProm/Storage/DBAL.html", "link": "PHPProm/Storage/DBAL.html#method_getMeasurements", "name": "PHPProm\\Storage\\DBAL::getMeasurements", "doc": "&quot;Gets all measurements.&quot;"},
            
            {"type": "Class", "fromName": "PHPProm\\Storage", "fromLink": "PHPProm/Storage.html", "link": "PHPProm/Storage/Memcached.html", "name": "PHPProm\\Storage\\Memcached", "doc": "&quot;Class Memcached\nStorage implementation using memcached.&quot;"},
                                                        {"type": "Method", "fromName": "PHPProm\\Storage\\Memcached", "fromLink": "PHPProm/Storage/Memcached.html", "link": "PHPProm/Storage/Memcached.html#method___construct", "name": "PHPProm\\Storage\\Memcached::__construct", "doc": "&quot;Memcached constructor.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\Memcached", "fromLink": "PHPProm/Storage/Memcached.html", "link": "PHPProm/Storage/Memcached.html#method_storeMeasurement", "name": "PHPProm\\Storage\\Memcached::storeMeasurement", "doc": "&quot;Stores a measurement.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\Memcached", "fromLink": "PHPProm/Storage/Memcached.html", "link": "PHPProm/Storage/Memcached.html#method_incrementMeasurement", "name": "PHPProm\\Storage\\Memcached::incrementMeasurement", "doc": "&quot;Increments a measurement, starting with 1 if it doesn&#039;t exist yet.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\Memcached", "fromLink": "PHPProm/Storage/Memcached.html", "link": "PHPProm/Storage/Memcached.html#method_getMeasurements", "name": "PHPProm\\Storage\\Memcached::getMeasurements", "doc": "&quot;Gets all measurements.&quot;"},
            
            {"type": "Class", "fromName": "PHPProm\\Storage", "fromLink": "PHPProm/Storage.html", "link": "PHPProm/Storage/MongoDB.html", "name": "PHPProm\\Storage\\MongoDB", "doc": "&quot;Class MongoDB\nStorage implementation using MongoDB.&quot;"},
                                                        {"type": "Method", "fromName": "PHPProm\\Storage\\MongoDB", "fromLink": "PHPProm/Storage/MongoDB.html", "link": "PHPProm/Storage/MongoDB.html#method___construct", "name": "PHPProm\\Storage\\MongoDB::__construct", "doc": "&quot;MongoDB constructor.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\MongoDB", "fromLink": "PHPProm/Storage/MongoDB.html", "link": "PHPProm/Storage/MongoDB.html#method_storeMeasurement", "name": "PHPProm\\Storage\\MongoDB::storeMeasurement", "doc": "&quot;Stores a measurement.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\MongoDB", "fromLink": "PHPProm/Storage/MongoDB.html", "link": "PHPProm/Storage/MongoDB.html#method_incrementMeasurement", "name": "PHPProm\\Storage\\MongoDB::incrementMeasurement", "doc": "&quot;Increments a measurement, starting with 1 if it doesn&#039;t exist yet.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\MongoDB", "fromLink": "PHPProm/Storage/MongoDB.html", "link": "PHPProm/Storage/MongoDB.html#method_getMeasurements", "name": "PHPProm\\Storage\\MongoDB::getMeasurements", "doc": "&quot;Gets all measurements.&quot;"},
            
            {"type": "Class", "fromName": "PHPProm\\Storage", "fromLink": "PHPProm/Storage.html", "link": "PHPProm/Storage/Redis.html", "name": "PHPProm\\Storage\\Redis", "doc": "&quot;Class Redis\nStorage implementation using Redis.&quot;"},
                                                        {"type": "Method", "fromName": "PHPProm\\Storage\\Redis", "fromLink": "PHPProm/Storage/Redis.html", "link": "PHPProm/Storage/Redis.html#method___construct", "name": "PHPProm\\Storage\\Redis::__construct", "doc": "&quot;Redis constructor.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\Redis", "fromLink": "PHPProm/Storage/Redis.html", "link": "PHPProm/Storage/Redis.html#method_storeMeasurement", "name": "PHPProm\\Storage\\Redis::storeMeasurement", "doc": "&quot;Stores a measurement.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\Redis", "fromLink": "PHPProm/Storage/Redis.html", "link": "PHPProm/Storage/Redis.html#method_incrementMeasurement", "name": "PHPProm\\Storage\\Redis::incrementMeasurement", "doc": "&quot;Increments a measurement, starting with 1 if it doesn&#039;t exist yet.&quot;"},
                    {"type": "Method", "fromName": "PHPProm\\Storage\\Redis", "fromLink": "PHPProm/Storage/Redis.html", "link": "PHPProm/Storage/Redis.html#method_getMeasurements", "name": "PHPProm\\Storage\\Redis::getMeasurements", "doc": "&quot;Gets all measurements.&quot;"},
            
            
                                        // Fix trailing commas in the index
        {}
    ];

    /** Tokenizes strings by namespaces and functions */
    function tokenizer(term) {
        if (!term) {
            return [];
        }

        var tokens = [term];
        var meth = term.indexOf('::');

        // Split tokens into methods if "::" is found.
        if (meth > -1) {
            tokens.push(term.substr(meth + 2));
            term = term.substr(0, meth - 2);
        }

        // Split by namespace or fake namespace.
        if (term.indexOf('\\') > -1) {
            tokens = tokens.concat(term.split('\\'));
        } else if (term.indexOf('_') > 0) {
            tokens = tokens.concat(term.split('_'));
        }

        // Merge in splitting the string by case and return
        tokens = tokens.concat(term.match(/(([A-Z]?[^A-Z]*)|([a-z]?[^a-z]*))/g).slice(0,-1));

        return tokens;
    };

    root.Sami = {
        /**
         * Cleans the provided term. If no term is provided, then one is
         * grabbed from the query string "search" parameter.
         */
        cleanSearchTerm: function(term) {
            // Grab from the query string
            if (typeof term === 'undefined') {
                var name = 'search';
                var regex = new RegExp("[\\?&]" + name + "=([^&#]*)");
                var results = regex.exec(location.search);
                if (results === null) {
                    return null;
                }
                term = decodeURIComponent(results[1].replace(/\+/g, " "));
            }

            return term.replace(/<(?:.|\n)*?>/gm, '');
        },

        /** Searches through the index for a given term */
        search: function(term) {
            // Create a new search index if needed
            if (!bhIndex) {
                bhIndex = new Bloodhound({
                    limit: 500,
                    local: searchIndex,
                    datumTokenizer: function (d) {
                        return tokenizer(d.name);
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace
                });
                bhIndex.initialize();
            }

            results = [];
            bhIndex.get(term, function(matches) {
                results = matches;
            });

            if (!rootPath) {
                return results;
            }

            // Fix the element links based on the current page depth.
            return $.map(results, function(ele) {
                if (ele.link.indexOf('..') > -1) {
                    return ele;
                }
                ele.link = rootPath + ele.link;
                if (ele.fromLink) {
                    ele.fromLink = rootPath + ele.fromLink;
                }
                return ele;
            });
        },

        /** Get a search class for a specific type */
        getSearchClass: function(type) {
            return searchTypeClasses[type] || searchTypeClasses['_'];
        },

        /** Add the left-nav tree to the site */
        injectApiTree: function(ele) {
            ele.html(treeHtml);
        }
    };

    $(function() {
        // Modify the HTML to work correctly based on the current depth
        rootPath = $('body').attr('data-root-path');
        treeHtml = treeHtml.replace(/href="/g, 'href="' + rootPath);
        Sami.injectApiTree($('#api-tree'));
    });

    return root.Sami;
})(window);

$(function() {

    // Enable the version switcher
    $('#version-switcher').change(function() {
        window.location = $(this).val()
    });

    
        // Toggle left-nav divs on click
        $('#api-tree .hd span').click(function() {
            $(this).parent().parent().toggleClass('opened');
        });

        // Expand the parent namespaces of the current page.
        var expected = $('body').attr('data-name');

        if (expected) {
            // Open the currently selected node and its parents.
            var container = $('#api-tree');
            var node = $('#api-tree li[data-name="' + expected + '"]');
            // Node might not be found when simulating namespaces
            if (node.length > 0) {
                node.addClass('active').addClass('opened');
                node.parents('li').addClass('opened');
                var scrollPos = node.offset().top - container.offset().top + container.scrollTop();
                // Position the item nearer to the top of the screen.
                scrollPos -= 200;
                container.scrollTop(scrollPos);
            }
        }

    
    
        var form = $('#search-form .typeahead');
        form.typeahead({
            hint: true,
            highlight: true,
            minLength: 1
        }, {
            name: 'search',
            displayKey: 'name',
            source: function (q, cb) {
                cb(Sami.search(q));
            }
        });

        // The selection is direct-linked when the user selects a suggestion.
        form.on('typeahead:selected', function(e, suggestion) {
            window.location = suggestion.link;
        });

        // The form is submitted when the user hits enter.
        form.keypress(function (e) {
            if (e.which == 13) {
                $('#search-form').submit();
                return true;
            }
        });

    
});


