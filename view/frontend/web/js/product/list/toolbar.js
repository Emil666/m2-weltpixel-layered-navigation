define([
    "jquery",
    "jquery/ui",
    "Magento_Theme/js/view/messages",
    "ko",
    "Magento_Catalog/js/product/list/toolbar"
], function ($, ui, messageComponent, ko) {
    /**
     * ProductListToolbarForm Widget - this widget is setting cookie and submitting form according to toolbar controls
     */
    $.widget('mage.productListToolbarForm', $.mage.productListToolbarForm, {

        options:
            {
                modeControl: '[data-role="mode-switcher"]',
                directionControl: '[data-role="direction-switcher"]',
                orderControl: '[data-role="sorter"]',
                limitControl: '[data-role="limiter"]',
                pagerControl: '[data-role="pager"], .pages-items a',
                mode: 'product_list_mode',
                direction: 'product_list_dir',
                order: 'product_list_order',
                limit: 'product_list_limit',
                pager: 'p',
                modeDefault: 'grid',
                directionDefault: 'asc',
                orderDefault: 'position',
                limitDefault: '9',
                pagerDefault: '1',
                productsToolbarControl: '.toolbar.toolbar-products',
                productsListBlock: '#layer-product-list',
                layeredNavigationFilterBlock: '.block.filter',
                filterItemControl: '.block.filter .item a, .block.filter .filter-clear,.block.filter .swatch-option-link-layered, .pages-items a',
                url: ''
            },

        _create: function () {
            this._super();
            this._bind($(this.options.pagerControl), this.options.pager, this.options.pagerDefault);
            $(this.options.filterItemControl)
                .off('click.' + this.namespace + 'productListToolbarForm')
                .on('click.' + this.namespace + 'productListToolbarForm', {}, $.proxy(this.applyFilterToProductsList, this))
            ;

        },
        _bind: function (element, paramName, defaultValue) {
            /**
             * Prevent double binding of these events because this component is being applied twice in the UI
             */
            if (element.is("select")) {
                element
                    .off('change.' + this.namespace + 'productListToolbarForm')
                    .on('change.' + this.namespace + 'productListToolbarForm', {
                        paramName: paramName,
                        default: defaultValue
                    }, $.proxy(this._processSelect, this));
            } else {
                element
                    .off('click.' + this.namespace + 'productListToolbarForm')
                    .on('click.' + this.namespace + 'productListToolbarForm', {
                        paramName: paramName,
                        default: defaultValue
                    }, $.proxy(this._processLink, this));
            }
        },
        applyFilterToProductsList: function (evt) {
            var link = $(evt.currentTarget),
                linkA = link.attr('href'),
                urlParts = (typeof linkA !== 'undefined') ? linkA.split('?') : '',
                currentUrl = window.location.href,
                isMulti = (link.attr('data-is-multi')) ? link.data('is-multi') : 0,
                parentElem = link.parent(),
                clickOpt = (parentElem.attr('data-path-opt')) ? parentElem.data('opt-path') : link.data('opt-path'),
                c = currentUrl.split('?');
            var reqeustParams = (typeof urlParts[1] === 'undefined') ? '' : urlParts[1];

            var mergedPath = reqeustParams;

            if (reqeustParams.length > 0 && typeof c[1] !== 'undefined') {
                mergedPath = this.compareMergeParams(c[1], reqeustParams, clickOpt, isMulti);
            }

            self.elem = link;
            this.makeAjaxCall(urlParts[0], mergedPath);
            evt.preventDefault();

        },
        compareMergeParams: function (currentParamsStr, newParamsStr, clickOpt, isMulti) {
            var a = currentParamsStr.split('&');
            var b = newParamsStr.split('&');
            var c = (typeof clickOpt !== 'undefined') ? clickOpt.split('=') : '';
            var res = '';
            a.sort();
            b.sort();
            for (var i = 0; i < b.length; i++) {
                var paramStr = b[i].split('=')[0],
                    paramVal = decodeURIComponent(b[i].split('=')[1]),
                    paramArr = paramVal.split(',');
                for (var j = 0; j < a.length; j++) {

                    if (typeof a[j] === 'undefined') {
                        continue;
                    }

                    var existParamStr = a[j].split('=')[0],
                        existParamVal = decodeURIComponent(a[j].split('=')[1]),
                        existParamArr = existParamVal.split(','),
                        matchParams = '';

                    if(paramStr == 'p' || paramStr == 'q' || paramStr == 'ajax') {
                        continue;
                    }

                    if (paramStr !== existParamStr) {
                        //res += paramStr + '=' + paramVal + '&';
                        continue;
                    }
                    for (var z = 0; z < existParamArr.length; z++) {
                        if (paramArr.indexOf(existParamArr[z]) !== -1) {
                            matchParams = (matchParams.length === 0) ? existParamArr[z] : matchParams + ',' + existParamArr[z];
                        }
                    }

                    if (paramStr === existParamStr && paramVal !== existParamVal && matchParams.length === 0 && isMulti != 0) {
                        paramVal = existParamVal + ',' + paramVal;
                    }
                    else if( c[0] == paramStr && c[1] == matchParams ){
                        var filteredArray = existParamArr.filter(function(e) { return e !== matchParams })
                        paramVal = (filteredArray.length > 0) ? filteredArray.join(',') : '';
                    }
                    else  {
                        //paramVal = matchParams;
                    }
                }

                if(paramVal) {
                    res += paramStr + '=' + paramVal + '&';
                }

            }
            res = res.slice(0, -1);

            return res;
        },
        updateUrl: function (url, paramData) {
            if (!url) {
                return;
            }
            if (paramData && paramData.length > 0) {
                url += '?' + paramData;
            }
            url = this.removeQueryStringParameter('ajax', url);
            if (typeof history.replaceState === 'function') {
                history.replaceState(null, null, url);
            }
        },

        getParams: function (urlParams, paramName, paramValue, defaultValue) {
            var decode = window.decodeURIComponent,
                paramData = {},
                parameters, i;

            for (i = 0; i < urlParams.length; i++) {
                parameters = urlParams[i].split('=');
                paramData[decode(parameters[0])] = parameters[1] !== undefined ?
                    decode(parameters[1].replace(/\+/g, '%20')) :
                    '';
            }

            /** get the real attr name from param */
            var paramValueArr = paramValue.split('~'),
                paramValueNew = paramValueArr[0];

            paramData[paramName] = paramValueNew;

            /** get the given direction from param */
            var directionName = this.options.direction;
            if (paramValueArr.length == 2 && paramName != directionName) {
                paramData[directionName] = paramValueArr[1];
            }

            return $.param(paramData);
        },
        _updateContent: function (content) {
            $(this.options.productsToolbarControl).remove();
            if (content.products_list) {
                $(this.options.productsListBlock).html(content.products_list);
                $(this.options.productsListBlock).trigger('contentUpdated');

            }

            if (content.filters) {
                $(this.options.layeredNavigationFilterBlock).replaceWith(content.filters);
                $(this.options.layeredNavigationFilterBlock).trigger('contentUpdated');
            }

            if (content.dataLayer) {
                var dlObjects = JSON.parse(content.dataLayer);
                window.dataLayer = window.dataLayer || [];
                for (var i in dlObjects) {
                    window.dataLayer.push(dlObjects[i]);
                }
           }

            $('body').trigger('contentUpdated');
        },
        reinitializeIas: function() {
            if(require.defined('ias') && window.ajaxCatalog == 'infiniteScroll') {
                jQuery.ias().destroy();
                jQuery(function($) {
                    var config = {
                        container:       '.products.wrapper .product-items',
                        item:            '.product-item',
                        pagination:      '.toolbar .pages, .toolbar .limiter',
                        next:            '.pages .action.next',
                        negativeMargin:  window.negativeMargin
                    };
                    /** added to prevent jquery to add extra "_" parameter to link */
                    $.ajaxSetup({ cache: true });

                    /** add infinite-scroll class */
                    $(config.container).closest('.column.main').addClass('infinite-scroll');
                    /** load ias */
                    var ias = $.ias(config);

                    ias.getNextUrl = function(container) {
                        if (!container) {
                            container = ias.$container;
                        }
                        /** always take the last matching item + fix to be protocol relative */
                        var nexturl = $(ias.nextSelector, container).last().attr('href');
                        if(typeof nexturl !== "undefined") {
                            if (window.location.protocol == 'https:') {
                                nexturl = nexturl.replace('http:', window.location.protocol);
                            } else {
                                nexturl = nexturl.replace('https:', window.location.protocol);
                            }
                            nexturl = window.ajaxInfiniteScroll.removeQueryStringParameter('_', nexturl);
                        }

                        return nexturl;
                    };

                    /** adds extra functionality to Infinite AJAX Scroll */
                    ias.extension(new IASPagingExtension());
                    ias.on('pageChange', function(pageNum, scrollOffset, url) {
                        window.page = pageNum;
                    });

                    /** added to prevent jquery to add extra "_" parameter to link */
                    ias.on('load', function(event) {
                        var url = event.url;
                        event.ajaxOptions.cache = true;
                        event.url = window.ajaxInfiniteScroll.removeQueryStringParameter('_', event.url);
                    });

                    ias.on('loaded', function(data, items) {
                        /** fix lazy load images */
                        window.ajaxInfiniteScroll.reloadImages(items);
                        window.ajaxInfiniteScroll.dataLayerUpdate(data);
                    });
                    /** fix ajax add to cart */
                    ias.on('rendered', function(items) {
                        window.ajaxInfiniteScroll.fixAddToCart();
                        /** re-init Pearl related elements */
                        window.ajaxInfiniteScroll.reloadQuickView();
                        window.ajaxInfiniteScroll.reloadCategoryPage();
                        /** update next/prev head links */
                        if (window.showCanonical == 1) {
                            window.ajaxInfiniteScroll.reloadCanonicalPrevNext();
                        }
                        $('.product-item-info a').each(function() {
                            if( typeof $(this).attr('data-item-page') === 'undefined') {
                                $(this).attr('data-item-page', window.page);
                            }
                        })
                    });


                    /** adds a text when there are no more pages to load */
                    ias.extension(new IASNoneLeftExtension({
                        html: '<span class="ias-no-more">' + window.textNoMore + '</span>'

                    }));
                    /** displays a customizable loader image when loading a new page */
                    var loadingHtml  = '<div class="ias-spinner">';
                    loadingHtml += '<img src="{src}"/>';
                    loadingHtml += '<span>' + window.textLoadingMore + '</span>';
                    loadingHtml += '</div>';
                    ias.extension(new IASSpinnerExtension({
                        src: window.loadingImage,
                        html: loadingHtml
                    }));

                    /** adds "Load More" and "Load Previous" button */
                    if (window.LoadMore > 0) {
                        ias.extension(new IASTriggerExtension({
                            text: window.textNext,
                            html: '<button class="button action ias-load-more" type="button"><span>{text}</span></button>',
                            textPrev: 'Load previous items',
                            htmlPrev: '<button class="button action ias-load-prev" type="button"><span>{text}</span></button>',
                            offset: window.LoadMore
                        }));
                    } else {
                        ias.extension(new IASTriggerExtension({
                            textPrev: 'Load previous items',
                            htmlPrev: '<button class="button action ias-load-prev" type="button"><span>{text}</span></button>',
                            offset: 1000
                        }));
                    }
                    /** adds history support */
                    ias.extension(new IASHistoryExtension({prev: '.previous'}));

                });
            }
        },
        updateContent: function (content) {
            this._updateContent(content)
        },


        changeUrl: function (paramName, paramValue, defaultValue) {
            if(paramName == 'p') {
                return;
            }
            var urlPaths = this.options.url.split('?'),
                baseUrl = urlPaths[0],
                urlParams = urlPaths[1] ? urlPaths[1].split('&') : [],
                paramData = this.getParams(urlParams, paramName, paramValue, defaultValue);

            this.makeAjaxCall(baseUrl, paramData);
        },

        makeAjaxCall: function (baseUrl, paramData) {
            var self = this;

            var jqxhr = $.ajax({
                url: baseUrl,
                data: (paramData && paramData.length > 0 ? paramData + '&ajax=1' : 'ajax=1'),
                type: 'get',
                dataType: 'json',
                cache: true,
                showLoader: true,
                beforeSend: function (xhr){
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    if(typeof window.page === 'undefined') {
                        window.page = $('.product-item-info a').last().attr('data-item-page');
                    }
                }
            }).done(function (response) {
                if (response.success) {
                    self.updateUrl(baseUrl, paramData);
                    self.updateContent(response.html);
                    self.slidersUpdate();
                    self.setMessage({
                        type: 'success',
                        text: 'Sections have been updated'
                    });
                } else {
                    var msg = response.error_message;
                    if (msg) {
                        self.setMessage({
                            type: 'error',
                            text: msg
                        });
                    }
                }
            }).fail(function (error) {
                self.setMessage({
                    type: 'error',
                    text: 'Sorry, something went wrong. Please try again later.'
                });
            });

            jqxhr.always(function() {
                self.reinitializeIas();
                $('.product.photo.product-item-photo').on('click', function(e) {
                    e.preventDefault();
                    var page = $(this).attr('data-item-page');
                    var url = window.location.href;
                    self.resetIasPagination(page, url);
                    var href = $(this).attr('href');
                    window.location.href = href;
                });
                $('.product-item-info a').each(function() {
                    if( typeof $(this).attr('data-item-page') === 'undefined') {
                        $(this).attr('data-item-page', window.page);
                    }
                })
                self.resetPage();
            });
        },
        resetIasPagination: function(page, url) {
            if(require.defined('ias') && window.ajaxCatalog == 'infiniteScroll') {
                jQuery.ias().destroy();
                var newUrl = url.replace(/(p=).*?(&|$)/, '$1' + page + '$2');
                window.history.replaceState("", "", newUrl);


            }
        },
        setMessage: function (obj) {
            var messages = ko.observableArray([obj]);
            messageComponent().messages({
                messages: messages
            });
        },
        slidersUpdate: function () {
            $('.wp-slide-in').not(':first').remove();
            $('.wp-slide-out').not(':first').remove();
            $('.wp-filters').not(':first').remove();
            $('.wp-ln-overlay').not(':first').remove();
            $('.wp-ln-slider-js').not(':first').remove();
            $('.wp-ln-selected-js').not(':first').remove();
        },
        resetPage: function () {
            //$('.slide-in-filter').hide('slide', {direction: "left"}, 500, function () {
            $('body').css({'height': 'auto', 'overflow': 'auto'});
            $('.wp-ln-overlay').hide();
            $('div.page-header').css({'z-index': '10'});
            $('nav.navigation').css({'z-index': '3'});
            $('.block-search, a.logo').css({'z-index': '5'});
            $('.page-wrapper .nav-sections:not(.sticky-header-nav)').removeAttr('style');
            //});
        },
        markSelected: function () {
            var elem = self.elem.parent();
            if(elem.hasClass('wp-ln-selected')) {
                elem.removeClass('wp-ln-selected');

            } else {
                elem.addClass('wp-ln-selected');
            }

        },
        removeQueryStringParameter: function (key, url)
        {
            if (!url) url = window.location.href;
            var hashParts = url.split('#'),
                regex = new RegExp("([?&])" + key + "=.*?(&|#|$)", "i");

            if (hashParts[0].match(regex)) {
                url = hashParts[0].replace(regex, '$1');
                url = url.replace(/([?&])$/, '');
                if (typeof hashParts[1] !== 'undefined' && hashParts[1] !== null)
                    url += '#' + hashParts[1];
            }

            return url;
        }

    });

    return $.mage.productListToolbarForm;
});