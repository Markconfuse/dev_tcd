(function($) {
    $.fn.dataTableFilter = function(table, options) {
        var settings = $.extend({
            container: '.toolbar',
            id: 'status_filter',
            label: 'Filter',
            defaultValue: '',
            options: [],
            margin: '10px'
        }, options);

        var html = `
            <label style="font-weight: normal; margin-bottom: 0; margin-left: ${settings.margin}">
                ${settings.label}: 
                <select id="${settings.id}" class="form-control form-control-sm" style="display: inline-block; width: auto; margin-left: 5px;">
                    ${settings.options.map(opt => `
                        <option value="${opt.value}" ${settings.defaultValue === opt.value ? 'selected' : ''}>
                            ${opt.label}
                        </option>
                    `).join('')}
                </select>
            </label>
        `;

        $(settings.container).css('display', 'inline-block').css('float', 'right').append(html);

        $(document).off('change', `#${settings.id}`).on('change', `#${settings.id}`, function() {
            table.draw();
        });

        return this;
    };
}(jQuery));

class data_table_filter {
    constructor(table, options = {}) {
        $(document).dataTableFilter(table, options);
    }

    static getValue(id) {
        return $(`#${id}`).val();
    }
}

