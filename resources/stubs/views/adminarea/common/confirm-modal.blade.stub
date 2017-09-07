<div class="modal fade" id="delete-confirmation" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmation" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                {{ Form::button('<span aria-hidden="true">&times;</span>', ['class' => 'close', 'data-dismiss' => 'modal', 'aria-label' => 'Close']) }}
                <h4 class="modal-title" id="deleteConfirmationModalLabel">{{ trans('common.delete_confirmation') }}</h4>
            </div>
            <div class="modal-body">
                {!! trans('common.delete_confirmation_body', ['type' => $type]) !!}
            </div>
            <div class="modal-footer">
                {{ Form::open(['id' => 'delete-item-form', 'method' => 'delete']) }}
                    {{ Form::button(trans('common.cancel'), ['class' => 'btn btn-default', 'data-dismiss' => 'modal']) }}
                    {{ Form::button('<i class="fa fa-trash-o"></i> '.trans('common.delete'), ['class' => 'btn btn-danger', 'type' => 'submit']) }}
                {{ Form::close() }}
            </div>
        </div>
    </div>
</div>

@section('scripts')
    <script>
        $('#delete-confirmation').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Button that triggered the modal
            var name = button.data('item-name'); // Extract info from data-* attributes
            var href = button.data('item-href'); // Extract info from data-* attributes

            var modal = $(this);
            modal.find('.item-name').text(name);
            modal.find('#delete-item-form').attr('action', href);
        });
    </script>
@endsection
