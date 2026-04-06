(function($) {
    $.fn.dataTableFilter = function(table, options) {
        var settings = $.extend({
            container: '.dataTables_filter',
            id: 'status_filter',
            label: 'Filter',
            defaultValue: '',
            options: [],
            margin: '0 10px',
            column: null
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

        $(settings.container).prepend(html);

        $(document).off('change', `#${settings.id}`).on('change', `#${settings.id}`, function() {
            var value = $(this).val();
            if (settings.column !== null) {
                table.column(settings.column).search(value).draw();
            } else {
                table.draw();
            }
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

