<div class="modal fade" id="delete-confirmation" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmation" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="deleteConfirmationModalLabel">{{ trans('rinvex.fort::backend/common.delete.heading') }}</h4>
            </div>
            <div class="modal-body">
                {!! trans('rinvex.fort::backend/common.delete.body', ['type' => $type]) !!}
            </div>
            <div class="modal-footer">
                <form id="delete-item-form" action="#" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('rinvex.fort::backend/common.cancel') }}</button>
                    <button type="submit" class="btn btn-danger btn-ok"><i class="fa fa-trash-o"></i> {!! trans('rinvex.fort::backend/'.str_plural($type).'.delete') !!}</button>
                </form>
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
