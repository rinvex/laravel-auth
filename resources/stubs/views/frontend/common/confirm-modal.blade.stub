<div class="modal fade" id="delete-confirmation" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmation" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                {{ Form::button('<span aria-hidden="true">&times;</span>', ['class' => 'close', 'data-dismiss' => 'modal', 'aria-label' => 'Close']) }}
                <h4 class="modal-title" id="deleteConfirmationModalLabel"></h4>
            </div>
            <div class="modal-body">
                <strong>{{ trans('common.warning') }}:</strong> <span class="warning"></span>
            </div>
            <div class="modal-footer">
                {{ Form::open(['id' => 'delete-item-form', 'method' => 'delete']) }}
                    {{ Form::hidden('confirm', true) }}
                    {{ Form::button(trans('common.close'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) }}
                    {{ Form::button('<i class="fa fa-trash-o"></i> <span class="button"></span>', ['class' => 'btn btn-danger modal-submit', 'type' => 'submit']) }}
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script>
        $('#delete-confirmation').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var type = button.data('item-type'); // Extract info from data-* attributes
            var href = button.data('item-href'); // Extract info from data-* attributes

            var single_heading = '{{ trans('messages.sessions.flush_single_heading') }}';
            var single_body = '{!! trans('messages.sessions.flush_single_body') !!}';

            var all_heading = '{{ trans('messages.sessions.flush_all_heading') }}';
            var all_body = '{!! trans('messages.sessions.flush_all_body') !!}';

            var modal = $(this);
            console.log(modal.find('.modal-submit').text);
            console.log(modal.find('.modal-submit').text());
            modal.find('.modal-title').text(type == 'single' ? single_heading : all_heading);
            modal.find('.modal-body .warning').text(type == 'single' ? single_body : all_body);
            modal.find('.modal-submit .button').text(type == 'single' ? single_heading : all_heading);
            modal.find('#delete-item-form').attr('action', href);
        });
    </script>
@endsection
