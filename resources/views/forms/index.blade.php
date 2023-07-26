@extends('forms.layout')

@section('content')
<div class="row">
    <div class="col-lg-12 margin-tb">
        <div class="pull-left">
            <h2>Form Contact Bla bla bla</h2>
        </div>
        <div class="pull-right">
            <a class="btn btn-success" href="{{ route('forms.create')}}">Kembali</a>
        </div>
    </div>
</div>

@if ($message = Session::get('success'))
    <div class="alert alert-success">
            <p>{{$message}}</p>
    </div>
@endif

<table class="table table-bordered">
    <tr>
        <th>No</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Website</th>
        <th>Message</th>
    </tr>
    @foreach($forms as $form)
    <tr>
        <td>{{++$i}}</td>
        <td>{{$form->name}}</td>
        <td>{{$form->email}}</td>
        <td>{{$form->phone}}</td>
        <td>{{$form->website}}</td>
        <td>{{$form->message}}</td>
        <td>
            <form action="{{ route('forms.destroy',$form->id) }}" method="POST">   
                    <a class="btn btn-info" href="{{ route('forms.show',$form->id) }}">Show</a>
                    <a class="btn btn-primary" href="{{ route('forms.edit',$form->id) }}">Edit</a>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
    </tr>
    @endforeach
</table>    

@endsection