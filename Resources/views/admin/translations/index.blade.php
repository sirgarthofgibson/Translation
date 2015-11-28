@extends('layouts.master')

@section('content-header')
    <h1>
        {{ trans('translation::translations.title.translations') }}
    </h1>
    <ol class="breadcrumb">
        <li><a href="{{ route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ trans('core::core.breadcrumb.home') }}</a></li>
        <li class="active">{{ trans('translation::translations.title.translations') }}</li>
    </ol>
@stop

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="btn-group pull-right" style="margin: 0 15px 15px 0;">
                    <a href="" class="btn btn-flat btn-primary jsClearTranslationCache"><i class="fa fa-refresh"></i> {{ trans('translation::translations.Clear translation cache') }}</a>
                </div>
            </div>
            <div class="box box-primary">
                <div class="box-header">
                </div>
                <!-- /.box-header -->
                <div class="box-body">
                    <table class="data-table table table-bordered table-hover">
                        <thead>
                        <tr>
                            <th>Key</th>
                            <?php foreach (config('laravellocalization.supportedLocales') as $locale => $language): ?>
                                <th>{{ $locale }}</th>
                            <?php endforeach; ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php if (isset($translations)): ?>
                        <?php foreach ($translations->all() as $key => $translationGroup): ?>
                        <tr>
                            <td>{{ $key }}</td>
                            <?php foreach (config('laravellocalization.supportedLocales') as $locale => $language): ?>
                                <td>
                                    <a class="translation" data-pk="{{ $locale }}__-__{{ $key }}">{{ array_get($translationGroup, $locale, null) }}</a>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <th>{{ trans('core::core.table.created at') }}</th>
                            <?php foreach (config('laravellocalization.supportedLocales') as $locale => $language): ?>
                            <th>{{ $locale }}</th>
                            <?php endforeach; ?>
                        </tr>
                        </tfoot>
                    </table>
                    <!-- /.box-body -->
                </div>
                <!-- /.box -->
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $( document ).ready(function() {
            $('.jsClearTranslationCache').on('click',function (event) {
                event.preventDefault();
                var $self = $(this);
                $self.find('i').toggleClass('fa-spin');
                $.ajax({
                    type: 'POST',
                    url: '{{ route('api.translation.translations.clearCache') }}',
                    data: {_token: '{{ csrf_token() }}'},
                    success: function() {
                        $self.find('i').toggleClass('fa-spin');
                    }
                });
            });
        });
        $(function() {
            $('a.translation').editable({
                url: function(params) {
                    var splitKey = params.pk.split("__-__");
                    var locale = splitKey[0];
                    var key = splitKey[1];
                    var value = params.value;

                    if (! locale || ! key) {
                        return false;
                    }

                    $.ajax({
                        url: '{{ route("api.translation.translations.update") }}',
                        method: 'POST',
                        data: {
                            locale: locale,
                            key: key,
                            value: value,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(res) {
                        }
                    })
                },
                type: 'textarea',
                mode: 'inline',
                send: 'always', /* Always send, because we have no 'pk' which editable expects */
                inputclass: 'translation_input'
            });
        });
    </script>
    <?php $locale = locale(); ?>
    <script type="text/javascript">
        $(function () {
            $('.data-table').dataTable({
                "paginate": true,
                "lengthChange": true,
                "filter": true,
                "sort": true,
                "info": true,
                "autoWidth": true,
                "language": {
                    "url": '<?php echo Module::asset("core:js/vendor/datatables/{$locale}.json") ?>'
                }
            });
        });
    </script>
@stop
