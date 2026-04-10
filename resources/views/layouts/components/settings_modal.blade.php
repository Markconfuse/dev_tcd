<!-- Settings Modal -->
<div class="modal fade" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <div class="modal-content">

      <div class="modal-header bg-info">
        <h5 class="modal-title" id="settingsModalLabel"><i class="fas fa-cogs"></i> System Settings</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body p-0">
        <div class="row no-gutters h-100">
          <div class="col-3 col-sm-3 bg-light" style="border-right: 1px solid #dee2e6;">
            <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical"
              style="border-bottom: 0;">
              <a class="nav-link active" id="vert-tabs-general-tab" data-toggle="pill" href="#vert-tabs-general"
                role="tab" aria-controls="vert-tabs-general" aria-selected="true" style="border-radius: 0;">General</a>

              @if(Session('userData')->account_id == 57610 || Session('userData')->account_id == 57615 || Session('userData')->role_name == 'admin' || Session('userData')->role_name == 'super_user')
                <a class="nav-link" id="vert-tabs-engineer-reminders-tab" data-toggle="pill"
                  href="#vert-tabs-engineer-reminders" role="tab" aria-controls="vert-tabs-engineer-reminders"
                  aria-selected="false" style="border-radius: 0;">Engineer Reminders</a>
              @endif

            </div>
          </div>
          <div class="col-9 col-sm-9">
            <div class="tab-content" id="vert-tabs-tabContent" style="padding: 20px; min-height: 480px;">
              <div class="tab-pane text-left fade show active h-100" id="vert-tabs-general" role="tabpanel"
                aria-labelledby="vert-tabs-general-tab">
                <form action="{{ route('settings.year_filter') }}" method="post" style="min-height: 440px;">
                  @csrf
                  <div class="form-group">
                    <label for="yearFilterSetting" class="mb-0">
                      Filter Data By Year
                      <i class="fas fa-question-circle text-info ml-1" data-toggle="tooltip" data-placement="right"
                        data-html="true"
                        title="<div class='text-left'><strong>This setting only affects your own account view.</strong><br>It does not change what other users see.<br><br>Use this to filter dashboard/reports by year.<br><br><strong>Not affected by this filter (always shown if still open):</strong><br>- Unread Tickets<br>- Unanswered<br>- In Progress<br>- Assigned to Me<br><br>These action lists stay visible so you do not miss pending work, even if tickets are from older years.</div>"></i>
                    </label>
                    <p class="text-muted small mb-3">Controls the default application year filter.</p>
                    <select name="year" id="yearFilterSetting" class="form-control" style="max-width: 200px;"
                      onchange="this.form.submit()">
                      @php
                        // Fetch the currently active setting or default to current year.
                        $currentYearFilter = \App\Setting::where('key', 'year_filter')->where('account_id', Session('userData')->account_id)->value('value') ?? date('Y');
                        $startYear = 2021;
                        $endYear = date('Y') + 1; 
                      @endphp
                      <option value="All" {{ $currentYearFilter == 'All' ? 'selected' : '' }}>All</option>
                      @for($y = $endYear; $y >= $startYear; $y--)
                        <option value="{{ $y }}" {{ $currentYearFilter == $y ? 'selected' : '' }}>{{ $y }}</option>
                      @endfor
                    </select>
                  </div>
                </form>
              </div>

              @if(Session('userData')->account_id == 57610 || Session('userData')->account_id == 57615 || Session('userData')->role_name == 'admin' || Session('userData')->role_name == 'super_user')
                <div class="tab-pane fade h-100" id="vert-tabs-engineer-reminders" role="tabpanel"
                  aria-labelledby="vert-tabs-engineer-reminders-tab">
                  <form action="{{ route('settings.engineer_reminder') }}" method="post"
                    style="min-height: 440px; display: flex; flex-direction: column; justify-content: space-between;">
                    @csrf
                    <div class="form-group">
                      <label for="engineerRemindersContent">Engineer Reminders Content:</label>
                      <textarea name="engineer_reminder" id="engineerRemindersContent" rows="10"
                        cols="80">{!! \App\Setting::where('key', 'engineer_reminder')->value('value') !!}</textarea>
                    </div>
                    <div class="form-group text-right mb-0">
                      <button type="button" class="btn btn-info mr-2" id="btnPreviewSettings"><i class="fas fa-eye"></i>
                        Live Preview</button>
                      <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
                    </div>
                  </form>
                </div>
              @endif

            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<div class="modal fade" id="settingsPreviewModal" tabindex="-1" style="z-index: 1060;">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning">
        <h5 class="modal-title">Review Before Sending (PREVIEW)</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body" style="min-height: 400px;">
        <div id="previewReminderBody"></div>
        <p class="text-danger mb-0 mt-3">
          <strong>Are you sure you want to mark this request as answered?</strong>
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close Preview</button>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    var initSettingsEditor = function () {
      if (window.jQuery && $.fn.summernote) {
        $('#settingsModal').on('shown.bs.modal', function () {
          var $editor = $('#engineerRemindersContent');
          if ($editor.length && !$editor.data('summernote')) {
            $editor.summernote({
              height: '300px',
              toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['view', ['fullscreen', 'codeview', 'help']],
              ]
            }).on("summernote.enter", function (we, e) {
              $(this).summernote("pasteHTML", "<br><br>");
              e.preventDefault();
            });
          }
        });
      } else {
        setTimeout(initSettingsEditor, 100);
      }
    };
    initSettingsEditor();

    $('#btnPreviewSettings').on('click', function () {
      var content = $('#engineerRemindersContent').summernote('code');
      $('#previewReminderBody').html(content);
      $('#settingsPreviewModal').modal('show');
    });

    // Initialize tooltips explicitly inside the modal
    $('[data-toggle="tooltip"]').tooltip();
  });
</script>