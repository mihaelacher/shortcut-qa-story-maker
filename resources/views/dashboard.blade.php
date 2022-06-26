<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>QA Shortcut App</title>
    <link rel="shortcut icon" href="{{Cdn::url()}}/img/icon.png">
    @include('blocks.cssresources')
</head>
<body class="antialiased">
@include('blocks.jsresources')


<div>
    <div>
        <div class="select-container">
            <label>DEV ITERATION:</label>
            <select class="form-control dev-stories-iteration-select2" tabindex="-1">
                @foreach($iterations ?? [] as $id => $iteration)
                    <option value="{{ $id }}">{{ $iteration }}</option>
                @endforeach
            </select>
        </div>

        <div class="select-container">
            <label>QA ITERATION:</label>
            <select class="form-control qa-stories-iteration-select2" tabindex="-1">
                <option value="" class="clearFilterOption"></option>
                @foreach($iterations ?? [] as $id => $iteration)
                    <option value="{{ $id }}">{{ $iteration }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div>
        <button class="btn btn-primary js-mass-create" href="javascript:void(0)" disabled>CREATE QA STORIES FOR SELECTION</button>
    </div>

    <div class="page-loader" style="display:none;z-index: 501; margin-left: 50%;" data-html2canvas-ignore>
        <img src="{{Cdn::url()}}/img/loading.gif" alt="" class="loading">
        <span>Loading...</span>
    </div>

    <div class='table-container'></div>

</div>
</body>
</html>
