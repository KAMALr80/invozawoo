(function ($) {
    var currentFilter = 'all';
    var globalSearchXhr = null;
    var focusedIndex = -1;

    var searchCache = {};
    var lastSearchKey = '';
    var inputDebounceTimer = null;
    var REQUEST_DELAY = 450;
var MIN_SEARCH_LENGTH = 1;

    function openGlobalSearch() {
        $('#erp-global-search-overlay').show().addClass('active');
        $('#erp-global-search').focus();
        $('#erp-search-shortcuts').show();
        $('#erp-search-quick-actions').show();
        $('#erp-global-search-results').html('');
        $('#erp-search-loading').removeClass('visible');
        $('#erp-search-clear').removeClass('visible');
        focusedIndex = -1;
    }
    function showSearchAnimation() {
    $('#erp-global-search-results').html(
        '<div class="erp-searching-state">' +
            '<div class="erp-search-skeleton"></div>' +
            '<div class="erp-search-skeleton"></div>' +
            '<div class="erp-search-skeleton"></div>' +
            '<div class="erp-search-skeleton"></div>' +
        '</div>'
    );
}

    function closeGlobalSearch() {
        $('#erp-global-search-overlay').removeClass('active');

        if (globalSearchXhr && globalSearchXhr.readyState !== 4) {
            globalSearchXhr.abort();
        }

        clearTimeout(inputDebounceTimer);

        setTimeout(function () {
            $('#erp-global-search-overlay').hide();
            $('#erp-global-search').val('');
            $('#erp-global-search-results').html('');
            $('#erp-search-shortcuts').show();
            $('#erp-search-quick-actions').show();
            $('#erp-search-loading').removeClass('visible');
            $('#erp-search-clear').removeClass('visible');
            focusedIndex = -1;
            currentFilter = 'all';
            lastSearchKey = '';
            setFilter('all');
        }, 250);
    }

    function setFilter(filter) {
        currentFilter = filter;
        $('.erp-filter-pill').removeClass('btn-primary active').addClass('btn-default');
        $('.erp-filter-pill[data-filter="' + filter + '"]').removeClass('btn-default').addClass('btn-primary active');
    }

    function getPrefixMap() {
        return {
            '/all': 'all',
            '/cus': 'customer',
            '/customer': 'customer',
            '/sup': 'supplier',
            '/supplier': 'supplier',
            '/inv': 'invoice',
            '/invoice': 'invoice',
            '/po': 'purchase_order',
            '/purchaseorder': 'purchase_order',
            '/purchase_order': 'purchase_order',
            '/vb': 'vendor_bill',
            '/vendorbill': 'vendor_bill',
            '/vendor_bill': 'vendor_bill',
            '/cm': 'credit_memo',
            '/creditmemo': 'credit_memo',
            '/credit_memo': 'credit_memo',
            '/vcm': 'vendor_credit_memo',
            '/vendorcreditmemo': 'vendor_credit_memo',
            '/vendor_credit_memo': 'vendor_credit_memo',
            '/exp': 'expense',
            '/expense': 'expense',
            '/fol': 'followup',
            '/followup': 'followup',
            '/pro': 'product',
            '/product': 'product',
            '/menu': 'menu',
            '/rep': 'report',
            '/report': 'report'
        };
    }

    function getAllowedFilters() {
        var filters = [];
        $('.erp-filter-pill').each(function () {
            filters.push($(this).data('filter'));
        });
        return filters;
    }

    function applyPrefixSearch() {
        var $input = $('#erp-global-search');
        var raw = $.trim($input.val());

        if (!raw || raw.charAt(0) !== '/') {
            return raw;
        }

        var parts = raw.split(/\s+/);
        var prefix = (parts[0] || '').toLowerCase();
        var prefixMap = getPrefixMap();

        if (prefixMap[prefix] && $.inArray(prefixMap[prefix], getAllowedFilters()) !== -1) {
            setFilter(prefixMap[prefix]);
            parts.shift();
            raw = $.trim(parts.join(' '));
            $input.val(raw);
        }

        return raw;
    }

    function escapeHtml(str) {
        return String(str || '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function escapeAttr(str) {
        return escapeHtml(str);
    }

    function highlightText(text, search) {
        text = String(text || '');
        search = $.trim(search || '');

        if (!search) {
            return escapeHtml(text);
        }

        var escapedText = escapeHtml(text);
        var escapedSearch = search.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');

        try {
            var regex = new RegExp('(' + escapedSearch + ')', 'ig');
            return escapedText.replace(regex, '<mark>$1</mark>');
        } catch (e) {
            return escapedText;
        }
    }

    function getResultIconClass(groupName) {
        var type = (groupName || '').toLowerCase();

        if (type.indexOf('product') !== -1) return 'products';
        if (type.indexOf('customer') !== -1) return 'customers';
        if (type.indexOf('supplier') !== -1) return 'customers';
        if (type.indexOf('invoice') !== -1) return 'invoices';
        if (type.indexOf('report') !== -1) return 'reports';
        if (type.indexOf('order') !== -1) return 'orders';
        if (type.indexOf('menu') !== -1) return 'settings';
        if (type.indexOf('setting') !== -1) return 'settings';

        return 'products';
    }

    function getResultEmoji(groupName) {
        var type = (groupName || '').toLowerCase();

        if (type.indexOf('product') !== -1) return '📦';
        if (type.indexOf('customer') !== -1) return '👤';
        if (type.indexOf('supplier') !== -1) return '🏢';
        if (type.indexOf('invoice') !== -1) return '🧾';
        if (type.indexOf('report') !== -1) return '📊';
        if (type.indexOf('order') !== -1) return '🚚';
        if (type.indexOf('menu') !== -1) return '📋';
        if (type.indexOf('setting') !== -1) return '⚙️';

        return '•';
    }

    function getBadgeClass(text) {
        var t = String(text || '').toLowerCase();

        if (t.indexOf('paid') !== -1 || t.indexOf('active') !== -1 || t.indexOf('done') !== -1 || t.indexOf('complete') !== -1) {
            return 'badge-green';
        }

        if (t.indexOf('pending') !== -1 || t.indexOf('processing') !== -1 || t.indexOf('low') !== -1) {
            return 'badge-yellow';
        }

        if (t.indexOf('overdue') !== -1 || t.indexOf('inactive') !== -1 || t.indexOf('cancel') !== -1) {
            return 'badge-red';
        }

        if (t.indexOf('vip') !== -1 || t.indexOf('finance') !== -1 || t.indexOf('shipped') !== -1) {
            return 'badge-blue';
        }

        return 'badge-gray';
    }

    function renderResults(items) {
        var $box = $('#erp-global-search-results');

        if (!items || !items.length) {
            $box.html(
                '<div class="erp-empty-state">' +
                    '<div class="erp-empty-icon">🔍</div>' +
                    '<div class="erp-empty-title">No results found</div>' +
                    '<div class="erp-empty-sub">Try different keywords or change the filter</div>' +
                '</div>'
            );
            focusedIndex = -1;
            return;
        }

        var grouped = {};

        $.each(items, function (i, item) {
            var groupName = item.type || 'Results';

            if (!grouped[groupName]) {
                grouped[groupName] = [];
            }

            grouped[groupName].push(item);
        });

        var html = '';
        var groupIndex = 0;
        var totalGroups = Object.keys(grouped).length;
        var searchTerm = $('#erp-global-search').val();

        $.each(grouped, function (groupName, groupItems) {
            html += '<div class="erp-result-group">';
            html += '<div class="erp-group-label">' + escapeHtml(groupName) + '</div>';

            $.each(groupItems, function (j, item) {
                var badge = item.badge || item.status || '';
                var badgeHtml = badge
                    ? '<span class="erp-item-badge ' + getBadgeClass(badge) + '">' + escapeHtml(badge) + '</span>'
                    : '';

                html += ''
                    + '<a href="' + escapeAttr(item.url || '#') + '" class="erp-result-item" data-url="' + escapeAttr(item.url || '#') + '">'
                    +     '<div class="erp-item-icon ' + getResultIconClass(groupName) + '">'
                    +         '<span class="erp-item-icon-emoji">' + getResultEmoji(groupName) + '</span>'
                    +     '</div>'
                    +     '<div class="erp-item-body">'
                    +         '<div class="erp-item-title">' + highlightText(item.title || '', searchTerm) + '</div>'
                    +         '<div class="erp-item-subtitle">' + escapeHtml(item.subtitle || '') + '</div>'
                    +     '</div>'
                    +     badgeHtml
                    +     '<div class="erp-item-arrow">'
                    +         '<svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">'
                    +             '<path d="M2 7h10M8 3l4 4-4 4"></path>'
                    +         '</svg>'
                    +     '</div>'
                    + '</a>';
            });

            html += '</div>';

            groupIndex++;

            if (groupIndex < totalGroups) {
                html += '<div class="erp-group-separator"></div>';
            }
        });

        $box.html(html);
        focusedIndex = -1;

        $('.erp-result-item').on('mouseenter', function () {
            $('.erp-result-item').removeClass('focused');
            $(this).addClass('focused');
            focusedIndex = $('.erp-result-item').index(this);
        });
    }

    function buildSearchKey(term, filter) {
        return $.trim(term).toLowerCase() + '||' + filter;
    }

    function doGlobalSearch(force) {
        var term = $.trim($('#erp-global-search').val());
        term = applyPrefixSearch();

        if (term.length < MIN_SEARCH_LENGTH) {
            if (globalSearchXhr && globalSearchXhr.readyState !== 4) {
                globalSearchXhr.abort();
            }

            $('#erp-search-loading').removeClass('visible');
            $('#erp-global-search-results').html('');
            lastSearchKey = '';
            return;
        }

        var searchKey = buildSearchKey(term, currentFilter);

        if (!force && lastSearchKey === searchKey) {
            $('#erp-search-loading').removeClass('visible');
            return;
        }

        if (searchCache[searchKey]) {
            lastSearchKey = searchKey;
            $('#erp-search-loading').removeClass('visible');
            renderResults(searchCache[searchKey]);
            return;
        }

        lastSearchKey = searchKey;

        if (globalSearchXhr && globalSearchXhr.readyState !== 4) {
            globalSearchXhr.abort();
        }

        $('#erp-search-loading').addClass('visible');
showSearchAnimation();

        globalSearchXhr = $.ajax({
            url: '/global-search',
            type: 'GET',
            dataType: 'json',
            data: {
                term: term,
                category: currentFilter
            },
            success: function (response) {
                searchCache[searchKey] = response || [];
                $('#erp-search-loading').removeClass('visible');
                renderResults(response || []);
            },
            error: function (xhr, status) {
                if (status === 'abort') {
                    return;
                }

                $('#erp-search-loading').removeClass('visible');
                $('#erp-global-search-results').html(
                    '<div class="erp-empty-state">' +
                        '<div class="erp-empty-icon">🔍</div>' +
                        '<div class="erp-empty-title">Search error occurred.</div>' +
                        '<div class="erp-empty-sub">Please try again.</div>' +
                    '</div>'
                );
            }
        });
    }

    function scheduleSearch(force) {
        clearTimeout(inputDebounceTimer);

        inputDebounceTimer = setTimeout(function () {
            doGlobalSearch(force === true);
        }, REQUEST_DELAY);
    }

    function moveFocus(direction) {
        var $items = $('.erp-result-item');

        if (!$items.length) {
            return;
        }

        $items.removeClass('focused');

        if (direction === 'down') {
            focusedIndex = Math.min(focusedIndex + 1, $items.length - 1);
        } else if (direction === 'up') {
            focusedIndex = Math.max(focusedIndex - 1, 0);
        }

        $items.eq(focusedIndex).addClass('focused');

        if ($items.eq(focusedIndex).length) {
            $items.eq(focusedIndex)[0].scrollIntoView({ block: 'nearest' });
        }
    }

    function openFocusedResult() {
        var $items = $('.erp-result-item');

        if (focusedIndex >= 0 && $items.eq(focusedIndex).length) {
            window.location.href = $items.eq(focusedIndex).data('url');
        }
    }

    function cycleFilter() {
        var $filters = $('.erp-filter-pill');

        if (!$filters.length) {
            return;
        }

        var currentIndex = $filters.index($('.erp-filter-pill.active'));
        var nextIndex = (currentIndex + 1) % $filters.length;
        var nextFilter = $filters.eq(nextIndex).data('filter');

        setFilter(nextFilter);
        scheduleSearch(true);
    }

    $(document).on('click', '#erp-search-trigger', function () {
        openGlobalSearch();
    });

    $(document).on('click', '#erp-search-sidebar-trigger', function (e) {
        e.preventDefault();
        openGlobalSearch();
    });

    $(document).on('click', '.erp-filter-pill', function () {
        var newFilter = $(this).data('filter');

        if (currentFilter === newFilter) {
            return;
        }

        setFilter(newFilter);
        scheduleSearch(true);
    });

    $(document).on('input', '#erp-global-search', function () {
        var val = $.trim($(this).val());

        $('#erp-search-clear').toggleClass('visible', val.length > 0);
        focusedIndex = -1;

        if (!val) {
            clearTimeout(inputDebounceTimer);

            if (globalSearchXhr && globalSearchXhr.readyState !== 4) {
                globalSearchXhr.abort();
            }

            $('#erp-search-loading').removeClass('visible');
            $('#erp-global-search-results').html('');
            $('#erp-search-shortcuts').show();
            $('#erp-search-quick-actions').show();
            lastSearchKey = '';
            return;
        }

        $('#erp-search-shortcuts').hide();
        $('#erp-search-quick-actions').hide();

        if (val.length < MIN_SEARCH_LENGTH) {
    $('#erp-search-loading').removeClass('visible');
    $('#erp-global-search-results').html('');
    return;
}

$('#erp-search-loading').addClass('visible');
showSearchAnimation();

        scheduleSearch(false);
    });

    $(document).on('click', '#erp-search-clear', function () {
        if (globalSearchXhr && globalSearchXhr.readyState !== 4) {
            globalSearchXhr.abort();
        }

        clearTimeout(inputDebounceTimer);

        $('#erp-global-search').val('').focus();
        $('#erp-search-clear').removeClass('visible');
        $('#erp-search-loading').removeClass('visible');
        $('#erp-global-search-results').html('');
        $('#erp-search-shortcuts').show();
        $('#erp-search-quick-actions').show();
        focusedIndex = -1;
        lastSearchKey = '';
    });

    $(document).on('click', '#erp-global-search-overlay', function (e) {
        if (e.target.id === 'erp-global-search-overlay') {
            closeGlobalSearch();
        }
    });

    $(document).on('keydown', function (e) {
        var key = e.key ? e.key.toLowerCase() : '';

        if (e.altKey && e.shiftKey && (key === 's' || e.keyCode === 83)) {
            e.preventDefault();
            openGlobalSearch();
            return false;
        }

        if ($('#erp-global-search-overlay').is(':hidden')) {
            return;
        }

        if (e.key === 'Escape' || e.keyCode === 27) {
            e.preventDefault();
            closeGlobalSearch();
            return;
        }

        if (e.key === 'ArrowDown' || e.keyCode === 40) {
            e.preventDefault();
            moveFocus('down');
            return;
        }

        if (e.key === 'ArrowUp' || e.keyCode === 38) {
            e.preventDefault();
            moveFocus('up');
            return;
        }

        if (e.key === 'Enter' || e.keyCode === 13) {
            e.preventDefault();
            openFocusedResult();
            return;
        }

        if (e.key === 'Tab' || e.keyCode === 9) {
            e.preventDefault();
            cycleFilter();
            return;
        }
    });

    $(document).on('click', '.erp-quick-action-item', function () {
        var action = $(this).data('action');

        if (action === 'product_create') {
            window.location.href = '/products/create';
            return;
        }

        if (action === 'invoice_create') {
            window.location.href = '/pos/create';
            return;
        }

        if (action === 'customer_add') {
            window.location.href = '/contacts?type=customer&open_create=1';
            return;
        }
    });

    $(document).ready(function () {
        var params = new URLSearchParams(window.location.search);
        var type = params.get('type');
        var openCreate = params.get('open_create');

        if (type === 'customer' && openCreate === '1') {
            setTimeout(function () {
                $('.contact_modal').load('/contacts/create?type=customer', function () {
                    $('.contact_modal').modal('show');

                    setTimeout(function () {
                        $('.contact_modal').find('#contact_type').val('customer').trigger('change');
                    }, 200);
                });
            }, 400);
        }
    });

})(jQuery);