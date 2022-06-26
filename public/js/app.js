let app = {
    initSelect2Iterations: function () {
        $.fn.select2.defaults.set("width", "100%");
        $('.dev-stories-iteration-select2').select2({
            selectOnClose: false,
            language: "select2/i18n/bg",
            width: "100%",
            placeholder: "DEV stories iteration",
        });

        $('.qa-stories-iteration-select2').select2({
            selectOnClose: false,
            language: "select2/i18n/bg",
            width: "100%",
            placeholder: "QA stories iteration",
        });
    },

    attachDevStorySelect2EventListener: function () {
        $('.dev-stories-iteration-select2').on('select2:select', function () {
            let devStoryId = $(this).val();
            let devStoriesTableSelector = $('#devStoriesTable');

            if (devStoriesTableSelector.length) {
                devStoriesTableSelector.DataTable().destroy();
                devStoriesTableSelector.remove();
            }

            app.loaderShow();

            $.ajax({
                method: "GET",
                url: '/ajax/getShortcutDevStoriesTable',
                data: {'devStoryId': devStoryId}
            })
                .success(function (data) {
                    $('.table-container').html(data);
                    app.initDevStoriesDatatable();
                    app.loaderHide();
                })
        });
    },

    triggerDevStorySelect2Change: function () {
        $('.dev-stories-iteration-select2').trigger('change').trigger('select2:select');
    },

    initDevStoriesDatatable: function () {
        $('#devStoriesTable').DataTable({
            'paginate': false,
            'initComplete': function () {
                app.handleMassStoriesCreate();
            }
        });
    },

    handleMassStoriesCreate: function () {
        var jqCreateStoriesButton = $('.js-mass-create');
        var jqStoryCheckboxes = $('.js-story-checkbox');
        var jqStoriesCheckboxAll = $('.js-stories-checkbox-all');

        jqStoriesCheckboxAll.change(function () {
            if (this.checked) {
                jqStoryCheckboxes.prop('checked', true);
                jqCreateStoriesButton.attr('disabled', false);
            } else {
                jqStoryCheckboxes.prop('checked', false);
                jqCreateStoriesButton.attr('disabled', true);
            }
        });

        jqStoryCheckboxes.change(function () {
            if ($('.js-story-checkbox:checked').length) {
                jqCreateStoriesButton.attr('disabled', false);
            } else {
                jqCreateStoriesButton.attr('disabled', true);
            }
        })
    },

    preparePostCreateStoriesData: function () {
        let table = $('#devStoriesTable').DataTable();
        let postData = {};

        table.cells().eq(0).each(function (index) {
            let cell = table.cell(index);
            let cellNode = cell.node();

            let input = $(cellNode).children('input');
            let select = $(cellNode).children('select');

            if (input.length) {
                let storyId = input.data('story-id');
                let isChecked = input.is(':checked');

                if (isChecked) {
                    if (input.hasClass('js-story-checkbox')) {
                        postData[storyId] = {};
                        postData[storyId]['creators'] = [];
                        postData[storyId]['owner_id'] = null;
                    } else if (postData[storyId] !== undefined) {
                        postData[storyId]['creators'].push(input.data('creator-name'));
                    }
                }
            }

            if (select.length) {
                let storyId = select.data('story-id');

                if (postData[storyId] !== undefined) {
                    postData[storyId]['owner_id'] = select.val();
                }
            }
        });

        return postData;
    },

    attachCreateQAStoriesBtnHandler: function () {
        var jqCreateStoriesButton = $('.js-mass-create');

        jqCreateStoriesButton.on('click', function () {
            if (!$('.js-story-checkbox:checked').length) {
                return;
            }

            let button = $(this);

            button.attr('disabled', true);

            app.loaderShow();

            let postData = app.preparePostCreateStoriesData();

            $.ajax({
                method: "POST",
                url: '/ajax/createShortcutQAStories',
                data: {
                    'selectedIds': JSON.stringify(postData),
                    'qaIterationId': $('.qa-stories-iteration-select2').val()
                }
            })
                .success(function () {
                    button.attr('disabled', false);
                    app.loaderHide();
                });
        });
    },

    loaderShow: function () {
        $('.page-loader').show();
    },

    loaderHide: function () {
        $('.page-loader').hide();
    },

    init: function () {
        $(document).ready(function () {
            app.initSelect2Iterations();
            app.attachDevStorySelect2EventListener();
            app.triggerDevStorySelect2Change();
            app.attachCreateQAStoriesBtnHandler();
        });
    }
}

app.init();
