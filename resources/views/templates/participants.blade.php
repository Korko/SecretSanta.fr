@section('participants')
    <fieldset>
       <legend>@yield('participants.title')</legend>
       <div class="table-responsive form-group">
           <table id="participants" class="table table-hover table-striped table-numbered">
               <thead>
                   <tr>
                       <th class="col-xs-3">@lang('form.participant.name')</th>
                       <th class="col-xs-3">@lang('form.participant.email')</th>
                       <th class="col-xs-0"></th>
                       <th class="col-xs-2 col-lg-2">@lang('form.participant.phone')</th>
                       <th class="col-xs-2">@lang('form.participant.exclusions')</th>
                       <th class="col-xs-1 col-lg-1"></th>
                   </tr>
               </thead>
               <tbody>
                   @yield('participants.tbody')
               </tbody>
           </table>
       </div>
    </fieldset>
@stop
