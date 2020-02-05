@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">

                @if (Auth::check())

                    <div class="card-header">My notes
                    <input style="margin-top: 10px;" class="form-control" v-model="searching.searchQuery" v-on:keyup="keyupFiredOnSeachingInput" value="" placeholder="Search" />
                    </div>

                    <div ref="refMessageFromRequest" class="refMessageFromRequest">

                    </div>

                    <div id="table_users_notes" class="card-body">

                        <table v-if="searching.searchedNotes.length && is_searching == true" class="table">
                            <thead class="thead-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col">Phone</th>
                                    <th scope="col">Observations</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in searching.searchedNotes">
                                    <td>@{{item.noteId}}</td>
                                    <td>@{{item.firstName}}</td>
                                    <td>@{{item.lastName}}</td>
                                    <td>@{{item.phone}}</td>
                                    <td>@{{item.observations}}</td>
                                </tr>
                            </tbody>
                        </table>

                        <table style="margin-bottom: 30px;" v-show="is_searching == false" class="table">                        
                          <thead class="thead-dark">
                            <tr>
                              <th scope="col">#</th>
                              <th scope="col">First Name</th>
                              <th scope="col">Last Name</th>
                              <th scope="col">Phone</th>
                              <th scope="col">Observations</th>
                              <th scope="col">Actions</th>
                            </tr>
                          </thead>
                          <tbody ref="refNotesTbody">
                            @foreach ($notes as $note)
                                <tr>
                                    <!-- Id of note -->
                                    <th scope="row">{{ $note->id }}</th>
                                    <!-- First name -->
                                    <td>
                                        <input ref="refFirstName_{{ $note->id }}" v-bind:disabled="editCurrentField === false || {{ $note->id }} != currentFieldId" value="{{ $note->first_name }}">
                                    </td>
                                    <!-- Last name -->
                                    <td>
                                        <input ref="refLastName_{{ $note->id }}" v-bind:disabled="editCurrentField === false || {{ $note->id }} != currentFieldId" value="{{ $note->last_name }}">
                                    </td>
                                    <!-- Phone -->
                                    <td>
                                        <input ref="refPhone_{{ $note->id }}" v-bind:disabled="editCurrentField === false || {{ $note->id }} != currentFieldId" value="{{ $note->phone }}">
                                    </td>
                                    <!-- Observations -->
                                    <td>
                                        <input ref="refObservations_{{ $note->id }}" style="width: 260px;" v-bind:disabled="editCurrentField === false || {{ $note->id }} != currentFieldId" value="{{ $note->observations }}">
                                    </td>
                                    <!-- Actions -->
                                    <td>
                                        <div class="actions">
                                            <div v-if="editCurrentField === false || {{ $note->id }} != currentFieldId" v-on:click="clickEditButton('{{ $note->id }}')" class="edit">
                                                <img src="{{ asset('img/edit_icon.png') }}" alt="">
                                            </div>
                                            <div v-if="editCurrentField === true && {{ $note->id }} == currentFieldId" v-on:click="clickSaveButton('{{ $note->id }}')" class="save">
                                                <img src="{{ asset('img/save_icon.png') }}" alt="">
                                            </div>                                        
                                            <div v-on:click="clickDeleteButton('{{ $note->id }}')" class="delete">
                                                <img src="{{ asset('img/delete_icon.png') }}" alt="">
                                            </div>                                        
                                        </div>
                                    </td>
                                </tr>                        
                            @endforeach                        
                          </tbody>
                        </table>
                        <button v-show="is_searching == false" style="float: right;" type="button" class="btn btn-primary" data-toggle="modal" data-target="#AddNoteModal">
                            Add new note
                        </button>
                        {{ $notes->appends(Request::except('page'))->links() }}
                    </div>

                @else
    
                    <div style="text-align: center;padding: 20px 0;font-size: 16px;">You are not logged. Please login</div>

                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade add_note_modal" id="AddNoteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add new note</h5>
       
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body add_note_modal_body">
        <div>
            <label for="input">First Name</label>
            <input ref="refModalFirstName" value="" required type="text">
        </div>
        <div>
            <label for="input">Last Name</label>
            <input ref="refModalLastName" value="" type="text">
        </div>
        <div>
            <label for="input">Phone</label>
            <input ref="refModalPhone" value="" type="text">
        </div>
        <div>
            <label for="input">Observations</label>
            <input ref="refModalObservations" value="" type="text">
        </div>        
      </div>
      <div style="justify-content: space-between;" class="modal-footer">
        <div style="padding: 0;" ref="refModalMessageFromRequest" class="refMessageFromRequest">

        </div>         
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button v-on:click="clickAddButton" type="button" class="btn btn-primary">Add</button>
      </div>
    </div>
  </div>
</div>

@endsection

