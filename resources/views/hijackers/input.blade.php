@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Submit Hijacker Links</div>

                <div class="panel-body">
                  <form method="post" action="/hijackers/input">
                    {{csrf_field()}}
                    @for($i = 0; $i < 20; $i++)
                    <div class="row">
                      <label class="form-group col-md-2">Hijacker Name</label>
                      <div class="col-md-4">
                        <input type="text" name="name_{{$i}}" class="form-control">
                      </div>
                      <label class="form-group col-md-2">Link</label>
                      <div class="col-md-4">
                        <input type="text" name="link_{{$i}}" class="form-control">
                      </div>
                    </div>
                    @endfor
                    <div class="row">
                      <label class="form-group col-md-2"></label>
                      <div class="col-md-10">
                        <button class="btn btn-primary">Submit</button>
                      </div>
                    </div>
                  </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
